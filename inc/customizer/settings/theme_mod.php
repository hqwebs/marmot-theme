<?php

defined('ABSPATH') || exit;

$add_setting_controls = array_merge($add_setting_controls,
        apply_filters('hqt/customizer/settings/theme_mod',
                [
                    '_hqt_theme_customizable_mode' => [
                        'default' => 'on',
                        'label' => _x('Enable Full Customizable Theme Mode?', 'settings', 'marmot'),
                        'type' => 'select',
                        'section' => 'hq_theme_mod',
                        'description' => 'Enable full customizations mode. Edit hedear, footer and content area with Elementor page bulder. This mode requires "HQTheme Extra" and "Elementor" plugins. Both are free.',
                        'choices' => [
                            '' => 'Off',
                            'on' => 'On',
                        ]
                    ],
        ])
);
