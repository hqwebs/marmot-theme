<?php

namespace Marmot\Customizer;

defined('ABSPATH') || exit;

use function Marmot\get_elementor_templates;

/**
 * Contains settings for customizing the theme customization screen.
 * 
 * @link http://codex.wordpress.org/Theme_Customization_API
 * 
 * @since 1.0.0
 */
class Settings {

    /**
     * Instance
     * 
     * @since 1.0.0
     * 
     * @var Settings 
     */
    private static $_instance;

    /**
     * Panels for adding
     * 
     * @since 1.0.0
     * 
     * @var array
     */
    public $add_panels;

    /**
     * Panels for removing
     * 
     * @since 1.0.0
     * 
     * @var array
     */
    public $remove_panels;

    /**
     * Sections for adding
     * 
     * @since 1.0.0
     * 
     * @var array
     */
    public $add_sections;

    /**
     * Sections for removing
     * 
     * @since 1.0.0
     * 
     * @var array
     */
    public $remove_sections;

    /**
     * Settings type tansport
     * 
     * @since 1.0.0
     * 
     * @var array
     */
    public $transport_settings;

    /**
     * Controls for adding
     * 
     * @since 1.0.0
     * 
     * @var array
     */
    public $add_setting_controls;

    /**
     * Get class instance
     *
     * @since 1.0.0
     *
     * @return Settings
     */
    public static function instance() {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Marmot options definitions
     *
     * @since 1.0.0
     */
    private function __construct() {

        $HQCustomize = \Marmot\Customizer::instance();

        // Panels array
        $add_panels = [
            'hq_general_settings' => [
                'title' => _x('Marmot', 'settings', 'marmot'),
                'description' => _x('Site general options.', 'settings', 'marmot'),
                'priority' => 130,
            ],
            'hq_blog_settings' => [
                'title' => _x('Blog / News', 'settings', 'marmot'),
                'priority' => 140,
            ],
        ];
        $remove_panels = [
                //'nav_menus'
        ];

        // Sections array
        $add_sections = [
            /* General */
            'hq_theme_mod' => [
                'title' => _x('Theme MOD', 'settings', 'marmot'),
                'panel' => 'hq_general_settings',
            ],
            'hq_layouts' => [
                'title' => _x('Layouts', 'settings', 'marmot'),
                'panel' => 'hq_general_settings',
            ],
            'hq_other' => [
                'title' => _x('Other', 'settings', 'marmot'),
                'panel' => 'hq_general_settings',
            ],
            /* END General */

            /* Blog */
            'hq_blog_home' => [
                'title' => _x('Blog Page', 'settings', 'marmot'),
                'panel' => 'hq_blog_settings',
            ],
            'hq_post_archive' => [
                'title' => _x('Blog Category & Tag', 'settings', 'marmot'),
                'panel' => 'hq_blog_settings',
            ],
            'hq_post_single' => [
                'title' => _x('Single Post Page', 'settings', 'marmot'),
                'panel' => 'hq_blog_settings',
            ],
            /* END Blog */
            /* Woocommerce */
            'hq_woocommerce_general' => [
                'title' => _x('Shop Page', 'settings', 'marmot'),
                'panel' => 'woocommerce',
            ],
            'hq_product_archive' => [
                'title' => _x('Listing', 'settings', 'marmot'),
                'panel' => 'woocommerce',
            ],
            'hq_product_single' => [
                'title' => _x('Product Page', 'settings', 'marmot'),
                'panel' => 'woocommerce',
            ],
                /* END Woocommerce */
        ];
        $remove_sections = [
            'background_image',
            'colors',
        ];

        // specifies the transport for some options
        $transport_settings = [
            'blogname',
            'blogdescription',
        ];

        $add_setting_controls = [];

        do_action('hqt/customizer/settings/add_setting_controls/start');

        $settings_dir = dirname(__FILE__) . '/settings/';

        // phpcs:disable
        require $settings_dir . 'theme_mod.php';
        require $settings_dir . 'layouts.php';
        require $settings_dir . 'other.php';
        require $settings_dir . 'blog_general.php';
        require $settings_dir . 'blog_archive.php';
        require $settings_dir . 'blog_single.php';

        // WooCommerce
        if (class_exists('\WooCommerce')) {
            // WooCommerce Options
            require $settings_dir . 'woocommerce_general.php';
            require $settings_dir . 'woocommerce_archive.php';
            require $settings_dir . 'woocommerce_single.php';
        }
        // phpcs:enable

        /**
         *  Custom Post fields - PODS
         */
        $custom_pods = \Marmot\Pods::get_custom_post_types('post_type', false);
        foreach ($custom_pods as $custom_pod_key => $custom_pod) {
            // Panel
            $add_panels['hq_' . $custom_pod_key . '_settings'] = [
                'title' => $custom_pod,
                'priority' => 145,
            ];

            // Sections
            $add_sections['hq_' . $custom_pod_key . '_archive'] = [
                'title' => $custom_pod . ' ' . _x('Listing', 'settings', 'marmot'),
                'panel' => 'hq_' . $custom_pod_key . '_settings',
            ];
            $add_sections['hq_' . $custom_pod_key . '_single'] = [
                'title' => $custom_pod . ' ' . _x('Single', 'settings', 'marmot'),
                'panel' => 'hq_' . $custom_pod_key . '_settings',
            ];

            // Settings
            // Archive
            $add_setting_controls = array_merge($add_setting_controls,
                    apply_filters('hqt/customizer/settings/' . $custom_pod_key . '/archive',
                            array_merge(
                                    [
                                        'hq_' . $custom_pod_key . '_archive_layout' => [
                                            'default' => 0,
                                            'label' => $custom_pod . ' ' . _x('Archive Layout', 'settings', 'marmot'),
                                            'type' => 'select',
                                            'section' => 'hq_' . $custom_pod_key . '_archive',
                                            'choices' => get_elementor_templates('archive'),
                                        ]
                                    ],
                                    self::generate_layout_templates_controls('hq_' . $custom_pod_key . '_archive')
                            ),
                            $custom_pod_key
                    )
            );

            // Single
            $add_setting_controls = array_merge($add_setting_controls,
                    apply_filters('hqt/customizer/settings/' . $custom_pod_key . '/single',
                            array_merge(
                                    [
                                        'hq_' . $custom_pod_key . '_single_layout' => [
                                            'default' => 0,
                                            'label' => $custom_pod . ' ' . _x('Single Layout', 'settings', 'marmot'),
                                            'type' => 'select',
                                            'section' => 'hq_' . $custom_pod_key . '_single',
                                            'choices' => get_elementor_templates('single'),
                                        ]
                                    ],
                                    self::generate_layout_templates_controls('hq_' . $custom_pod_key . '_single')
                            ),
                            $custom_pod_key
                    )
            );
        }

        do_action('hqt/customizer/settings/add_setting_controls/end');

        // Panels
        $this->add_panels = apply_filters('hqt/customizer/panels/add', $add_panels);
        $this->remove_panels = apply_filters('hqt/customizer/panels/remove', $remove_panels);

        // Sections
        $this->add_sections = apply_filters('hqt/customizer/sections/add', $add_sections);
        $this->remove_sections = apply_filters('hqt/customizer/sections/remove', $remove_sections);

        // Transport Settings
        $this->transport_settings = apply_filters('hqt/customizer/transport_settings', $transport_settings);
        $this->add_setting_controls = apply_filters('hqt/customizer/setting_controls/add', $add_setting_controls);

        return $this;
    }

    /**
     * Generates configs for section
     * 
     * @since 1.0.0
     * 
     * @param string $section
     * @return array
     */
    public static function generate_layout_templates_controls($section) {
        return [
            // Layout Templates
            $section . '_templates_title' => [
                'setting_type' => null,
                'control' => 'Marmot\Customizer\Controls',
                'type' => 'sub-title',
                'section' => $section,
                'label' => _x('Layout Templates', 'settings', 'marmot'),
            ],
            $section . '_templates_link' => [
                'setting_type' => null,
                'label' => _x('Edit Templates', 'settings', 'marmot'),
                'control' => 'Marmot\Customizer\Controls',
                'type' => 'link',
                'section' => $section,
                'url' => get_admin_url(null, '/edit.php?post_type=elementor_library&tabs_group=library'),
            ],
            // Header
            $section . '_header_template' => [
                'default' => 'default',
                'label' => _x('Header Template', 'settings', 'marmot'),
                'control' => 'Marmot\Customizer\Controls',
                'type' => 'pro',
                'section' => $section,
            ],
            // Footer
            $section . '_footer_template' => [
                'default' => 'default',
                'label' => _x('Footer Template', 'settings', 'marmot'),
                'control' => 'Marmot\Customizer\Controls',
                'type' => 'pro',
                'section' => $section,
            ],
        ];
    }

    public static function full_mode_requires_description() {
        if (\Marmot\Marmot::is_full_customization_mode()) {
            return false;
        }
        /* translators: %1$s is replaced with "one <a> tag" %2$s is replaced with "close </a> tag" */
        return sprintf(_x('Applies only if %1$s Full Customizable Theme Mode%2$s is enabled.',
                        'settings',
                        'marmot'),
                '<a href="' . esc_url(admin_url('/customize.php?autofocus[control]=_hqt_theme_customizable_mode') . '" data-focus-control="_hqt_theme_customizable_mode">',
                        '</a>')
        );
    }

}
