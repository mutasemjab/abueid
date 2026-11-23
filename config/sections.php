<?php

/*
|--------------------------------------------------------------------------
| Website sections
|--------------------------------------------------------------------------
| Every block of the storefront is described here. The dashboard builds the
| edit form of each section (and of its repeatable items) from this schema,
| and the storefront renders resources/views/front/sections/{key}.blade.php.
|
| Section options:
|   home      rendered on the home page (ordered by sort_order), otherwise a layout block
|   menu      may be linked from the header/footer menu (uses the "menu_label" field)
|   numbered  shows the running section number ("01", "02", …)
|   anchor    HTML id used for in-page scrolling
|   fields    the section's own fields
|   items     schema of the repeatable items
|
| Field options:
|   type          text | textarea | number | url | image
|   translatable  one value per language
|   required      the field must be filled (for translatable fields: in every language)
|   ltr           input is always left-to-right (numbers, phones, codes)
|   rules         extra validation rules
*/

$text = fn (bool $required = false) => ['type' => 'text', 'translatable' => true, 'required' => $required];
$textarea = fn (bool $required = false) => ['type' => 'textarea', 'translatable' => true, 'required' => $required];
$image = ['type' => 'image'];

return [

    'topbar' => [
        'icon' => 'bi-megaphone',
        'items' => [
            'fields' => [
                'text' => $text(true),
            ],
        ],
    ],

    'hero' => [
        'icon' => 'bi-stars',
        'home' => true,
        'fields' => [
            'eyebrow' => $text(),
            'title_line_1' => $text(true),
            'title_line_2' => $text(),
            'title_highlight' => $text(),
            'lead' => $textarea(),
            'primary_button' => $text(true),
            'secondary_button' => $text(),
        ],
        'items' => [
            'fields' => [
                'value' => ['type' => 'text', 'required' => true, 'ltr' => true, 'rules' => ['max:12']],
                'suffix' => $text(),
                'label' => $text(true),
            ],
        ],
    ],

    'categories' => [
        'icon' => 'bi-grid',
        'home' => true,
        'menu' => true,
        'numbered' => true,
        'anchor' => 'cats',
        'fields' => [
            'menu_label' => $text(true),
            'title' => $text(true),
            'link_label' => $text(),
            'cards_count' => ['type' => 'number', 'required' => true, 'ltr' => true, 'rules' => ['integer', 'min:1', 'max:60']],
            'more_label' => $text(true),
        ],
    ],

    'brands' => [
        'icon' => 'bi-award',
        'home' => true,
        'menu' => true,
        'numbered' => true,
        'anchor' => 'brands',
        'fields' => [
            'menu_label' => $text(true),
            'title' => $text(true),
            'lead' => $textarea(),
        ],
    ],

    'featured' => [
        'icon' => 'bi-box-seam',
        'home' => true,
        'numbered' => true,
        'fields' => [
            'title' => $text(true),
            'link_label' => $text(),
            'products_count' => ['type' => 'number', 'required' => true, 'ltr' => true, 'rules' => ['integer', 'min:1', 'max:24']],
        ],
    ],

    'about' => [
        'icon' => 'bi-info-circle',
        'home' => true,
        'menu' => true,
        'numbered' => true,
        'anchor' => 'about',
        'fields' => [
            'menu_label' => $text(true),
            'title' => $text(true),
            'lead' => $textarea(),
            'paragraph_1' => $textarea(),
            'paragraph_2' => $textarea(),
            'image_1' => $image,
            'image_2' => $image,
            'image_3' => $image,
        ],
    ],

    'why' => [
        'icon' => 'bi-patch-check',
        'home' => true,
        'numbered' => true,
        'fields' => [
            'title' => $text(true),
        ],
        'items' => [
            'fields' => [
                'image' => $image,
                'title' => $text(true),
                'description' => $textarea(),
            ],
        ],
    ],

    'branches' => [
        'icon' => 'bi-geo-alt',
        'home' => true,
        'menu' => true,
        'numbered' => true,
        'anchor' => 'branches',
        'fields' => [
            'menu_label' => $text(true),
            'title' => $text(true),
            'aside' => $text(),
        ],
    ],

    'how' => [
        'icon' => 'bi-list-ol',
        'home' => true,
        'numbered' => true,
        'fields' => [
            'title' => $text(true),
        ],
        'items' => [
            'fields' => [
                'title' => $text(true),
                'description' => $textarea(),
            ],
        ],
    ],

    'contact' => [
        'icon' => 'bi-chat-dots',
        'home' => true,
        'menu' => true,
        'numbered' => true,
        'anchor' => 'contact',
        'fields' => [
            'menu_label' => $text(true),
            'title' => $text(true),
            'lead' => $textarea(),
            'image' => $image,
            'list_label' => $text(true),
            'list_placeholder' => $text(),
            'submit_label' => $text(true),
        ],
    ],

    'product_page' => [
        'icon' => 'bi-box',
        'items' => [
            'fields' => [
                'title' => $text(true),
                'description' => $text(),
            ],
        ],
    ],

    'footer' => [
        'icon' => 'bi-layout-text-window-reverse',
        'fields' => [
            'about' => $textarea(),
            'delivery_note' => $text(),
            'copyright' => $text(true),
            'trademark_note' => $text(),
            'categories_count' => ['type' => 'number', 'required' => true, 'ltr' => true, 'rules' => ['integer', 'min:0', 'max:30']],
        ],
    ],

];
