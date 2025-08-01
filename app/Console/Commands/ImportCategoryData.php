<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportCategoryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:categories {file=categories.xlsx}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import categories from an Excel file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists(storage_path("app/files/{$file}"))) {
            $this->error("File not found at: storage/app/files/{$file}");
            return 1;
        }

        Excel::import(new CategoryImport, "files/{$file}");
        $this->info('Categories imported successfully!');
        return 0;
    }
}
