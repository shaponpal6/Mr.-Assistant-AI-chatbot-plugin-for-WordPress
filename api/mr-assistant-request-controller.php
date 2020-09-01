<?php

/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

/**
 * Class MrAssistantRequestController
 */
if (!class_exists('MrAssistantRequestController')) :
    /**
     * This is Mr Assistant API Request controller main class.
     *
     * All request handler functionality will navigate from this class
     * according to API name.
     *
     * @category   MrAssistantRequestController
     * @package    WordPress
     * @subpackage Mr_Assistant
     * @author     Shapon pal <helpmrassistant@gmail.com>
     * @Version    1.0
     */
    class MrAssistantRequestController
    {

        /**
         * Request data of API
         *
         * @var $options
         */
        protected $options;


        /**
         * MrAssistantRequestController constructor.
         * Initialized request data of API
         *
         * @param $options - Request data of API
         */
        public function __construct($options)
        {
            $this->options = $options;
        }


        /**
         * This API Method will handle all process to get formatted data
         * for single view of post / product.
         *
         * Request will send to MrAssistantPostsManager class.
         *
         * @return string - Formatted JSON string
         */
        public function mr_get_posts()
        {
            $models = array('mr_view_post');
            $options = (array)$this->options;
            $api = isset($options['mr_api']) ? $options['mr_api'] : '';
            if (in_array($api, $models, true)) {
                $setup = MrAssistantPostsManager::get_posts($options);
            } else {
                $setup = array('status'=>'Your request is not correct!!');
            }

            return json_encode(array(
                'data' => $setup,
                'api' => $api,
                'status' => 1
            ));
        }


        /**
         * This API Method will handle all process to get formatted data
         * for WooCommerce Cart Contents.
         *
         * Request will send to MrAssistantCommon class.
         *
         * @return string - Formatted JSON string
         */
        public function mr_cart_contents()
        {
            return MrAssistantCommon::get_cart_content();
        }


        /**
         * This is the API Professor Method that will handle all
         * of master api call. It will process to get formatted data
         * for dynamically selected request type using Artificial Intelligence
         * technology thought user search in chat widget public interface.
         *
         * For more details - check `mr-assistant.js` file
         *
         * Request will send to MrAssistantRequestManager class.
         *
         * @return string - Formatted JSON string
         */
        public function mr_professor()
        {
            $models = array('posts', 'pages', 'products', 'tags', 'master', 'author');
            $api = isset($this->options['mr_api']) ? $this->options['mr_api'] : '';
            $requestData = isset($this->options['requestData']) ? $this->options['requestData'] : '';

            $cryptography = '';
            $error = array();
            $accessToken = '';
            try {
                $cryptography = (array)json_decode($requestData, true);
                $accessToken = $cryptography['accessToken'];
            } catch (Exception $e) {
                $error[] = $e->getMessage();
                echo 'Error Message: ' . $e->getMessage();
            }

            $posts = array();
            try {
                if (in_array($api, $models, true)) {
                    if (isset($cryptography['qs'])) {
                        $feature = new MrAssistantRequestManager();
                        $posts = $feature->$api($cryptography);
                    } else {
                        $posts = array();
                        $error[] = 'Invalid Request';
                    }
                }
            } catch (Exception $e) {
                $error[] = $e->getMessage();
            }

            return json_encode(array(
                'api' => $api,
                'cryptography' => $cryptography,
                'data' => $posts,
                'error' => $error,
                'accessToken' => $accessToken,
                'status' => 'ok'
            ));
        }


        /**
         * This API Method will handle all process for indexing.
         *
         * Request will send to mrAssistantIndexing class.
         *
         * @return string - Formatted JSON string
         */
        public function mr_bot_install()
        {
            $models = array('mr_set_indexing', 'mr_get_indexing', 'mr_do_indexing');
            $api = isset($this->options['mr_api']) ? $this->options['mr_api'] : '';
            if (in_array($api, $models, true)) {
                $handler = new mrAssistantIndexing();
//                $setup = $feature->$api($this->options);
                if (is_callable(array($handler, $api))) {
                    // call Mr. Assistant API action
                    $setup = call_user_func_array(array($handler, $api), array($this->options));
                } else {
                    $setup = 'Your request is not correct!!';
                }
            } else {
                $setup = 'Your request is not correct!!';
            }

            return json_encode(array(
                'data' => $setup,
                'api' => $api,
                'status' => 1
            ));
        }
    }
endif;
