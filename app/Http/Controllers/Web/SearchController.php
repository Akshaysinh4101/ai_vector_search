<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "The query field is required.",
            ]);
        }

        $query = $request->input('query');

        $client = new Client();
        $response = $client->post('https://api.cohere.ai/v1/embed', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('COHERE_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'embed-english-v3.0',
                'texts' => [$query],
                'input_type' => 'search_query',
            ],
        ]);

        $queryEmbedding = json_decode($response->getBody(), true)['embeddings'][0];

        $categories = DB::table('tbl_category_embedding as ce')
            ->join('tbl_sub_category as sc', 'sc.id', '=', 'ce.sub_category_id')
            ->join('tbl_category as c', 'c.id', '=', 'sc.category_id')
            ->select(
                'ce.id',
                'ce.service',
                'ce.keywords',
                'ce.embedding',
                'sc.name as sub_category',
                'c.name as category'
            )
            ->get();

        $results = $categories->map(function ($item) use ($queryEmbedding) {
            $itemEmbedding = json_decode($item->embedding, true);
            $score = $this->cosineSimilarity($queryEmbedding, $itemEmbedding);

            return [
                'category' => $item->category,
                'sub_category' => $item->sub_category,
                'service' => $item->service,
                'score' => $score,
            ];
        })->sortByDesc('score')->take(5)->values();

        $html = View::make('web.search_results', compact('results'))->render();

        return response()->json([
            'success' => true,
            'content' => $html,
        ]);
    }


    private function cosineSimilarity($vec1, $vec2)
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($vec1); $i++) {
            $dot += $vec1[$i] * $vec2[$i];
            $normA += pow($vec1[$i], 2);
            $normB += pow($vec2[$i], 2);
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
