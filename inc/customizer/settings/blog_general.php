<?php

use function Marmot\get_elementor_templates;

defined('ABSPATH') || exit;

$add_setting_controls = array_merge($add_setting_controls,
        apply_filters('hqt/customizer/settings/blog/home',
                array_merge(
                        [
                            'hq_blog_home_layout' => [
                                'default' => 'noeltmp',
                                'label' => _x('Blog Home Layout', 'settings', 'marmot'),
                                'type' => 'select',
                                'section' => 'hq_blog_home',
                                'choices' => get_elementor_templates('archive'),
                                'description' => \Marmot\Customizer\Settings::full_mode_requires_description(),
                            ],
                            'hq_blog_home_excerpt_length' => [
                                'default' => 55,
                                'sanitize_callback' => [$this, 'sanitize_number'],
                                'label' => _x('Blog Excerpt Length', 'settings', 'marmot'),
                                'type' => 'number',
                                'section' => 'hq_blog_home',
                            ],
                        ],
                        Marmot\Customizer\Settings::generate_layout_templates_controls('hq_blog_home')
                )
        )
);
