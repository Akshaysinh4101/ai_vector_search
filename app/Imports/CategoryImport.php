<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\CategoryEmbedding;
use App\Models\SubCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Exception;

class CategoryImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            $texts = [];
            $filteredRows = [];
            $rowHashes = [];

            // Skip header row
            foreach ($rows as $index => $row) {
                if ($index === 0) continue;

                $category = trim($row[0] ?? '');
                $subcategory = trim($row[1] ?? '');
                $service = trim($row[2] ?? '');
                $keywords = trim($row[3] ?? '');

                $combinedText = implode(' ', array_filter([$category, $subcategory, $service, $keywords]));

                if ($combinedText !== '') {
                    $rowHash = hash('sha256', strtolower($combinedText));

                    if (!CategoryEmbedding::where('row_hash', $rowHash)->exists()) {
                        $texts[] = $combinedText;
                        $filteredRows[] = [$category, $subcategory, $service, $keywords];
                        $rowHashes[] = $rowHash;
                    }
                }
            }

            if (count($texts) === 0) {
                DB::rollBack();
                return;
            }

            // Generate embeddings via Cohere
            $client = new Client();
            $allEmbeddings = [];
            $chunks = array_chunk($texts, 96);

            foreach ($chunks as $chunk) {
                $response = $client->post('https://api.cohere.ai/v1/embed', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . env('COHERE_API_KEY'),
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => 'embed-english-v3.0',
                        'texts' => $chunk,
                        'input_type' => 'search_query',
                    ],
                ]);

                $batchEmbeddings = json_decode($response->getBody(), true)['embeddings'];
                $allEmbeddings = array_merge($allEmbeddings, $batchEmbeddings);
            }

            // Store each record
            foreach ($filteredRows as $i => $row) {
                [$category, $subcategory, $service, $keywords] = $row;

                // Save or get category
                $categoryModel = Category::firstOrCreate([
                    'name' => $category
                ]);

                // Save or get subcategory
                $subCategoryModel = SubCategory::firstOrCreate([
                    'category_id' => $categoryModel->id,
                    'name' => $subcategory,
                ]);

                // Save embedding
                CategoryEmbedding::create([
                    'sub_category_id' => $subCategoryModel->id,
                    'service' => $service,
                    'keywords' => $keywords,
                    'embedding' => json_encode($allEmbeddings[$i]),
                    'row_hash' => $rowHashes[$i],
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('CategoryImport Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
