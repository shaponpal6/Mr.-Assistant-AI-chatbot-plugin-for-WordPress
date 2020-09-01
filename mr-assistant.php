<?php
/**
 * Plugin Name:       Mr. Assistant
 * Plugin URI:        https://mrassistant.tech/
 * Description:       Mr. Assistant is an asynchronous, event-driven and single thread AI Chat Bot that will be able to keep live supporting your visitors like an agent as your training. You can check how many visitors are exactly browsing on your site right now and chat with them instantly when they need any help. Also, you can join in chat room when visitor is chatting with Mr. Assistant.
 * Version:           1.0
 * Author:            Shapon pal
 * Author URI:        https://www.linkedin.com/in/shapon-pal
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mr-assistant
 * Plugin Slug:       mr-assistant
 * Domain Path:       /languages/
 */


// If this file is called directly, abort.
defined('ABSPATH') || exit;

/**
 * Define the core functionality of the plugin.
 *
 * Set the plugin core functionality that can be used throughout the plugin.
 * Load the dependencies, define the locale, and set the hooks for the admin area and
 * the public-facing side of the site.
 */


// Define the Mr. Assistant Plugin file name
if (!defined('MR_ASSISTANT_PLUGIN_FILE')) {
    define('MR_ASSISTANT_PLUGIN_FILE', __FILE__);
}


//Define the Mr. Assistant Plugin base name
if (!defined('MR_ASSISTANT_BASE_NAME')) {
    define('MR_ASSISTANT_BASE_NAME', plugin_basename(__FILE__));
}


//Define the Mr. Assistant Plugin file directory url
if (!defined('MR_ASSISTANT_BASE_URL')) {
    define('MR_ASSISTANT_BASE_URL', trailingslashit(plugin_dir_url(__FILE__)));
}


//Define the Mr. Assistant Plugin file directory path
if (!defined('MR_ASSISTANT_BASE_PATH')) {
    define('MR_ASSISTANT_BASE_PATH', trailingslashit(plugin_dir_path(__FILE__)));
}


//Define the Mr. Assistant Plugin access token
if (!defined('MR_ASSISTANT_ACCESS_TOKEN')) {
    define('MR_ASSISTANT_ACCESS_TOKEN', uniqid('mrAssistant-', false));
}


//Define the Mr. Assistant Plugin name
if (!defined('MR_ASSISTANT_NAME')) {
    define('MR_ASSISTANT_NAME', 'Mr. Assistant');
}


//Define the Mr. Assistant Plugin Slug
if (!defined('MR_ASSISTANT_SLUG')) {
    define('MR_ASSISTANT_SLUG', 'mr-assistant');
}


//Define the Mr. Assistant Plugin Version
if (!defined('MR_ASSISTANT_VERSION')) {
    define('MR_ASSISTANT_VERSION', 1.0);
}


/**
 * This function will execute on plugin page.
 * Set extra action link for plugin
 */
if (!function_exists('Func_Mr_Actions_link')) {
    /**
     * Here you can see added a action link to setting page.
     * This static link will go to setting page.
     *
     * @param $links - $links an array of links related to the plugin.
     *
     * @return array - updated array of links related to the plugin.
     */
    function Func_Mr_Actions_link($links)
    {
        $mr_link = admin_url('admin.php?page=mr-assistant-setting');
        $mr_link_name = __('Setting', 'mr-assistant');
        $setting = '<a href="'.$mr_link.'" target="_blank">' .$mr_link_name. '</a>';
        array_unshift($links, $setting);
        return $links;
    }
}
add_filter('plugin_action_links_' . MR_ASSISTANT_BASE_NAME, 'Func_Mr_Actions_link');


/**
 * Mr. Assistant plugin update checker.
 * Allow update checker on following those pages.
 */
if (is_admin()) {
    global $pagenow;
    // Plugin Update check on those pages
    $check_pages = array(
        'update-core.php',
        'plugins.php',
        'admin-ajax.php',
        'update.php',
        'plugin-install.php'
    );

    if (!empty($pagenow) && in_array($pagenow, $check_pages, true)) {
        include MR_ASSISTANT_BASE_PATH. 'libs/updater/plugin-update-checker.php';
        if (class_exists('Puc_v4_Factory', false)) {
            $mrServer = 'https://support.mrassistant.tech/update-server';
            $update_checker = Puc_v4_Factory::buildUpdateChecker(
                $mrServer.'?action=get_metadata&slug=' . MR_ASSISTANT_SLUG,
                __FILE__
            );
        }
    }
}


/**
 * Uses this function in order to set the domain and to register the
 * hook for internationalization.
 */
if (!function_exists('Mr_Assistant_locale')) {
    /**
     * Define the locale for this plugin for internationalization.
     * Load the plugin text domain for translation.
     *
     * @return bool
     */
    function Mr_Assistant_locale()
    {
        $mrLocale = apply_filters('plugin_locale', get_locale(), 'mr-assistant');
        $mrLangDir = dirname(MR_ASSISTANT_BASE_NAME) . '/languages/';
        $mrBaseLangDir = trailingslashit(WP_LANG_DIR) . 'mr-assistant' . '/';
        $wpLangDir = $mrBaseLangDir. 'mr-assistant' . '-' . $mrLocale . '.mo';

        if ($loaded = load_textdomain('mr-assistant', $wpLangDir)) {
            return $loaded;
        }

        load_plugin_textdomain('mr-assistant', false, $mrLangDir);
        return true;
    }
}
add_action('plugin_loaded', 'mr_assistant_locale');


/**
 * Don't add any functionality under this line.
 */


/**
 * Begins execution of the Mr. Assistant plugin.
 *
 * Include the main Mr. Assistant class.
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
if (!class_exists('MrAssistant', false)) {
    include_once __DIR__ . '/core/class-mr-assistant.php';
    if (!function_exists('Mr_Assistant_execute')) {
        /**
         * Set the Mr. Assistant plugin core functionality that can be
         * used throughout the plugin.
         *
         * @return MrAssistant|null
         */
        function Mr_Assistant_execute()
        {
            if (class_exists('MrAssistant')) {
                return MrAssistant::instance();
            }
            return null;
        }
        add_action('init', 'Mr_Assistant_execute');
    }
}
