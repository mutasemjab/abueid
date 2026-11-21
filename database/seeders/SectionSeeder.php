<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->sections() as $position => $section) {
            $model = Section::query()->create([
                'key' => $section['key'],
                'content' => $section['content'] ?? [],
                'show_in_menu' => $section['menu'] ?? false,
                'sort_order' => $position + 1,
            ]);

            foreach ($section['items'] ?? [] as $index => $content) {
                $model->items()->create(['content' => $content, 'sort_order' => $index + 1]);
            }
        }
    }

    private function sections(): array
    {
        return [
            [
                'key' => 'topbar',
                'items' => [
                    ['text' => $this->t('توصيل لكافة مناطق المملكة', 'Delivery across Jordan')],
                    ['text' => $this->t('الدفع عند الاستلام أو عبر CliQ', 'Cash on delivery or CliQ')],
                    ['text' => $this->t('طلبات الجملة والمقاولين بأسعار خاصة', 'Special pricing for contractors & bulk orders')],
                ],
            ],
            [
                'key' => 'hero',
                'content' => [
                    'eyebrow' => $this->t('منتجات أصلية من وكلاء عالميين — جاهزة بالمخزن', 'Genuine products from global brands — in stock'),
                    'title_line_1' => $this->t('كل اللي بتحتاجه', 'Everything electrical'),
                    'title_line_2' => $this->t('للكهرباء والإنارة،', 'and lighting,'),
                    'title_highlight' => $this->t('بمكان واحد.', 'in one place.'),
                    'lead' => $this->t(
                        'من اللمبة للّوحة: إنارة، أسلاك وكوابل، قواطع، أباريز ومفاتيح وتحكم صناعي — بأسعار واضحة وتوصيل لكل المملكة.',
                        'From the bulb to the panel: lighting, wires & cables, breakers, sockets & switches and industrial control — clear prices, delivery across Jordan.'
                    ),
                    'primary_button' => $this->t('تسوّق الآن', 'Shop now'),
                    'secondary_button' => $this->t('أقرب فرع إلك', 'Find a branch'),
                ],
                'items' => [
                    ['value' => '35', 'suffix' => $this->t(null, null), 'label' => $this->t('أقسام رئيسية', 'main departments')],
                    ['value' => '24', 'suffix' => $this->t('س', 'h'), 'label' => $this->t('توصيل داخل عمّان', 'delivery in Amman')],
                    ['value' => '100', 'suffix' => $this->t('%', '%'), 'label' => $this->t('منتج أصلي', 'genuine parts')],
                ],
            ],
            [
                'key' => 'categories',
                'menu' => true,
                'content' => [
                    'menu_label' => $this->t('الأقسام', 'Categories'),
                    'title' => $this->t('تسوّق حسب القسم', 'Shop by category'),
                    'link_label' => $this->t('كل المنتجات', 'All products'),
                ],
            ],
            [
                'key' => 'brands',
                'menu' => true,
                'content' => [
                    'menu_label' => $this->t('وكلاؤنا', 'Our brands'),
                    'title' => $this->t('وكلاؤنا', 'Our brands'),
                    'lead' => $this->t(
                        'منتجات أصلية من أكبر الشركات العالمية في عالم الكهرباء والإنارة — مستوردة عبر القنوات الرسمية وبكفالة الوكيل.',
                        "Genuine products from the world's leading electrical and lighting manufacturers — imported through official channels with agent warranty."
                    ),
                ],
            ],
            [
                'key' => 'featured',
                'content' => [
                    'title' => $this->t('المتوفر بالمخزن', 'In stock now'),
                    'link_label' => $this->t('كل المنتجات', 'All products'),
                    'products_count' => 4,
                ],
            ],
            [
                'key' => 'about',
                'menu' => true,
                'content' => [
                    'menu_label' => $this->t('من نحن', 'About'),
                    'title' => $this->t('من نحن', 'About us'),
                    'lead' => $this->t(
                        'أبو عيد للأدوات الكهربائية والإنارة — محل أردني بيخدم الفنيين والمقاولين وأصحاب البيوت من سنين.',
                        'Abu Eid Electrical & Lighting Supplies — a Jordanian store serving technicians, contractors and homeowners for years.'
                    ),
                    'paragraph_1' => $this->t(
                        'بنوفّر كل اللي بتحتاجه اللوحة والتمديدات: قواطع، كونتاكتورات، ريليهات حماية، مؤقتات، أسلاك، أباريز وإنارة — من شركات عالمية معروفة، وبأسعار واضحة.',
                        'We stock what panels and installations need: breakers, contactors, overload relays, timers, cables, sockets and lighting — from well-known global brands, at clear prices.'
                    ),
                    'paragraph_2' => $this->t(
                        'اللي بميّزنا إنه اللي بيبيعك القطعة بيعرف شو بيبيع. اسألنا قبل ما تشتري، وبنساعدك تختار الصح من أول مرة.',
                        "What sets us apart: the people selling you the part know what they're selling. Ask before you buy and we'll help you get it right the first time."
                    ),
                    'image_1' => 'assets_front/img/lrd21.png',
                    'image_2' => 'assets_front/img/d-dial.jpg',
                    'image_3' => 'assets_front/img/min15363.png',
                ],
            ],
            [
                'key' => 'why',
                'content' => [
                    'title' => $this->t('ليش تشتري من أبو عيد؟', 'Why buy from Abu Eid?'),
                ],
                'items' => [
                    [
                        'image' => 'assets_front/img/d-qr.jpg',
                        'title' => $this->t('أصلي وموثّق', 'Genuine, traceable'),
                        'description' => $this->t('كل قطعة عليها رمز المنتج من الشركة المصنّعة — بتقدر تتحقق منها بنفسك.', "Every part carries the manufacturer's product code — check it yourself."),
                    ],
                    [
                        'image' => 'assets_front/img/d-dial.jpg',
                        'title' => $this->t('مواصفات واضحة', 'Clear specs'),
                        'description' => $this->t('القدرة، الجهد، المقاس وطريقة التركيب مكتوبة لكل منتج، بدون تخمين.', 'Wattage, voltage, size and mounting listed for every product. No guesswork.'),
                    ],
                    [
                        'image' => 'assets_front/img/d-terms.jpg',
                        'title' => $this->t('مشورة فنية', 'Technical advice'),
                        'description' => $this->t('من إنارة البيت للوحة المصنع — فريقنا بساعدك تختار الصح قبل ما تشتري.', 'From home lighting to factory panels — our team helps you choose right before you buy.'),
                    ],
                    [
                        'image' => 'assets_front/img/d-buttons.jpg',
                        'title' => $this->t('طلب بخطوتين', 'Two-step ordering'),
                        'description' => $this->t('بدون تسجيل حساب. اسمك ورقمك وعنوانك — وخلصنا.', 'No account needed. Name, number, address — done.'),
                    ],
                ],
            ],
            [
                'key' => 'branches',
                'menu' => true,
                'content' => [
                    'menu_label' => $this->t('فروعنا', 'Branches'),
                    'title' => $this->t('فروعنا', 'Our branches'),
                    'aside' => $this->t('زورنا، أو اطلب أونلاين واستلم من أقرب فرع', 'Visit us, or order online and pick up nearby'),
                ],
            ],
            [
                'key' => 'how',
                'content' => [
                    'title' => $this->t('كيف بتطلب؟', 'How ordering works'),
                ],
                'items' => [
                    [
                        'title' => $this->t('اختار القطعة', 'Pick the part'),
                        'description' => $this->t('ضيفها للسلة، أو اضغط «اشترِ الآن» لتروح للدفع مباشرة.', 'Add it to the cart, or tap “Buy now” to go straight to checkout.'),
                    ],
                    [
                        'title' => $this->t('أكّد عنوانك', 'Confirm your address'),
                        'description' => $this->t('الاسم، رقم الموبايل والمنطقة. رسوم التوصيل بتظهرلك فوراً.', 'Name, mobile and area. The delivery fee shows instantly.'),
                    ],
                    [
                        'title' => $this->t('استلم وادفع', 'Receive and pay'),
                        'description' => $this->t('نقداً عند الاستلام، أو حوّل عبر CliQ.', 'Cash on delivery, or transfer via CliQ.'),
                    ],
                ],
            ],
            [
                'key' => 'contact',
                'menu' => true,
                'content' => [
                    'menu_label' => $this->t('تواصل', 'Contact'),
                    'title' => $this->t('مقاول، كهربجي أو مشروع؟', 'Contractor, electrician or project?'),
                    'lead' => $this->t(
                        'ابعتلنا قائمة القطع أو صورة المخطط، وبنرجعلك بعرض سعر للكمية خلال ساعات العمل.',
                        "Send us your parts list or a photo of the drawing and we'll come back with a volume quote during working hours."
                    ),
                    'image' => 'assets_front/img/lc1d09-poster.jpg',
                    'list_label' => $this->t('القطع والكميات', 'Parts and quantities'),
                    'list_placeholder' => $this->t('مثال: 20 سبوت LED، 100 م كيبل 2.5 مم، 10 قواطع 16A', 'e.g. 20 LED spots, 100 m of 2.5 mm² cable, 10 × 16 A MCBs'),
                    'submit_label' => $this->t('اطلب عرض سعر عبر واتساب', 'Request a quote on WhatsApp'),
                ],
            ],
            [
                'key' => 'product_page',
                'items' => [
                    ['title' => $this->t('أصلي 100%', '100% genuine'), 'description' => $this->t('من الوكيل المعتمد', 'From the official agent')],
                    ['title' => $this->t('توصيل سريع', 'Fast delivery'), 'description' => $this->t('1–3 أيام عمل', '1–3 working days')],
                    ['title' => $this->t('دفع عند الاستلام', 'Cash on delivery'), 'description' => $this->t('أو تحويل CliQ', 'or CliQ transfer')],
                ],
            ],
            [
                'key' => 'footer',
                'content' => [
                    'about' => $this->t(
                        'أبو عيد للأدوات الكهربائية والإنارة — كل اللي بتحتاجه للكهرباء والإنارة، بمنتجات أصلية وخدمة بتعرف شو بتحكي.',
                        "Abu Eid Electrical & Lighting Supplies — everything electrical and lighting, genuine products, and people who know what they're talking about."
                    ),
                    'delivery_note' => $this->t('التوصيل: 1–3 أيام عمل', 'Delivery: 1–3 working days'),
                    'copyright' => $this->t('أبو عيد للأدوات الكهربائية والإنارة', 'Abu Eid Electrical & Lighting Supplies'),
                    'trademark_note' => $this->t('Schneider Electric علامة تجارية لمالكها.', 'Schneider Electric is a trademark of its owner.'),
                ],
            ],
        ];
    }

    private function t(?string $ar, ?string $en): array
    {
        return ['ar' => $ar, 'en' => $en];
    }
}
