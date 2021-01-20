<?php

defined('ABSPATH') || exit;

use function Marmot\get_elementor_templates;

$add_setting_controls = array_merge($add_setting_controls,
        apply_filters('hqt/customizer/settings/product/single',
                array_merge(
                        [
                            'hq_product_single_layout' => [
                                'default' => 'noeltmp',
                                'label' => _x('Product Layout', 'settings', 'marmot'),
                                'type' => 'select',
                                'section' => 'hq_product_single',
                                'choices' => get_elementor_templates('single'),
                                'description' => \Marmot\Customizer\Settings::full_mode_requires_description(),
                            ],
                        ],
                        Marmot\Customizer\Settings::generate_layout_templates_controls('hq_product_single')
                ),
                'product'
        )
);
