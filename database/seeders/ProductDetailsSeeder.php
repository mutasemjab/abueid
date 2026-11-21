<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Extra detail (English name, description, photos, technical specifications,
 * "pairs with") for imported items that have it. Items are matched by barcode (SKU).
 */
class ProductDetailsSeeder extends Seeder
{
    private const IMG = 'assets_front/img/';

    public function run(): void
    {
        $products = collect($this->details())
            ->map(fn (array $detail, string $sku) => $this->apply(Product::query()->where('sku', $sku)->first(), $detail))
            ->filter();

        $this->seedPairs($products->all());
    }

    private function apply(?Product $product, array $detail): ?Product
    {
        if (! $product) {
            return null;
        }

        $product->fill([
            'name' => ['ar' => $product->getTranslation('name', 'ar'), 'en' => $detail['en']],
            'short_description' => $detail['short'],
            'series' => $detail['series'],
            'range_min' => $detail['range'][0] ?? null,
            'range_max' => $detail['range'][1] ?? null,
        ])->save();

        $product->images()->delete();
        foreach ($detail['images'] as $index => $image) {
            $product->images()->create(['path' => self::IMG.$image, 'sort_order' => $index]);
        }

        $product->specs()->delete();
        foreach ($detail['specs'] as $index => [$label, $value]) {
            $product->specs()->create([
                'label' => $this->specLabels()[$label],
                'value' => is_array($value) ? $value : $this->t($value, $value),
                'sort_order' => $index,
            ]);
        }

        return $product;
    }

    /**
     * Relays pair with the contactor; the contactor pairs with the two smallest relays.
     *
     * @param  array<string, Product>  $products  keyed by SKU
     */
    private function seedPairs(array $products): void
    {
        $contactor = $products['LC1D09F7'] ?? null;
        $relays = collect($products)->filter(fn (Product $product) => $product->range_min !== null)->sortBy('range_min');

        if (! $contactor || $relays->isEmpty()) {
            return;
        }

        $relays->each(fn (Product $relay) => $relay->pairs()->syncWithoutDetaching([$contactor->id]));
        $contactor->pairs()->syncWithoutDetaching($relays->take(2)->pluck('id'));
    }

    /**
     * @return array<string, array> SKU => detail
     */
    private function details(): array
    {
        $relay = fn (string $code, float $min, float $max, string $extraImage) => [
            'en' => "TeSys {$code} thermal overload relay",
            'short' => $this->t("مدى ضبط {$this->num($min)} – {$this->num($max)} أمبير", "Setting range {$this->num($min)} – {$this->num($max)} A"),
            'series' => 'TeSys LRD',
            'range' => [$min, $max],
            'images' => [strtolower($code).'.png', strtolower($code).'-poster.jpg', $extraImage],
            'specs' => [
                ['range', "{$this->num($min)} – {$this->num($max)} A"],
                ['class', '10A'],
                ['aux', '1 NO + 1 NC'],
                ['reset', $this->t('يدوي / تلقائي (H / A) + زر إيقاف', 'Manual / auto (H / A) + stop button')],
                ['mount', $this->t('تحت الكونتاكتور مباشرة أو على سكة DIN', 'Direct under contactor or DIN rail')],
                ['poles', '3P'],
            ],
        ];

        return [
            'LRD12' => $relay('LRD12', 5.5, 8, 'd-terms.jpg'),
            'LRD14' => $relay('LRD14', 7, 10, 'd-buttons.jpg'),
            'LRD21' => $relay('LRD21', 12, 18, 'd-pins.jpg'),
            'LRD22' => $relay('LRD22', 16, 24, 'd-dial.jpg'),
            'LC1D09F7' => [
                'en' => 'TeSys D LC1D09 contactor — 110 V AC coil',
                'short' => $this->t('3 أقطاب · 9 أمبير AC-3 · ملف 110V AC', '3P · 9 A AC-3 · 110 V AC coil'),
                'series' => 'TeSys D',
                'images' => ['lc1d09.png', 'lc1d09-poster.jpg', 'd-qr.jpg'],
                'specs' => [
                    ['poles', '3P (3 NO)'],
                    ['util', 'AC-3'],
                    ['rated', '≤ 440 V · 9 A'],
                    ['coil', '110 V AC'],
                    ['aux', '1 NO + 1 NC'],
                    ['mount', $this->t('سكة DIN أو تثبيت ببراغي', 'DIN rail or screw mounting')],
                ],
            ],
            '15363' => [
                'en' => 'Acti9 MIN staircase timer',
                'short' => $this->t('مؤقت كهروميكانيكي · 1 – 7 دقائق', 'Electromechanical · 1 – 7 minutes'),
                'series' => 'Acti9 MIN',
                'images' => ['min15363.png', 'min15363-poster.jpg', 'd-timer.jpg'],
                'specs' => [
                    ['type', $this->t('مؤقت كهروميكانيكي', 'Electromechanical timer')],
                    ['delay', $this->t('قابل للضبط من 1 إلى 7 دقائق', 'Adjustable 1 to 7 minutes')],
                    ['voltage', '230 – 240 V AC'],
                    ['contact', '16 A'],
                    ['mode', $this->t('تلقائي / تشغيل دائم', 'Auto / permanent on')],
                    ['mount', $this->t('سكة DIN — موديول واحد', 'DIN rail — 1 module')],
                ],
            ],
        ];
    }

    private function specLabels(): array
    {
        return [
            'range' => $this->t('مدى الضبط', 'Setting range'),
            'class' => $this->t('فئة الفصل', 'Trip class'),
            'aux' => $this->t('نقاط مساعدة', 'Auxiliary contacts'),
            'reset' => $this->t('إعادة الضبط', 'Reset'),
            'mount' => $this->t('التركيب', 'Mounting'),
            'poles' => $this->t('الأقطاب', 'Poles'),
            'util' => $this->t('فئة الاستخدام', 'Utilisation category'),
            'rated' => $this->t('الجهد والتيار', 'Rated voltage / current'),
            'coil' => $this->t('جهد الملف', 'Coil voltage'),
            'type' => $this->t('النوع', 'Type'),
            'delay' => $this->t('زمن التأخير', 'Time delay'),
            'voltage' => $this->t('جهد التشغيل', 'Supply voltage'),
            'contact' => $this->t('تيار التلامس', 'Contact rating'),
            'mode' => $this->t('الوضع', 'Mode'),
        ];
    }

    private function num(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    private function t(string $ar, string $en): array
    {
        return ['ar' => $ar, 'en' => $en];
    }
}
