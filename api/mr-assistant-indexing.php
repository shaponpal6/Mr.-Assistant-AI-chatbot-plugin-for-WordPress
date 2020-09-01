<?php

/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");


if (!class_exists('mrAssistantIndexing')) :

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
    class mrAssistantIndexing
    {

        /**
         * Set Access token
         *
         * @var string
         */
        private $access_token = 'MR_ASSISTANT';


        /**
         * Indexing Step
         * Number of row will index in every request
         *
         * @var int
         */
        private $index_step = 20;


        /**
         * Constructor
         * Initialize WordPress Database
         */
        public function __construct()
        {
            global $wpdb;
            $this->wpdb =& $wpdb; //phpcs:ignore
            $this->error = array(); //phpcs:ignore
            $this->prefix = $this->wpdb->prefix; //phpcs:ignore
        }


        /**
         * This function will set current indexing data to user
         * facing interface.
         *
         * @param $obj - request data from API
         *
         * @return array
         */
        public function mr_set_indexing($obj)
        {
            $options = (array) json_decode($obj['requestData'], true);
            $data = $options ? $options : array();
            if (get_option('__mr_assistant_indexing') !== false) {
                update_option('__mr_assistant_indexing', $data);
            } else {
                add_option('__mr_assistant_indexing', $data, '', 'yes');
            }
            return $this->mr_get_indexing($obj);
        }


        /**
         * This function will count all row according to post types.
         *
         * @param array  $postType - post type
         * @param string $type     - count/getID. Default - count
         *
         * @return array
         */
        public function get_index_data($postType, $type = 'count')
        {
//            echo '<pre>';
//            print_r($this->wpdb->queries);
            try {
                if ($type === 'getID') {

                    // Generate the SQL statement.
                    // The number of %s items is based on the length of the $villes array
                    $sql = "SELECT `ID` FROM `{$this->wpdb->posts}` WHERE `post_status` LIKE 'publish' AND `post_type` IN(".implode(', ', array_fill(0, count($postType), '%s')).")";

                    //Call $this->wpdb->prepare passing the values of the array as separate arguments
                    $pSQL = call_user_func_array(array($this->wpdb, 'prepare'), array_merge(array($sql), $postType));

                    $results = $this->wpdb->get_results($pSQL, ARRAY_N);
                } else {

                    // Generate the SQL statement.
                    // The number of %s items is based on the length of the $villes array
                    $sql = "SELECT COUNT(`ID`) FROM `{$this->wpdb->posts}` WHERE `post_status` LIKE 'publish' AND `post_type` IN(".implode(', ', array_fill(0, count($postType), '%s')).")";

                    //Call $this->wpdb->prepare passing the values of the array as separate arguments
                    $pSQL = call_user_func_array(array($this->wpdb, 'prepare'), array_merge(array($sql), $postType));

                    $results = $this->wpdb->get_var($pSQL);
                }
            } catch (Exception $e) {
                $results = array();
                $this->error['catch_error'] = $e;
                $status = 'Something going Wrong!!';
            }
            $this->error['last_query_error'] = $this->wpdb->last_error;

            return $results;
        }


        /**
         * This is initial function of indexing.
         * By this method API get row data of indexing.
         *
         * @param $obj - request data from API
         *
         * @return array
         */
        public function mr_get_indexing($obj)
        {
            $indexing = MrAssistantCommon::mrIndexingPostTypes();
            $post_type = MrAssistantCommon::mrPostType();
            $data = array();
            $data['mr_title'] = (isset($indexing['mr_title']) && (int)$indexing['mr_title']) ? $indexing['mr_title'] : 0;
            $data['mr_content'] = (isset($indexing['mr_content']) && (int)$indexing['mr_content']) ? $indexing['mr_content'] : 0;
            $data['mr_excerpt'] = (isset($indexing['mr_excerpt']) && (int)$indexing['mr_excerpt']) ? $indexing['mr_excerpt'] : 0;
            $data['mr_exactly'] = (isset($indexing['mr_exactly']) && (int)$indexing['mr_exactly']) ? $indexing['mr_exactly'] : 0;
            $data['post_types'] = $post_type;
            $data['indexings'] = array();
            $data['indexings222'] = $indexing;
            $data['row_count'] = 0;
            if (isset($indexing['postTypes']) && is_array($indexing['postTypes'])) {
                $data['indexings'] = $indexing['postTypes'];
                $data['row_count'] = $this->get_index_data($indexing['postTypes']);
            }
            return $data;
        }


        /**
         * Get meta value of post Attachment by post ID.
         *
         * @param $pid - post ID
         *
         * @return string
         */
        public function get_attachment($pid)
        {
            try {
                $attach = "SELECT `meta_value` FROM `{$this->wpdb->postmeta}` WHERE `post_id` = (SELECT MAX(ID) FROM `{$this->wpdb->posts}` WHERE post_parent = %d AND post_type='attachment') AND `meta_key` LIKE '_wp_attached_file' LIMIT 1";
                $pSQL = $this->wpdb->prepare($attach, array($pid));
                $attachment = $this->wpdb->get_results($pSQL, object);

                if (isset($attachment[0]->meta_value) && '' !== $attachment[0]->meta_value) {
                    return $attachment[0]->meta_value;
                }
            } catch (Exception $e) {
            }
            return '';
        }


        /**
         * This is the main function for indexing.
         * This function will do indexing according to indexing page number.
         *
         * @param $page - indexing page number
         *
         * @return array
         */
        public function make_index($page)
        {
            $result = array();
            try {
                $postType = (array)MrAssistantCommon::mrIndexingPostTypes('postTypes');
                $ids = $this->get_index_data($postType, 'getID');
                $count = count($ids);
                $start = $this->index_step * ($page - 1);
                $end = $start + $this->index_step;
                if ($end > $count) {
                    $end = $count;
                }
                for ($i = $start; $i < $end; $i++) {
                    if (isset($ids[$i][0]) && is_numeric($ids[$i][0])) {
                        $post_id = $ids[$i][0];
                        // Check and get a post meta
                        $att_url = $this->get_attachment($ids[$i][0]);
                        if ($att_url && $att_url !== '') {
                            if (metadata_exists('post', $post_id, '_mr_assistant_thumbnail')) {
                                update_post_meta($post_id, '_mr_assistant_thumbnail', $att_url);
                            } else {
                                add_post_meta($post_id, '_mr_assistant_thumbnail', $att_url, true);
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                $status = 'Something going Wrong!!';
            }

            return $result;
        }


        /**
         * This function will call dynamically within indexing loop.
         * Indexing loop will generate dynamically according to posts
         * data of selected post types.
         *
         * @param $obj - request data from API
         *
         * @return array
         */
        public function mr_do_indexing($obj)
        {
            $debug = array();
            try {
                $options = json_decode($obj['requestData'], true);
                $page = (int)$options['page'];
                $debug = $this->make_index($page);
                $results = $options;
                $status = 'ok';
            } catch (Exception $e) {
                $results = array();
                $this->error['catch_error'] = $e;
                $status = 'Something going Wrong!!';
            }
            $this->error['last_query_error'] = $this->wpdb->last_error;

            return array(
                'data' => $results,
                'debug' => $debug,
                'status' => $status,
                'access_token' => $this->access_token,
                'error' => $this->error
            );
        }
    }
endif;
