<?php

/**
 * Marmot main functions file
 *
 * @since 1.0.0
 */

namespace Marmot;

defined('ABSPATH') || exit;

/**
 * Theme Directory Path
 *
 * @since 1.0.0
 * @var string
 */
define('THEME_DIR', trailingslashit(get_template_directory()));

/**
 * Theme URL
 *
 * @since 1.0.0
 * @var string
 */
define('THEME_URL', get_template_directory_uri());

/**
 * Theme Debug Mode
 *
 * @since 1.0.0
 * @var bool
 */
defined('MARMOT_DEBUG') || define('MARMOT_DEBUG', false);

/**
 * Theme Version
 *
 * @since 1.0.0
 * @var string
 */
const THEME_VERSION = '1.0.2';

/**
 * Theme Unique Slug
 *
 * @since 1.0.0
 * @var string
 */
const THEME_SLUG = 'marmot';

/**
 * Theme Unique Name
 *
 * @since 1.0.0
 * @var string
 */
const THEME_NAME = 'Marmot';

/**
 * Marmot main website url
 */
const THEME_SITE_URL = 'https://marmot.hqwebs.net';

/**
 * HQTheme Extra plugin file
 */
const THEME_EXTRA_PLUGIN_FILE = 'hqtheme-extra/hqtheme-extra.php';

/**
 * HQTheme Extra plugin slug
 */
const THEME_EXTRA_PLUGIN_SLUG = 'hqtheme-extra';

// Load Autoloader
require_once THEME_DIR . 'inc/autoloader.php';
Autoloader::run();

/**
 * Marmot main class
 *
 * @since 1.0.0
 */
class Marmot {

    /**
     * Instance
     * 
     * @since 1.0.0
     * 
     * @var Marmot 
     */
    private static $_instance = null;

    /**
     * Uploads dir
     *
     * @since 1.0.0
     * @var string
     */
    const UPLOADS_DIR = 'marmot/';

    /**
     * Is full customization mode available and activated
     * 
     * @since 1.0.0
     * 
     * @var boolean
     */
    private static $_theme_full_custimization_mode = null;

    /**
     *
     * @since 1.0.0
     * 
     * @var array 
     */
    private static $wp_uploads_dir = [];

    /**
     * Get class instance
     *
     * @since 1.0.0
     *
     * @return Marmot
     */
    public static function instance() {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Marmot setup.
     *
     * Sets up theme defaults and registers the various WordPress features that
     * Marmot supports.
     *
     * @since Marmot 1.0.0
     */
    private function __construct() {
        add_action('after_setup_theme', [$this, 'run']);
    }

    public function run() {

        // Check dependencies
        $dependencies = new Dependencies(THEME_NAME);
        if (!$dependencies->is_dependencies_met()) {
            return;
        }

        // Set Marmot default values
        add_action('after_switch_theme', [$this, 'after_switch_theme']);

        // Deactivation
        add_action('switch_theme', [$this, 'force_deactivation']);
        add_action('marmot_freemius_license_deactivated', [$this, 'force_deactivation']);

        add_action('wp_enqueue_scripts', [$this, 'scripts_styles']);

        $this->setup_theme();

        /**
         * Init Elementor Extras
         */
        Elementor::instance();

        /**
         * Init Theme Options
         */
        if (class_exists('\HQExtra\HQExtra')) {
            Theme_Options::instance();
        }

        /**
         * Include functionality only if polylang is active
         */
        if (class_exists('\Polylang')) {
            Polylang::instance();
        }

        /**
         * Include functionality only if Layerslider is active
         */
        if (defined('\LS_PLUGIN_VERSION')) {
            Layerslider::instance();
        }

        /**
         * Init Customizer Extras
         */
        Customizer::instance();

        if (is_admin()) {
            Admin::instance();
        }

        // Full width gutenberg
        add_action('admin_head', [$this, 'editor_full_width_gutenberg']);
    }

    /**
     * Check if theme debug mode is on
     * 
     * @since 1.0.0
     * 
     * @return bool
     */
    public static function is_debug() {
        return MARMOT_DEBUG;
    }

    /**
     * Check if full customization is possible and enabled
     * 
     * @since 1.0.0
     * 
     * @return boolean
     */
    public static function is_full_customization_mode() {
        if (null === self::$_theme_full_custimization_mode) {
            if (
            // Check if elementor is active
                    class_exists('\Elementor\Plugin') &&
                    class_exists('\HQExtra\HQExtra') &&
                    // Is onption turned on
                    'on' === \HQLib\hq_get_option('theme_customizable_mode', null, 'on', 'theme_mods')
            ) {
                self::$_theme_full_custimization_mode = true;
            } else {
                self::$_theme_full_custimization_mode = false;
            }
        }

        return self::$_theme_full_custimization_mode;
    }

    /**
     * Adds style for fullwidth gutenberg
     * 
     * @since 1.0.0
     */
    public function editor_full_width_gutenberg() {
        echo '<style>
    @media screen and ( min-width: 768px ) {
        .edit-post-visual-editor .editor-post-title,
        .edit-post-visual-editor .editor-block-list__block {
            max-width: 1600px;
        }
    }
  </style>';
    }

    /**
     * Attach hook for Theme defaults
     * 
     * @since 1.0.0
     */
    public function after_switch_theme() {
        if (current_user_can('switch_themes')) {
            add_action('elementor/init', [$this, 'set_theme_detaults'], 20); // After init customizer

            if (get_option('marmot_first_theme_activation') === false) {
                update_option('marmot_first_theme_activation', true, '', false);
                // stuff here only runs once, when the theme is activated for the 1st time
                set_theme_mod('marmot_setup', 1);
            }
        }
    }

    public function force_deactivation() {

        $premium_plugins = Admin::instance()->get_included_premium_plugins();

        $deactivated = [];
        foreach ($premium_plugins as $slug => $plugin) {
            /*
             * Only proceed forward if the parameter is set to true and plugin is active
             * as a 'normal' (not must-use) plugin.
             */
            if (defined($plugin['constant'])) {
                deactivate_plugins($plugin['init']);
                $deactivated[$plugin['init']] = time();
            }
        }

        if (!empty($deactivated)) {
            update_option('recently_activated', $deactivated + (array) get_option('recently_activated'));
        }
    }

    /**
     * Set Theme defaults
     * 
     * @since 1.0.0
     */
    public function set_theme_detaults() {
        Customizer::instance()->setDefauls(); // Set theme options defaults
    }

    /**
     * Setup Theme
     * 
     * @since 1.0.0
     */
    public function setup_theme() {
        /*
         * Makes Theme available for translation.
         * Translations can be added to the /languages/ directory.
         */
        load_theme_textdomain('marmot', THEME_DIR . '/languages');

        // Adds RSS feed links to <head> for posts and comments.
        add_theme_support('automatic-feed-links');
        // Default WP generated title support.
        add_theme_support('title-tag');

        // Adds WooCommerce support
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');

        /*
         * Switches default core markup for search form, comment form,
         * and comments to output valid HTML5.
         */
        add_theme_support('html5', [
            'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
        ]);

        /*
         * This theme supports all available post formats by default.
         * See https://wordpress.org/support/article/post-formats/
         */
        add_theme_support('post-formats', [
            'audio', 'aside', 'gallery', 'image', 'link', 'quote', 'status', 'chat', 'video',
        ]);

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(array(
            'primary' => _x('Primary Navigation', 'admin', 'marmot'),
        ));

        add_theme_support('post-thumbnails');

        if (!isset($GLOBALS['content_width'])) {
            $GLOBALS['content_width'] = apply_filters('hqt/content_width', 1200);
        }

        $defaults = [
            'post' => '500',
            'page' => '500',
            'attachment' => '650',
            'artist' => '300',
            'movie' => '400',
        ];
        add_theme_support('content-width', $defaults);

        if (!Marmot::is_full_customization_mode()) {
            // Default custom header.
            add_theme_support('custom-header');
            // Default custom backgrounds.
            add_theme_support('custom-background');
            // Default custom logo.
            add_theme_support(
                    'custom-logo',
                    array(
                        'height' => 100,
                        'width' => 350,
                        'flex-height' => true,
                        'flex-width' => true,
                    )
            );
        }

        $this->load_classes();
    }

    /**
     * Load classes
     * 
     * @since 1.0.0
     */
    private function load_classes() {
        Parts\Layout::instance();
        Parts\Blog::instance();
        if (class_exists('\WooCommerce')) {
            Parts\Woocommerce::instance();
        }
    }

    /**
     * Enqueue scripts and styles for the front end.
     *
     * @since 1.0.0
     */
    public function scripts_styles() {
        $suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
        /*
         * Adds JavaScript to pages with the comment form to support
         * sites with threaded comments (when in use).
         */
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        // Loads JavaScript file with functionality specific to Marmot.
        wp_enqueue_script(THEME_SLUG . '-script', THEME_URL . "/assets/js/functions$suffix.js", ['jquery'], THEME_VERSION, true);
    }

    public static function get_base_uploads_dir() {
        $wp_upload_dir = self::get_wp_uploads_dir();

        return $wp_upload_dir['basedir'] . '/' . self::UPLOADS_DIR;
    }

    public static function get_base_uploads_url() {
        $wp_upload_dir = self::get_wp_uploads_dir();

        return $wp_upload_dir['baseurl'] . '/' . self::UPLOADS_DIR;
    }

    private static function get_wp_uploads_dir() {
        global $blog_id;
        if (empty(self::$wp_uploads_dir[$blog_id])) {
            self::$wp_uploads_dir[$blog_id] = wp_upload_dir(null, false);
        }

        return self::$wp_uploads_dir[$blog_id];
    }

}

/**
 * Checks if plugin is installed
 *
 * @since 1.0.0
 *
 * @param string $plugin Plugin activation string
 * @return bool
 */
function is_plugin_installed($plugin) {
    require_once ABSPATH . 'wp-includes/pluggable.php';
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $plugins = \get_plugins();
    return isset($plugins[$plugin]);
}

/* Freemius - This logic will only be executed when HQExtra is active and has the Freemius SDK */

if (!function_exists('mar_fs')) {
    if (class_exists('\HQExtra\HQExtra') &&
            defined('\HQExtra\PLUGIN_PATH') &&
            file_exists(\HQExtra\PLUGIN_PATH . '/inc/freemius/start.php')
    ) {

        // Create a helper function for easy SDK access.
        function mar_fs() {
            global $mar_fs;

            if (!isset($mar_fs)) {
                // Include Freemius SDK.
                require_once \HQExtra\PLUGIN_PATH . '/inc/freemius/start.php';

                $mar_fs = fs_dynamic_init(array(
                    'id' => '7293',
                    'slug' => 'marmot',
                    'premium_slug' => 'marmot-pro',
                    'type' => 'theme',
                    'public_key' => 'pk_4b87d0363c8732c27fd5b05b90478',
                    'is_premium' => false,
                    'premium_suffix' => 'Marmot PRO',
                    // If your theme is a serviceware, set this option to false.
                    'has_premium_version' => true,
                    'has_addons' => false,
                    'has_paid_plans' => true,
                    'has_affiliation' => 'selected',
                    'menu' => array(
                        'slug' => 'marmot',
                        'contact' => false,
                        'support' => true,
                    ),
                    'navigation' => 'menu',
                ));
            }

            return $mar_fs;
        }

        // Init Freemius.
        mar_fs();
        // Signal that SDK was initiated.
        do_action('mar_fs_loaded');
    }
}

Marmot::instance();
