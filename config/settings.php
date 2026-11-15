<?php

/*
|--------------------------------------------------------------------------
| Site settings
|--------------------------------------------------------------------------
| Grouped settings edited from the dashboard. Field options are the same as
| in config/sections.php.
*/

return [

    'general' => [
        'icon' => 'bi-sliders',
        'fields' => [
            'site_title' => ['type' => 'text', 'translatable' => true, 'required' => true],
            'site_name' => ['type' => 'text', 'translatable' => true, 'required' => true],
            'tagline' => ['type' => 'text', 'translatable' => true],
            'meta_description' => ['type' => 'textarea', 'translatable' => true],
            'logo' => ['type' => 'image'],
            'logo_light' => ['type' => 'image'],
        ],
    ],

    'contact' => [
        'icon' => 'bi-telephone',
        'fields' => [
            'whatsapp' => ['type' => 'text', 'required' => true, 'ltr' => true, 'rules' => ['regex:/^[1-9]\d{7,14}$/']],
            'phone' => ['type' => 'text', 'ltr' => true, 'rules' => ['max:30']],
            'facebook_url' => ['type' => 'url'],
            'instagram_url' => ['type' => 'url'],
        ],
    ],

    'store' => [
        'icon' => 'bi-bag',
        'fields' => [
            'currency' => ['type' => 'text', 'translatable' => true, 'required' => true],
            'free_delivery_from' => ['type' => 'number', 'ltr' => true, 'rules' => ['min:0']],
            'cliq_alias' => ['type' => 'text', 'ltr' => true, 'rules' => ['max:50']],
            'founded_year' => ['type' => 'number', 'ltr' => true, 'rules' => ['integer', 'min:1900', 'max:2100']],
            'empty_state_image' => ['type' => 'image'],
        ],
    ],

];
