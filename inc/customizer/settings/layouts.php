<?php

defined('ABSPATH') || exit;

use function Marmot\get_elementor_templates;

$add_setting_controls = array_merge($add_setting_controls,
        apply_filters('hqt/customizer/settings/layouts',
                [
                    'hq_header_elementor_template' => [
                        'default' => '',
                        'label' => _x('Header Template', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description('header'),
                        'choices' => get_elementor_templates('header', 0, 1),
                        'sanitize_callback' => '\Marmot\Customizer::sanitize_select',
                    ],
                    'hq_footer_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('Footer Template', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description('footer'),
                        'choices' => get_elementor_templates('footer', 0, 1),
                        'sanitize_callback' => '\Marmot\Customizer::sanitize_select',
                    ],
                    'hq_page_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('Page Template', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description('page'),
                        'choices' => get_elementor_templates('page'),
                        'sanitize_callback' => '\Marmot\Customizer::sanitize_select',
                    ],
                    'hq_attachment_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('Attachment Template', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description('single'),
                        'choices' => get_elementor_templates('single'),
                        'sanitize_callback' => '\Marmot\Customizer::sanitize_select',
                    ],
                    'hq_search_results_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('Seach Results Template', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description('archive'),
                        'choices' => get_elementor_templates('archive'),
                        'sanitize_callback' => '\Marmot\Customizer::sanitize_select',
                    ],
                    'hq_404_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('404 Template - Not Found', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description('page'),
                        'choices' => get_elementor_templates('page'),
                        'sanitize_callback' => '\Marmot\Customizer::sanitize_select',
                    ],
        ])
);
