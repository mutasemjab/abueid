<?php

/*
| Storefront interface strings (buttons, labels, messages).
| Page content (headings, paragraphs, images…) is managed from the dashboard.
| Strings are also used by assets_front/js/app.js: ":name" placeholders are replaced
| and "singular|plural" strings are chosen by the ":n" count.
*/

return [
    'switch_lang' => 'عربي',

    // Header / navigation
    'search_ph'    => 'Search bulbs, cables, breakers… or a part number',
    'search_short' => 'Search the store…',
    'search_none'  => 'No matches. Press Enter to search the store.',
    'cart'         => 'Cart',
    'home'         => 'Home',
    'nav_shop'     => 'Shop',
    'wishlist'     => 'Wishlist',

    // Home
    'items'         => 'items',
    'ask_avail'     => 'Available in-store — ask us',
    'brand_n'       => ':n product in store →|:n products in store →',
    'brand_ask'     => 'Ask us about it →',
    'since'         => 'Since',
    'stat_years'    => 'years of experience',
    'stat_branches' => 'branches',
    'stat_brands'   => 'global brands',
    'stat_products' => 'items in store',
    'main_branch'   => 'Main branch',
    'hours_l'       => 'Hours',
    'phone_l'       => 'Phone',
    'directions'    => 'Get directions',
    'bulk_ok'       => "Ready! If WhatsApp didn't open,",
    'bulk_open'     => 'tap here',
    'bulk_err'      => 'Enter your name and a valid mobile number.',

    // Shop
    'all_products'  => 'All products',
    'filter'        => 'Filter',
    'category'      => 'Category',
    'brand'         => 'Brand',
    'price'         => 'Price',
    'up_to'         => 'Up to',
    'in_stock_only' => 'In stock only',
    'fav_only'      => 'Favourites only',
    'reset'         => 'Clear filters',
    'sort_by'       => 'Sort',
    'sort_feat'     => 'Most popular',
    'sort_low'      => 'Price: low to high',
    'sort_high'     => 'Price: high to low',
    'empty_t'       => 'No parts match those filters',
    'empty_p'       => 'Try clearing filters, or ask us on WhatsApp — we can usually source it.',
    'empty_cat_t'   => ':name is coming to the online store',
    'empty_cat_p'   => 'These products are available at our branches. Message us on WhatsApp for price and availability.',
    'empty_wa_btn'  => 'Ask on WhatsApp',
    'results_n'     => ':n product|:n products',
    'favourites'    => 'Favourites',
    'search_for'    => 'Results for “:q”',

    // Product
    'add'         => 'Add to cart',
    'added'       => 'Added',
    'buy_now'     => 'Buy now',
    'in_stock'    => 'In stock',
    'low_stock'   => 'Only :n left',
    'out_stock'   => 'Out of stock',
    'added_toast' => 'Added to cart',
    'view_cart'   => 'View cart',
    'fav_added'   => 'Saved to favourites',
    'fav_removed' => 'Removed from favourites',
    'specs_t'     => 'Technical specifications',
    'sku'         => 'Part number',
    'pairs_t'     => 'Pairs with',
    'related_t'   => 'You may also need',
    'vat'         => 'Price includes tax',
    'ask_wa'      => 'Question about this part? WhatsApp',

    // Cart
    'cart_empty'    => 'Your cart is empty.',
    'shop_now'      => 'Shop now',
    'subtotal'      => 'Subtotal',
    'delivery_hint' => 'Delivery is calculated by area at the next step.',
    'go_checkout'   => 'Checkout',
    'remove'        => 'Remove',
    'ship_left'     => 'Add <b>:amount</b> more for free delivery',
    'ship_free'     => '<b>Nice!</b> Your delivery is free',

    // Checkout
    'checkout'      => 'Checkout',
    'co_sub'        => "No account needed. We'll call to confirm before dispatch.",
    'co_contact'    => 'Your details',
    'co_address'    => 'Delivery address',
    'co_pay'        => 'Payment',
    'f_name'        => 'Name',
    'f_phone'       => 'Mobile number',
    'f_address'     => 'Area, street, building number',
    'f_notes'       => 'Notes (optional)',
    'notes_ph'      => 'e.g. call before arriving',
    'err_name'      => 'Enter your name',
    'err_phone'     => '10-digit Jordanian number starting with 07',
    'err_address'   => 'We need the address to deliver',
    'pay_cod_d'     => 'Pay the courier',
    'pay_cliq_d'    => "Instant transfer — we'll send details",
    'place_order'   => 'Place order',
    'co_legal'      => "After you place the order we'll contact you to confirm availability and delivery time.",
    'order_summary' => 'Order summary',
    'delivery'      => 'Delivery',
    'total'         => 'Total',
    'free'          => 'Free',
    'co_empty'      => 'Your cart is empty — add a part first.',
    'order_failed'  => "We couldn't send your order. Please try again or contact us on WhatsApp.",
    'err_unavailable' => 'An item in your cart is no longer available. Refresh the page and try again.',
    'err_stock'     => 'Only :n of “:name” are available.',

    'payment_methods' => [
        'cod'  => 'Cash on delivery',
        'cliq' => 'CliQ',
    ],

    // Order confirmation
    'done_t'    => 'Order received — thank you!',
    'done_p'    => ":name, we'll call you during working hours to confirm your order and delivery time.",
    'order_no'  => 'Order number',
    'send_wa'   => 'Send order on WhatsApp',
    'continue'  => 'Continue shopping',
    'cliq_note' => 'Transfer the total via CliQ to alias <b>:alias</b> and add the order number in the notes.',

    // WhatsApp messages
    'wa_order'        => 'New website order',
    'wa_bulk'         => 'Bulk quote request',
    'wa_name'         => 'Name',
    'wa_phone'        => 'Mobile',
    'wa_area'         => 'Area',
    'wa_addr'         => 'Address',
    'wa_pay'          => 'Payment',
    'wa_notes'        => 'Notes',
    'wa_ask_brand'    => "Hi, I'm asking about :name",
    'wa_ask_branch'   => 'Hi, regarding :name',
    'wa_ask_category' => "Hi, I'm asking about :name:",
    'wa_ask_product'  => 'Hi, I have a question about :sku',

    // Footer
    'ftr_shop'    => 'Shop',
    'ftr_help'    => 'Help',
    'ftr_contact' => 'Contact us',
];
