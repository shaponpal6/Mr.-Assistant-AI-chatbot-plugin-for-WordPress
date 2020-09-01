<?php

/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('MrAssistantPostsManager')) :
    /**
     * This is controller class of all single view of post through
     * Mr. Assistant API called.
     *
     * All single view post functionality and values of API are defined here.
     *
     * @category   MrAssistantPostsManager
     * @package    WordPress
     * @subpackage Mr_Assistant
     * @author     Shapon pal <helpmrassistant@gmail.com>
     * @Version    1.0
     */
    class MrAssistantPostsManager
    {

        /**
         * Get Product details by product id
         *
         * @param $id - product id
         *
         * @return array
         */
        public static function get_product($id)
        {
            // Check WooCommerce class exist or not.
            if (class_exists('WooCommerce')) {
                try {
                    $pf = new WC_Product_Factory();
                    $product = $pf->get_product($id);
                    if ($product && $product->post_type === "product") {
                        $data = array();
                        $data['ID'] = $id;
                        $data['post_type'] = 'product';
                        $data['product_type'] = $product->get_type();
                        $data['post_title'] = $product->get_name();
                        $data['post_content'] = $product->get_description();
                        $data['post_excerpt'] = $product->get_short_description();
                        $data['post_status'] = $product->get_status();
                        $data['price'] = $product->get_price_html();
                        $data['regular_price'] = $product->get_regular_price();
                        $data['total_sales'] = $product->get_total_sales();
                        $data['stock_status'] = $product->get_stock_status();
                        $data['sku'] = $product->get_sku();
                        $data['attributes'] = get_post_meta($id, '_product_attributes', true);
                        $data['image'] = $product->get_image();
                        $gallery = $product->get_gallery_image_ids();
                        $data['gallery'] = array();
                        if (is_array($gallery) && count($gallery) > 0) {
                            foreach ($gallery as $attachment_id) {
                                if ((int)$attachment_id > 0) {
                                    $data['gallery'][] = wp_get_attachment_url((int)$attachment_id);
                                }
                            }
                        }
                        $data['gallery_image_ids'] = $product->get_gallery_image_ids();
                        $data['tags'] = get_the_terms($id, 'product_tag');
                        $data['categories'] = get_the_terms($id, 'product_cat');
                        $data['reviews_allowed'] = $product->get_reviews_allowed();
                        $data['rating_counts'] = $product->get_rating_counts();
                        $data['average_rating'] = $product->get_average_rating();
                        $data['review_count'] = $product->get_review_count();
                        $data['rating_html'] = wc_get_rating_html($data['average_rating'], $data['review_count']);
                        return (array)$data;
                    }
                } catch (Exception $e) {
                    return array();
                }
            }
            return array();
        }


        /**
         * Get single post details by post id
         *
         * @param $id - post id
         *
         * @return array
         */
        public static function get_post($id)
        {
            if (!$id || $id < 1) {
                return array();
            }
            $post = get_post($id, ARRAY_A);
            if (!$post || !isset($post['post_status']) || $post['post_status'] !== 'publish') {
                return array();
            }
            // Get post Attachment thumbnail url by id
            $post['attachment'] = get_the_post_thumbnail_url($id);
            // Get Author name by Id
            $author_id = (int)$post['post_author'];
            if ($author_id > 0) {
                $post['post_author'] = get_the_author_meta('display_name', $author_id);
            }
            //Get post Tags
            $post['tags'] = get_the_terms($id, 'post_tag');
            //Get post Categories
            $post['categories'] = get_the_terms($id, 'category');
            //Get post gallery
            $post['gallery'] = get_post_gallery_images($id);
            return $post;
        }


        /**
         * Get custom post by post id
         *
         * @param $id - post id
         *
         * @return array
         */
        public static function get_custom($id)
        {
            $post = get_post($id, ARRAY_A);
            if ($post['post_parent'] > 0) {
                $post_type = get_post_type($post['post_parent']);
                if ($post_type === 'product') {
                    return self::get_product($post['post_parent']);
                }
                return self::get_post($post['post_parent']);
            }

            return self::get_post($post['post_parent']);
        }


        /**
         * Main Action for Api call for posts details
         *
         * @param $options - request data from API
         *
         * @return array
         */
        public static function get_posts($options)
        {
            $error = array();
            $results = array();
            try {
                $data = json_decode($options['requestData'], true);
                if ((int)$data['id'] > 0) {
                    $post_type = get_post_type($data['id']);
                    if ($post_type === 'product') {
                        $results = self::get_product($data['id']);
                    } elseif ($post_type === 'post') {
                        $results = self::get_post($data['id']);
                    } else {
                        $results = self::get_custom($data['id']);
                    }
                    $status = 'ok';
                } else {
                    $status = 'No ID Found';
                }
            } catch (Exception $e) {
                $results = array();
                $error['catch_error'] = $e;
                $status = 'Something going Wrong!!';
            }

            return array(
                'data' => $results,
                'status' => $status,
                'error' => $error
            );
        }
    }
endif;
