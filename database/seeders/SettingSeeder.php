<?php

namespace Database\Seeders;

use App\Services\SiteSettings;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(SiteSettings $settings): void
    {
        $settings->save([
            // General
            'site_title' => ['ar' => 'أبو عيد للكهرباء — أدوات كهربائية وإنارة', 'en' => 'Abu Eid — Electrical & Lighting Supplies'],
            'site_name' => ['ar' => 'أبو عيد', 'en' => 'Abu Eid'],
            'tagline' => ['ar' => 'أدوات كهربائية وإنارة', 'en' => 'Electrical & Lighting Supplies'],
            'meta_description' => [
                'ar' => 'أبو عيد للأدوات الكهربائية والإنارة — إنارة LED، أسلاك وكوابل، قواطع ولوحات، أباريز ومفاتيح وتحكم صناعي. توصيل لكافة مناطق المملكة.',
                'en' => 'Abu Eid Electrical & Lighting Supplies — LED lighting, wires & cables, breakers & boards, sockets, switches and industrial control. Delivery across Jordan.',
            ],
            'logo' => 'assets_front/img/logo-color.png',
            'logo_light' => 'assets_front/img/logo-light.png',

            // Contact
            'whatsapp' => '962790000000',
            'phone' => '079 000 0000',
            'email' => null, // set the real address from Settings › Contact
            'facebook_url' => null,
            'instagram_url' => null,

            // Store
            'currency' => ['ar' => 'د.أ', 'en' => 'JOD'],
            'free_delivery_from' => 75,
            'cliq_alias' => 'ABUEID',
            'founded_year' => 2005,
            'empty_state_image' => 'assets_front/img/min15363.png',
        ]);
    }
}
