<?php

/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('MrAssistantQueryManager')) :
    /**
     * This is controller class of all single view of post through
     * Mr. Assistant API called.
     *
     * Query Builder class of search instance
     *
     * All search query will build to this class.
     *
     * @category   MrAssistantRequestManager
     * @package    WordPress
     * @subpackage Mr_Assistant
     * @author     Shapon pal <helpmrassistant@gmail.com>
     * @Version    1.0
     */
    class MrAssistantRequestManager
    {


        /**
         * Store all indexing data
         *
         * @var array of Indexing of search instance
         */

        private $_mrIndexing;


        /**
         * Store all errors in every request
         *
         * @var array of error of search
         */

        private $_mrError;


        /**
         * Store Access token
         *
         * @var string - dynamically generated token
         */
        private $_mrAccessToken = '';


        /**
         * MrAssistantQueryManager constructor.
         */
        public function __construct()
        {
            global $wpdb;
            $this->wpdb =& $wpdb;
            $this->prefix = $wpdb->prefix;
            $this->_mrIndexing = (array) MrAssistantCommon::mrIndexingPostTypes();
            $this->_mrAccessToken = MR_ASSISTANT_ACCESS_TOKEN;
        }


        /**
         * Generate query string and relevance
         *
         * @param $type - query string(qs) / relevance string (rs)
         * @param $key  - Relative Columns name
         * @param $val  - Search String
         * @param $del  - position of Columns
         *
         * @return string
         */
        public function create_query_string($type, $key, $val, $del)
        {
            $step = ($key === 'p.post_title') ? 4 : 2;
            $val = MrAssistantCommon::prepare_sql($val);
            $pw = 2;
            $rs = '';
            $allow = false; // execute after qs string generation
            if ($del === 0) {
                $rs = " = '" . $val . "'";
                $pw = ($type === 'qs') ? $step * 10 : $step * 6;
                if ($type === 'qs') {
                    $allow = true;
                }
            } elseif ($del === 1) {
                $rs = " LIKE '" . $val[0] . "%'";
                $pw = ($type === 'qs') ? $step * 8 : $step * 5;
                if ($type === 'rs') {
                    $allow = true;
                }
            } elseif ($del === 2) {
                $rs = " LIKE '" . $val . "%'";
                $pw = ($type === 'qs') ? $step * 6 : $step * 4;
                if ($key === 'p.post_title') {
                    $allow = true;
                }
            } elseif ($del === 3) {
                $rs = " LIKE '%" . $val . "%'";
                $pw = ($type === 'qs') ? $step * 5 : $step * 3;
                $allow = true;
            } elseif ($del === 4) {
                $rs = " LIKE '%" . $val . "'";
                $pw = ($type === 'qs') ? $step * 4 : $step * 2;
                if ($type === 'qs') {
                    $allow = true;
                }
            }

            // Join Query string and relevance
            if ($allow) {
                if ($type === 'qs') {
                    return $key . $rs . " OR ";
                }

                if ($type === 'rs') {
                    return " (CASE WHEN $key $rs THEN $pw ELSE 0 END) + ";
                }
            }

            return '';
        }


        /**
         * Get Post thumbnail image url
         *
         * @return string - sql
         */
        public function get_attachment_sql()
        {
            // Post Thumbnail
            $attached_url = "(SELECT `meta_value`  FROM `{$this->wpdb->postmeta}` WHERE `post_id` = p.ID AND `meta_key` LIKE '_mr_assistant_thumbnail') as attachment";
            return $attached_url;
        }


        /**
         * Get product Price
         * Get variation product price
         *
         * @param bool $variation - product variation true/false
         *
         * @return string - sql
         */
        public function get_price_sql($variation = false)
        {
            if ($variation) {
                return '';
            }

            $min = "(CASE WHEN (p.post_type = 'product') THEN (SELECT MIN(`meta_value`)  FROM `{$this->prefix}postmeta` WHERE `post_id` = p.id AND `meta_key` LIKE '_price') ELSE 0 END) AS price, ";
            $max = "(CASE WHEN (p.post_type = 'product') THEN (SELECT MAX(`meta_value`)  FROM `{$this->prefix}postmeta` WHERE `post_id` = p.id AND `meta_key` LIKE '_price') ELSE 0 END) AS price_max";
            return $min . $max;
        }


        /**
         * Select column with content or not
         *
         * @param bool $content - allow content true/false
         *
         * @return string
         */
        public function select_column($content = false)
        {
            if ($content) {
                return 'SELECT p.id, p.post_title, p.post_content, p.post_excerpt, p.post_type';
            }

            return 'SELECT p.id, p.post_title, p.post_type';
        }


        /**
         * Make sql query string with table name
         *
         * @return string
         */
        public function query_table()
        {
            return "FROM `{$this->wpdb->posts}` p WHERE p.post_status = 'publish'";
        }


        /**
         * Pagination for more result view
         *
         * @param int $page - page number
         * @param int $step - row number
         *
         * @return string
         */
        public function get_limit($page = 1, $step = 0)
        {
            // set the number of items to display per page
            $page = isset($page) && $page > 1 ? $page : 1;
            $items_per_page = isset($step) && $step > 1 ? $step : 10;
            $offset = ($page - 1) * $items_per_page;
            return 'LIMIT ' . absint($offset) . ', ' . absint($items_per_page) . ';';
        }


        /**
         * Main Search string builder
         *
         * @param array $obj   - request data from api call
         * @param string $type - dynamically selected api name
         * @param array $pt    - post types array
         *
         * @return array
         */
        protected function filter($obj, $type, $pt = array())
        {
            $this->_mrError = array();

            $key = MrAssistantCommon::make_slug(trim($obj['ks']));
            //$val = $this->wpdb->esc_like(trim($obj['qs']));
            $val = MrAssistantCommon::prepare_sql($obj['qs']);
            $mr_query = '';


            // user config
//            $config = (isset($obj['config']) && !empty($obj['config'])) ? $obj['config'] : array();
//
//            if ($key === 'navigator') {
//                $config['mr_content'] = 'not';
//                $config['mr_excerpt'] = 'not';
//            }


            /**
             * ***************************
             * Generate Query string filter and build order by relevance
             * ***************************
             */

            $search = '';
            $relevance = '';
            if (isset($val) && $val !== '') {
                // Indexing filter
                $labels = array();
                //Title
                if (isset($this->_mrIndexing['mr_title']) && (int)$this->_mrIndexing['mr_title'] === 1) {
                    $labels[] = 'p.post_title';
                }
                //Content
                if (isset($this->_mrIndexing['mr_content']) && (int)$this->_mrIndexing['mr_content'] === 1) {
                    $labels[] = 'p.post_content';
                }
                //Excerpt
                if (isset($this->_mrIndexing['mr_excerpt']) && (int)$this->_mrIndexing['mr_excerpt'] === 1) {
                    $labels[] = 'p.post_excerpt';
                }
                //Default
                if (empty($labels)) {
                    $labels[] = 'p.post_title';
                }
                //Exactly Match in Search
                if (isset($this->_mrIndexing['mr_exactly']) && (int)$this->_mrIndexing['mr_exactly'] === 0) {
                    $val = MrAssistantCommon::prepare_like($val);
                }
                $arr = array('p.post_title', 'p.post_content', 'p.post_excerpt');
                $qString = ''; // initial empty
                $rString = ''; // initial empty

                /**
                 * Execute loop for loop
                 */
                foreach ($arr as $item) {
                    for ($i = 0; $i < 5; $i++) {
                        $qString .= $this->create_query_string('qs', $item, $val, $i);
                        $rString .= $this->create_query_string('rs', $item, $val, $i);
                    }
                }


                if ($qString !== '') {
                    $qString = rtrim(trim($qString), 'OR');
                    $search = ' AND (' . $qString . ' ) ';
                }

                if ($rString !== '') {
                    $rString = rtrim(trim($rString), '+');
                    $relevance = ' ORDER BY  (' . $rString . ' )  DESC ';
                }
            }


            /**
             * Filter by Taxonomies
             */

            $taxonomy = '';
            if ($type === 'tags' && $key !== '') {
                $key = MrAssistantCommon::prepare_sql($key);
                $taxonomy = " AND (p.ID IN ( SELECT `object_id` FROM `{$this->wpdb->term_relationships}` WHERE `term_taxonomy_id` IN( (SELECT term_id FROM `{$this->wpdb->terms}` WHERE `slug` LIKE '".$key."') ) )) ";
            }


            /**
             * Filter by Author
             */

            $author = '';
            if ($type === 'author' && $key !== '') {
                $key = MrAssistantCommon::prepare_sql($key);
                $author = " AND (p.post_author IN ( SELECT `ID` FROM `{$this->wpdb->users}` WHERE `display_name` LIKE '".$key."')) ";
            }


            /**
             * Allow Search in indexing post type
             * Search with particular post type
             */

            $post_type = '';
            if (isset($this->_mrIndexing['postTypes']) && $this->_mrIndexing['postTypes']) {
                $allow_post_types = $this->_mrIndexing['postTypes'];
                $valid_pt = array();
                if (!empty($pt)) {
                    foreach ($pt as $t) {
                        if (in_array($t, $allow_post_types, true)) {
                            $valid_pt[] = $t;
                        }
                    }
                } else {
                    $valid_pt = $allow_post_types;
                }
                $sanitize_pt = MrAssistantCommon::prepare_sql($valid_pt);
                $post_type = " AND (`post_type` IN(".$sanitize_pt.") ) ";
            }


            /**
             * Page controller
             */

            $pages = array('page', 'pages');
            if ($val === '' && in_array($type, $pages, true)) {
                $search = '';
            }


            /**
             * Post controller
             */

            $posts = array('post', 'posts');
            if ($val === '' && in_array($type, $posts, true)) {
                $search = '';
            }


            /**
             * WooCommerce product controller
             */

            $products = array('product', 'products');
            if ($val === '' && in_array($type, $products, true)) {
                $search = '';
            }


            /**
             * Filter by Attributes
             *
             * @var $att - Attributes
             */
            $att = '';
//            if (isset($config['price_min'], $config['price_max']) && !(empty($config['price_min']) && empty($config['price_max']))) {
//                $att = " AND ( p.ID IN (SELECT `post_id` FROM `{$this->prefix}postmeta` WHERE `meta_key` LIKE '_price' AND CAST(`meta_value` AS INTEGER) BETWEEN '" . $config['price_min'] . "' AND '" . $config['price_max'] . "') )";
//            }


            try {

                /**
                 * Generate all filter string
                 *
                 * @var string $filter - filter string
                 */
                $filter = implode(array(
                    $search,
                    $taxonomy,
                    $post_type,
                    $att,
                    $author,
                    $relevance
                ));


                /**
                 * Generate all SQL Query string
                 *
                 * @var string $mr_query - SQL Query string
                 */
                $mr_query = implode(' ', array(
                    $this->select_column(true) . ',',
                    $this->get_attachment_sql() . ',',
                    $this->get_price_sql(false),
                    $this->query_table(),
                    $filter,
                    $this->get_limit(1, 10)
                ));

                // Fetch result
                $pSQL = $this->wpdb->prepare($mr_query);
                $results = $this->wpdb->get_results($pSQL, object);
                if (count($results) > 0) {
                    $status = 'ok';
                } else {
                    $status = 'No Result Found';
                }
            } catch (Exception $e) {
                $results = array();
                $this->_mrError['catch_error'] = $e;
                $status = 'Something going Wrong!!';
            }
            $this->_mrError['last_query_error'] = $this->wpdb->last_error;

            return array(
                'data' => $results,
                'status' => $status,
                'sql' => $mr_query,
                'pSQL' => $pSQL,
                'access_token' => $this->_mrAccessToken,
                'error' => $this->_mrError
            );
        }


        /**
         * Master API controller
         *
         * @param array $obj - array of configuration
         *
         * @return array - search result
         */
        public function master(array $obj)
        {
            return $this->filter($obj, 'master');
        }


        /**
         * Get Taxonomy
         *
         * @param array $obj   - request data
         * @param string $type - dynamically generate api name
         * @param array $pt    - post types
         *
         * @return array
         */
        public function filter_tags(array $obj, $type, $pt = array())
        {
            $this->_mrError = array();

            $key = MrAssistantCommon::make_slug(trim($obj['ks']));
//            $val = MrAssistantCommon::prepare_sql(trim($obj['qs']));
            $taxonomy = 'category';
            if (isset($obj['tax'])) {
                $allow_taxonomy = array('category', 'product_cat');
                $tax = trim($obj['tax']);
                $taxonomy = in_array($tax, $allow_taxonomy, true) ? $tax : 'category';
            }

            if ($key !== '') {
                return $this->filter($obj, $type);
            }
            try {
                $results = array();
                $args = array(
                    'hide_empty' => true,
                    'taxonomy' => $taxonomy,
                    'order' => 'ASC'
                );
                $categories = get_categories($args);
                foreach ($categories as $category) {
                    $results[$category->term_id] = array(
                        "name" => $category->name,
                        "slug" => $category->slug,
                        "term_id" => $category->term_id,
                    );
                }
                if (count($results) > 0) {
                    $status = 'ok';
                } else {
                    $status = 'No Result Found';
                }
            } catch (Exception $e) {
                $results = array();
                $this->_mrError['catch_error']= $e;
                $status = 'Something going Wrong!!';
            }
            $this->_mrError['last_query_error'] = $this->wpdb->last_error;
            return array(
                'data' => $results,
                'status' => $status,
                'access_token' => $this->_mrAccessToken,
                'error' => $this->_mrError
            );
        }


        /**
         * Tags API controller
         *
         * @param array $obj - array of configuration
         *
         * @return array - search result
         */
        public function tags($obj)
        {
            return $this->filter_tags($obj, 'tags');
        }


        /**
         * Author API controller
         *
         * @param array $obj - array of configuration
         *
         * @return array - search result
         */
        public function author(array $obj)
        {
            return $this->filter($obj, 'author');
        }


        /**
         * Products API controller
         *
         * @param array $obj - array of configuration
         *
         * @return array - search result
         */
        public function products(array $obj)
        {
            return $this->filter($obj, 'products', array('product'));
        }


        /**
         * Posts API controller
         *
         * @param array $obj - array of configuration
         *
         * @return array - search result
         */
        public function posts(array $obj)
        {
            return $this->filter($obj, 'posts', array('post'));
        }


        /**
         * Pages API controller
         *
         * @param array $obj - array of configuration
         *
         * @return array - search result
         */
        public function pages(array $obj)
        {
            return $this->filter($obj, 'pages', array('page'));
        }
    }

endif;
