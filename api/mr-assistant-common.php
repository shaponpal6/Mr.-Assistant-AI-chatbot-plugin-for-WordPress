<?php

/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('MrAssistantCommon')) :


    /**
     * The Mr. Assistant plugin API common class.
     *
     * All common functionality and values of API are defined here.
     *
     * @category   MrAssistantCommon
     * @package    WordPress
     * @subpackage Mr_Assistant
     * @author     Shapon pal <helpmrassistant@gmail.com>
     * @Version    1.0
     */
    class MrAssistantCommon
    {

        /**
         * This function will return post type array.
         * Get all post types except attachment.
         *
         * @return array
         */
        public static function mrPostType()
        {
            $post_types = get_post_types(array('public' => true), 'objects', 'or');
            if ($post_types) {
                if (isset($post_types['attachment'])) {
                    unset($post_types['attachment']);
                }
                return array_keys($post_types);
            }
            return array();
        }


        /**
         * Get all indexing data
         * Get all allow post types array
         * Get value of index by passing $key
         *
         * @param string $key - index name or array key
         *
         * @return array|string
         */
        public static function mrIndexingPostTypes($key = '')
        {

            $opts = get_option('__mr_assistant_indexing');
            //print_r($opts);
            if ($opts !== false && is_array($opts)) {
                if ($key !== '' && in_array($key, $opts, true)) {
                    if ($key === 'postTypes') {
                        return is_array($opts[$key]) ? $opts[$key] : array();
                    }
                    return $opts[$key];
                }
                return $opts;
            }
            return array();
        }


        /**
         * Escapes data for use in a MySQL query.
         *
         * Usually we should prepare queries using `wpdb::prepare()`.
         * Sometimes, spot-escaping is required or useful.
         * One example is preparing an array for use in an IN clause.
         *
         * Be careful in using this function correctly. It will
         * only escape values to be used in strings in the query.
         * That is, it only provides escaping for values that will
         * be within quotes in the SQL (as in field = '{$escaped_value}').
         * If your value is not going to be within quotes, your code will
         * still be vulnerable to SQL injection. For example, this is
         * vulnerable, because the escaped value is not surrounded by
         * quotes in the SQL query: ORDER BY {$escaped_value}. As such,
         * this function does not escape unquoted numeric values,
         * field names, or SQL keywords.
         *
         * @param array|string $args - user input
         *
         * @return string - sanitize string with esc_sql()
         */
        public static function prepare_sql($args)
        {
            if (is_array($args)) {
                $values = array_map(function ($v) {
                    return "'" . esc_sql($v) . "'";
                }, $args);
                return implode(',', $values);
            }
            return esc_sql($args);
        }


        /**
         * Validate User Input String and preparing query string
         *
         * @param array|string $args - user input
         *
         * @return string - sanitize string with esc_sql()
         */
        public static function prepare_like($args)
        {
            /**
             * Validate User Input String
             *
             * 1. Replacing multiple spaces with a single space.
             * \d, \w and \s
             * Shorthand character classes matching digits, word characters
             * (letters, digits, and underscores), and whitespace (spaces,
             * tabs, and line breaks). Can be used inside and
             * outside character classes.
             *
             * 2.Strip whitespace (or other characters) from the beginning
             * and end of a string
             *
             * 3. Escapes data for use in a MySQL query.
             */

            $str = trim(preg_replace('!\s+!', ' ', $args));
            $arr = explode(" ", self::prepare_sql($str));
            $values = array_map(function ($v) {
                return "%" . $v . "%";
            }, $arr);
            return implode(' ', $values);
        }


        /**
         * This function will sanitize string for
         * preparing slug.
         *
         * @param $string - slug name
         *
         * @return string
         */
        public static function make_slug($string)
        {
            return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        }


        /**
         * This function will prepare cart content for view cart action.
         *
         * @return string
         */
        public static function get_cart_content()
        {
            if (class_exists('WooCommerce')) {
                $cart = array();
                $items = WC()->cart->get_cart();
                foreach ($items as $item => $values) {
                    $_product = wc_get_product($values['data']->get_id());
                    //product image
                    $getProductDetail = wc_get_product($values['product_id']);
                    $image = $getProductDetail->get_image(); // accepts 2 arguments ( size, attr )
                    $price = get_post_meta($values['product_id'], '_price', true);
                    $regular_price = get_post_meta($values['product_id'], '_regular_price', true);
                    $sale_price = get_post_meta($values['product_id'], '_sale_price', true);
                    $att = array(
                        'title' => $_product->get_title(),
                        'quantity' => $values['quantity'],
                        'price' => $price,
                        'item_total' => wc_price($values['quantity'] * $price),
                        'regular_price' => wc_price($regular_price),
                        'sale_price' => wc_price($sale_price),
                        'image' => $image,
                    );
                    $cart[] = $att;
                }
                return json_encode(array(
                    'cart_total' => WC()->cart->get_cart_contents_count(),
                    'subtotal' => WC()->cart->get_cart_subtotal(),
                    'total' => wc_price(WC()->cart->total),
                    'shipping_cost' => WC()->cart->get_cart_shipping_total(),
                    'cart_contents' => $cart,
                    'status' => 'ok',
                ));
            }

            return json_encode(array(
                'cart_total' => 0,
                'subtotal' => 0,
                'total' => 0,
                'shipping_cost' => 0,
                'cart_contents' => 0,
                'status' => 'WooCommerce is not installed',
            ));
        }


        /**
         * Get WooCommerce cart count
         *
         * @return array
         */
        public static function get_cart_count()
        {
            if (class_exists('WooCommerce') && is_object(WC()->cart)) {
                return (array)WC()->cart->get_cart_contents_count();
            }
            return array();
        }
    }
endif;
