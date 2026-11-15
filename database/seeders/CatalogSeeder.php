<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Initial catalogue. Prices and stock are placeholders — update them from the dashboard.
 */
class CatalogSeeder extends Seeder
{
    private const IMG = 'assets_front/img/';

    public function run(): void
    {
        $this->seedCategories();
        $this->seedBrands();
        $this->seedProducts();
        $this->seedPairs();
    }

    private function seedCategories(): void
    {
        $categories = [
            'lighting' => [
                'name' => $this->t('الإنارة', 'Lighting'),
                'blurb' => $this->t('لمبات LED، سبوتات، بانلات وبروجكتورات', 'LED bulbs, spots, panels & floodlights'),
                'subs' => [['لمبات LED', 'سبوتات', 'بانلات', 'بروجكتورات', 'شرائط LED', 'إنارة خارجية'], ['LED bulbs', 'Spotlights', 'Panels', 'Floodlights', 'LED strips', 'Outdoor']],
            ],
            'cables' => [
                'name' => $this->t('الأسلاك والكوابل', 'Wires & cables'),
                'blurb' => $this->t('أسلاك نحاس، كوابل مسلّحة ومرنة', 'Copper wire, armoured & flexible cable'),
                'subs' => [['أسلاك نحاس', 'كوابل مسلّحة', 'كوابل مرنة', 'كوابل شبكات وكاميرات'], ['Copper wire', 'Armoured', 'Flexible', 'Data & CCTV']],
            ],
            'breakers' => [
                'name' => $this->t('القواطع واللوحات', 'Breakers & boards'),
                'blurb' => $this->t('قواطع MCB وتسرّب أرضي ولوحات توزيع', 'MCBs, RCCBs and distribution boards'),
                'subs' => [['قواطع MCB', 'قواطع تسرّب RCCB', 'MCCB', 'لوحات توزيع'], ['MCB', 'RCCB', 'MCCB', 'Distribution boards']],
            ],
            'wiring' => [
                'name' => $this->t('الأباريز والمفاتيح', 'Sockets & switches'),
                'blurb' => $this->t('مفاتيح إنارة، أباريز، ديمرات وأباريز صناعية', 'Switches, sockets, dimmers, industrial sockets'),
                'subs' => [['مفاتيح', 'أباريز', 'ديمر', 'أباريز صناعية'], ['Switches', 'Sockets', 'Dimmers', 'Industrial']],
            ],
            'control' => [
                'name' => $this->t('التحكم الصناعي', 'Industrial control'),
                'blurb' => $this->t('ريليهات حماية، كونتاكتورات ومؤقتات', 'Overload relays, contactors & timers'),
                'subs' => [['ريليهات حماية', 'كونتاكتورات', 'مؤقتات', 'أزرار وإشارات'], ['Overload relays', 'Contactors', 'Timers', 'Pushbuttons & pilots']],
            ],
            'accessories' => [
                'name' => $this->t('عدد وإكسسوارات', 'Tools & accessories'),
                'blurb' => $this->t('مواسير، علب، شريط عازل وفاحصات', 'Conduit, boxes, tape & testers'),
                'subs' => [['مواسير وعلب', 'شريط عازل', 'وصلات وأفياش', 'فاحصات وعدد'], ['Conduit & boxes', 'Insulating tape', 'Plugs & extensions', 'Testers & tools']],
            ],
        ];

        $position = 0;
        foreach ($categories as $slug => $category) {
            Category::query()->create([
                'slug' => $slug,
                'name' => $category['name'],
                'blurb' => $category['blurb'],
                'subcategories' => $this->t(implode("\n", $category['subs'][0]), implode("\n", $category['subs'][1])),
                'sort_order' => ++$position,
            ]);
        }
    }

    private function seedBrands(): void
    {
        $brands = [
            'schneider' => ['Schneider Electric', $this->t('فرنسا', 'France'), $this->t('تحكم وحماية المحركات، قواطع، مؤقتات', 'Motor control, breakers, timers')],
            'abb' => ['ABB', $this->t('سويسرا', 'Switzerland'), $this->t('قواطع ومفاتيح وتجهيزات لوحات', 'Breakers, switches, panel gear')],
            'legrand' => ['Legrand', $this->t('فرنسا', 'France'), $this->t('أباريز ومفاتيح وتمديدات', 'Sockets, switches, wiring devices')],
            'siemens' => ['Siemens', $this->t('ألمانيا', 'Germany'), $this->t('تحكم صناعي وحماية', 'Industrial control & protection')],
            'hager' => ['Hager', $this->t('ألمانيا', 'Germany'), $this->t('لوحات توزيع وقواطع منزلية', 'Distribution boards, MCBs')],
            'philips' => ['Philips Lighting', $this->t('هولندا', 'Netherlands'), $this->t('إنارة LED داخلية وخارجية', 'Indoor & outdoor LED lighting')],
        ];

        $position = 0;
        foreach ($brands as $slug => [$name, $origin, $line]) {
            Brand::query()->create([
                'slug' => $slug,
                'name' => $name,
                'origin' => $origin,
                'product_line' => $line,
                'sort_order' => ++$position,
            ]);
        }
    }

    private function seedProducts(): void
    {
        $control = Category::query()->where('slug', 'control')->value('id');
        $schneider = Brand::query()->where('slug', 'schneider')->value('id');

        $relayReset = $this->t('يدوي / تلقائي (H / A) + زر إيقاف', 'Manual / auto (H / A) + stop button');
        $relayMount = $this->t('تحت الكونتاكتور مباشرة أو على سكة DIN', 'Direct under contactor or DIN rail');
        $relay = fn (string $code, float $min, float $max, float $price, int $stock, string $extraImage) => [
            'slug' => strtolower($code),
            'sku' => $code,
            'subcategory' => $this->t('ريليهات حماية', 'Overload relays'),
            'series' => 'TeSys LRD',
            'name' => $this->t("ريليه حماية حراري {$code}", "TeSys {$code} thermal overload relay"),
            'short' => $this->t("مدى ضبط {$this->num($min)} – {$this->num($max)} أمبير", "Setting range {$this->num($min)} – {$this->num($max)} A"),
            'price' => $price,
            'stock' => $stock,
            'range' => [$min, $max],
            'images' => [strtolower($code).'.png', strtolower($code).'-poster.jpg', $extraImage],
            'specs' => [
                ['range', "{$this->num($min)} – {$this->num($max)} A"],
                ['class', '10A'],
                ['aux', '1 NO + 1 NC'],
                ['reset', $relayReset],
                ['mount', $relayMount],
                ['poles', '3P'],
            ],
        ];

        $products = [
            $relay('LRD12', 5.5, 8, 19.5, 14, 'd-terms.jpg'),
            $relay('LRD14', 7, 10, 19.5, 22, 'd-buttons.jpg'),
            $relay('LRD21', 12, 18, 21, 9, 'd-pins.jpg'),
            $relay('LRD22', 16, 24, 23.5, 3, 'd-dial.jpg'),
            [
                'slug' => 'lc1d09',
                'sku' => 'LC1D09F7',
                'subcategory' => $this->t('كونتاكتورات', 'Contactors'),
                'series' => 'TeSys D',
                'name' => $this->t('كونتاكتور TeSys D ‏LC1D09 — ملف 110 فولت', 'TeSys D LC1D09 contactor — 110 V AC coil'),
                'short' => $this->t('3 أقطاب · 9 أمبير AC-3 · ملف 110V AC', '3P · 9 A AC-3 · 110 V AC coil'),
                'price' => 22,
                'stock' => 18,
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
            [
                'slug' => 'min15363',
                'sku' => '15363',
                'subcategory' => $this->t('مؤقتات', 'Timers'),
                'series' => 'Acti9 MIN',
                'name' => $this->t('مؤقت درج Acti9 MIN', 'Acti9 MIN staircase timer'),
                'short' => $this->t('مؤقت كهروميكانيكي · 1 – 7 دقائق', 'Electromechanical · 1 – 7 minutes'),
                'price' => 21,
                'stock' => 11,
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

        foreach ($products as $position => $data) {
            $product = Product::query()->create([
                'slug' => $data['slug'],
                'sku' => $data['sku'],
                'category_id' => $control,
                'brand_id' => $schneider,
                'subcategory' => $data['subcategory'],
                'series' => $data['series'],
                'name' => $data['name'],
                'short_description' => $data['short'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'range_min' => $data['range'][0] ?? null,
                'range_max' => $data['range'][1] ?? null,
                'sort_order' => $position + 1,
            ]);

            foreach ($data['images'] as $index => $image) {
                $product->images()->create(['path' => self::IMG.$image, 'sort_order' => $index]);
            }

            foreach ($data['specs'] as $index => [$label, $value]) {
                $product->specs()->create([
                    'label' => $this->specLabels()[$label],
                    'value' => is_array($value) ? $value : $this->t($value, $value),
                    'sort_order' => $index,
                ]);
            }
        }
    }

    /**
     * Relays pair with the contactor; the contactor pairs with the two smallest relays.
     */
    private function seedPairs(): void
    {
        $contactor = Product::query()->where('slug', 'lc1d09')->firstOrFail();
        $relays = Product::query()->whereNotNull('range_min')->orderBy('range_min')->get();

        $relays->each(fn (Product $relay) => $relay->pairs()->attach($contactor));
        $contactor->pairs()->attach($relays->take(2));
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
