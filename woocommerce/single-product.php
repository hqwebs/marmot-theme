<?php

/**
 * Render Product
 */
defined('ABSPATH') || exit;

use function Marmot\display_elementor_template;

if (\Marmot\Marmot::is_full_customization_mode()) {

    get_header('shop');

    $tmp = \HQLib\get_post_meta(null, 'woocommerce_product_template');
    if (!empty($tmp) && $tmp != 'default') {
        display_elementor_template($tmp);
    } else {
        $tmp = get_theme_mod('hq_product_single_layout');
        if ($tmp) {
            display_elementor_template($tmp);
        } else {
            the_content();
        }
    }

    get_footer('shop');
} else { // No templates version
    wc_get_template('single-product.php', null, WC()->plugin_path() . '/templates/');
}