<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Converts the shop's item export (Excel) into database/seeders/data/products.csv,
 * which CatalogSeeder imports. Re-run it whenever the Excel file changes:
 *
 *   php artisan catalog:convert "C:\Users\me\Downloads\ABUEID.xls"
 *   php artisan db:seed --class=CatalogSeeder
 *
 * Expected columns (by position): item code, barcode, name, unit, sale price,
 * tax rate, group, sub-group.
 */
class ConvertCatalogExcel extends Command
{
    public const CSV_PATH = 'seeders/data/products.csv';

    public const HEADER = ['code', 'barcode', 'name', 'unit', 'price', 'tax_rate', 'group', 'subgroup'];

    protected $signature = 'catalog:convert {file : Path to the .xls/.xlsx item export}';

    protected $description = 'Convert the item Excel export into the catalogue CSV used by the seeder';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (! is_file($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        ini_set('memory_limit', '1G');

        $reader = IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $rows = $reader->load($file)->getActiveSheet()->toArray(null, false, false, false);
        array_shift($rows); // header row

        $target = database_path(self::CSV_PATH);
        @mkdir(dirname($target), 0755, true);
        $out = fopen($target, 'wb');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel opens the CSV correctly
        fputcsv($out, self::HEADER);

        $written = 0;
        foreach ($rows as $row) {
            $row = array_map(fn ($cell) => trim(preg_replace('/\s+/u', ' ', (string) $cell)), array_pad($row, 8, ''));

            if ($row[0] === '' || $row[2] === '') {
                continue;
            }

            $row[4] = is_numeric($row[4]) ? (string) round((float) $row[4], 3) : '0';
            $row[5] = is_numeric($row[5]) ? (string) (float) $row[5] : '0';

            fputcsv($out, $row);
            $written++;
        }

        fclose($out);
        $this->info("Wrote {$written} items to database/".self::CSV_PATH);

        return self::SUCCESS;
    }
}
