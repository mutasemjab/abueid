<?php

namespace Database\Seeders;

use App\Console\Commands\ConvertCatalogExcel;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Imports the shop's full item list from database/seeders/data/products.csv
 * (generated from the Excel export with `php artisan catalog:convert <file>`).
 *
 *  - every Excel "group" becomes a category, every "sub-group" the item's sub-category
 *  - groups named after a manufacturer also become a brand linked to their items
 *  - items without a sale price are imported hidden (they cannot be ordered at 0)
 *  - stock is not tracked (NULL) because the export has no quantities
 */
class CatalogSeeder extends Seeder
{
    private const CHUNK = 500;

    /**
     * Excel group => [slug, English name, brand name (null when the group is not a manufacturer)].
     * Arabic names stay exactly as in the export; edit any of them later from the dashboard.
     */
    private const GROUPS = [
        'Schneider' => ['schneider', 'Schneider Electric', 'Schneider Electric'],
        'VIMAR' => ['vimar', 'VIMAR', 'VIMAR'],
        'الكابلات الحديثة' => ['modern-cables', 'Modern Cables', null],
        'LEENO' => ['leeno', 'LEENO', 'LEENO'],
        'ثريه' => ['chandeliers', 'Chandeliers', null],
        'LED' => ['led', 'LED lighting', null],
        'شراك' => ['sharak', 'Sharak', null],
        'بلاستيك' => ['plastic', 'Plastic & conduit', null],
        'ABB' => ['abb', 'ABB', 'ABB'],
        'LOVATO' => ['lovato', 'LOVATO Electric', 'LOVATO Electric'],
        'دمج' => ['damj', 'Damj', null],
        'نحاس' => ['copper', 'Copper', null],
        'Radium' => ['radium', 'Radium', 'Radium'],
        'خزانه' => ['cabinets', 'Cabinets', null],
        'PHILIPS' => ['philips', 'Philips', 'Philips'],
        'لمبات' => ['lamps', 'Lamps', null],
        'M3' => ['m3', 'M3', 'M3'],
        'Fumagalli' => ['fumagalli', 'Fumagalli', 'Fumagalli'],
        'راسيات' => ['rasiyat', 'Rasiyat', null],
        'اتك' => ['atek', 'Atek', null],
        'ELvox' => ['elvox', 'Elvox', 'Elvox'],
        'EVO-TECH' => ['evo-tech', 'EVO-TECH', 'EVO-TECH'],
        'TENBY' => ['tenby', 'TENBY', 'TENBY'],
        'انتركم' => ['intercom', 'Intercom', null],
        'شفاطات' => ['exhaust-fans', 'Exhaust fans', null],
        'W.H' => ['westinghouse', 'Westinghouse', 'Westinghouse'],
        'قلاند' => ['qaland', 'Qaland', null],
        'بنسر' => ['benser', 'Benser', null],
        'FAREX تركي' => ['farex', 'FAREX (Turkey)', 'FAREX'],
        'فوتينا' => ['fotina', 'Fotina', null],
        'محول 110/220' => ['transformers', '110/220 V transformers', null],
        'كيبل ترى' => ['cable-tray', 'Cable tray', null],
        'ليندر' => ['linder', 'Linder', null],
        'الدسوقي' => ['al-desouki', 'Al-Desouki', null],
        'متفرقات' => ['miscellaneous', 'Miscellaneous', null],
    ];

    /** Excel unit => [Arabic, English]. Units missing here are kept as-is; "per piece" units map to NULL. */
    private const UNITS = [
        'حبة' => null,
        'حبه' => null,
        'NO.' => null,
        'متر' => ['متر', 'm'],
        'ربطة' => ['ربطة', 'bundle'],
        'بكيت' => ['بكيت', 'pack'],
        'رول' => ['رول', 'roll'],
        'كيلو' => ['كيلو', 'kg'],
        'درم 305' => ['درم 305 م', '305 m drum'],
        'درم 400' => ['درم 400 م', '400 m drum'],
        'درم 300 يارد' => ['درم 300 يارد', '300 yd drum'],
    ];

    public function run(): void
    {
        $items = $this->readCsv();

        DB::transaction(function () use ($items) {
            $categories = $this->seedCategories($items);
            $brands = $this->seedBrands();
            $this->seedProducts($items, $categories, $brands);
        });

        $this->call(ProductDetailsSeeder::class);

        $this->command?->info(sprintf(
            'Catalogue: %d products (%d visible), %d categories, %d brands.',
            Product::query()->count(),
            Product::query()->where('is_active', true)->count(),
            Category::query()->count(),
            Brand::query()->count(),
        ));
    }

    /**
     * @return Collection<int, array<string, string>>
     */
    private function readCsv(): Collection
    {
        $path = database_path(ConvertCatalogExcel::CSV_PATH);

        if (! is_file($path)) {
            throw new RuntimeException("Catalogue file missing: {$path}. Run `php artisan catalog:convert <excel file>` first.");
        }

        $handle = fopen($path, 'rb');
        fgets($handle); // header (with BOM)

        $items = collect();
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count(ConvertCatalogExcel::HEADER)) {
                $items->push(array_combine(ConvertCatalogExcel::HEADER, $row));
            }
        }
        fclose($handle);

        return $items;
    }

    /**
     * @return array<string, int> Excel group => category id
     */
    private function seedCategories(Collection $items): array
    {
        $byGroup = $items->groupBy('group');
        $ordered = $byGroup->sortByDesc(fn (Collection $rows, string $group) => $group === 'متفرقات' ? -1 : $rows->count());

        $ids = [];
        $position = 0;
        foreach ($ordered as $group => $rows) {
            [$slug, $english] = self::GROUPS[$group] ?? [str($group)->slug()->value() ?: 'group-'.($position + 1), $group];
            $subgroups = $rows->pluck('subgroup')->filter()->countBy()->sortDesc()->keys()->implode("\n");

            $ids[$group] = Category::query()->create([
                'slug' => $slug,
                'name' => ['ar' => $group, 'en' => $english],
                'subcategories' => ['ar' => $subgroups, 'en' => $subgroups],
                'sort_order' => ++$position,
            ])->id;
        }

        return $ids;
    }

    /**
     * @return array<string, int> Excel group => brand id
     */
    private function seedBrands(): array
    {
        $ids = [];
        $position = 0;
        foreach (self::GROUPS as $group => [$slug, , $brand]) {
            if ($brand !== null) {
                $ids[$group] = Brand::query()->create(['slug' => $slug, 'name' => $brand, 'sort_order' => ++$position])->id;
            }
        }

        return $ids;
    }

    private function seedProducts(Collection $items, array $categories, array $brands): void
    {
        $brandNames = Brand::query()->pluck('name', 'id');
        $usedSlugs = [];
        $now = now();

        $items->chunk(self::CHUNK)->each(function (Collection $chunk) use ($categories, $brands, $brandNames, &$usedSlugs, $now) {
            $rows = $chunk->map(function (array $item) use ($categories, $brands, $brandNames, &$usedSlugs, $now) {
                $slug = $this->uniqueSlug($item['code'], $usedSlugs);
                $brandId = $brands[$item['group']] ?? null;
                $price = (float) $item['price'];
                $sku = $item['barcode'] !== '' ? $item['barcode'] : $item['code'];

                return [
                    'slug' => $slug,
                    'sku' => $sku,
                    'category_id' => $categories[$item['group']],
                    'brand_id' => $brandId,
                    'subcategory' => $this->json($item['subgroup'] ?: null),
                    'name' => $this->json($item['name']),
                    'unit' => $this->unit($item['unit']),
                    'price' => $price,
                    'tax_rate' => (float) $item['tax_rate'],
                    'stock' => null,
                    'sort_order' => 0,
                    'is_active' => $price > 0,
                    'search_text' => Product::searchText([$sku, $slug, $brandNames[$brandId] ?? null, $item['name']]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            });

            Product::query()->insert($rows->values()->all());
        });
    }

    private function uniqueSlug(string $code, array &$used): string
    {
        $slug = $code;
        for ($n = 2; isset($used[$slug]); $n++) {
            $slug = "{$code}-{$n}";
        }

        return $used[$slug] = $slug;
    }

    private function unit(string $unit): ?string
    {
        $translation = array_key_exists($unit, self::UNITS) ? self::UNITS[$unit] : [$unit, $unit];

        return $translation ? $this->json($translation[0], $translation[1]) : null;
    }

    /**
     * Translatable value for a bulk insert (the export only has Arabic text, used for both languages).
     */
    private function json(?string $ar, ?string $en = null): ?string
    {
        return $ar === null || $ar === ''
            ? null
            : json_encode(['ar' => $ar, 'en' => $en ?? $ar], JSON_UNESCAPED_UNICODE);
    }
}
