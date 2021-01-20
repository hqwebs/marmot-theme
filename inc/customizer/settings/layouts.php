<?php

defined('ABSPATH') || exit;

use function Marmot\get_elementor_templates;

$add_setting_controls = array_merge($add_setting_controls,
        apply_filters('hqt/customizer/settings/layouts',
                [
                    'hq_header_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('Header Template', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description(),
                        'choices' => get_elementor_templates('header'),
                    ],
                    'hq_footer_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('Footer Template', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description(),
                        'choices' => get_elementor_templates('footer'),
                    ],
                    'hq_page_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('Page Template', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description(),
                        'choices' => get_elementor_templates('page'),
                    ],
                    'hq_attachment_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('Attachment Template', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description(),
                        'choices' => get_elementor_templates('single'),
                    ],
                    'hq_search_results_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('Seach Results Template', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description(),
                        'choices' => get_elementor_templates('archive'),
                    ],
                    'hq_404_elementor_template' => [
                        'default' => 'noeltmp',
                        'label' => _x('404 Template - Not Found', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_layouts',
                        'description' => \Marmot\Customizer\Settings::full_mode_requires_description(),
                        'choices' => get_elementor_templates('page'),
                    ],
        ])
);
