<?php
/**
 * Mr. Assistant - WordPress Ajax api Process Execution
 * Minimum php version 5.6
 *
 * @category   Core
 * @package    WordPress
 * @subpackage Administration
 * @author     Shapon pal <helpmrassistant@gmail.com>
 * @Version    1.0
 *
 * @link https://codex.wordpress.org/AJAX_in_Plugins
 */

define('DOING_AJAX', true);


//Load WordPress Bootstrap
require_once '../../../wp-load.php';

send_nosniff_header();
nocache_headers();
cache_javascript_headers();


$mr_content = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';

if ($mr_content === 'application/json') {
    //Receive the RAW post data.
    $content = trim(file_get_contents('php://input'));

    $decoded = json_decode($content, true);
    if ((bool) is_array($decoded)) {
        $mr_ac = isset($decoded['action']) ? esc_attr(trim($decoded['action'])) : '';
        if (!empty($mr_ac) && strpos($mr_ac, 'mr_') === 0) {
            if (class_exists('MrAssistantRequestController')) {
                $handler = new MrAssistantRequestController($decoded); // phpcs:ignore
                if (isset($mr_ac) && is_callable(array($handler, $mr_ac))) {
                    // call Mr. Assistant API action
                    echo call_user_func(array($handler, $mr_ac));
                } else {
                    wp_die('{"status":"Action not Match"}');
                }
            } else {
                wp_die('{"status":"Something was wrong. Please try again." }');
            }
        } else {
            wp_die('{"status":"Action not valid" }');
        }
    } else {
        wp_die('{"status":"Bad Request" }');
    }
} else {
    wp_die('{"status":"Request not accept" }');
}



