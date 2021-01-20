<?php

/**
 * Render Shop Archive
 */
defined('ABSPATH') || exit;

use function Marmot\display_elementor_template;

if (\Marmot\Marmot::is_full_customization_mode()) {

    get_header('shop');

    $tmp = 'noeltmp';
    $templateLoaded = 0;
    if (is_shop()) { // Load shop home
        $rawtpl = get_theme_mod('hq_woocommerce_general_list_layout');
        if (!empty($rawtpl) && $rawtpl != 'default') {
            $tmp = $rawtpl;
            $templateLoaded = 1;
        }
    } elseif (is_product_category() || is_product_tag()) { // By taxonomy
        global $wp_query;
        $wterm = $wp_query->get_queried_object();
        $rawtpl = \HQLib\get_term_meta($wterm->term_id, 'archive_template');
        if (!empty($rawtpl) && $rawtpl != 'default') {
            $tmp = $rawtpl;
            $templateLoaded = 1;
        }
    }

    if (!$templateLoaded) { // Load archive if other are empty
        $rawtpl = get_theme_mod('hq_product_archive_layout');
        if (!empty($rawtpl) && $rawtpl != 'default') {
            $tmp = $rawtpl;
        }
    }

    if ($tmp != 'noeltmp') {
        display_elementor_template($tmp);
    }

    get_footer('shop');
} else { // No templates version
    wc_get_template('archive-product.php', null, WC()->plugin_path() . '/templates/');
}