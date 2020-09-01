<?php

// If this file is called directly, abort.
defined('ABSPATH') || exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * This is used to Load the dependencies,
 * enqueue the admin-specific stylesheet.
 */
if (!class_exists('Mr_Assistant_Admin')) :
    /**
     * The admin-specific functionality of Mr. Assistant plugin.
     *
     * @category   Mr_Assistant_Admin
     * @package    WordPress
     * @subpackage Mr_Assistant
     * @author     Shapon pal <helpmrassistant@gmail.com>
     * @Version    1.0
     */
    class Mr_Assistant_Admin
    {


        /**
         * Set all Mr. Assistant options here.
         * Execute thought Mr_Options_tree class.
         *
         * @var null
         */
        protected $mrOptionsTree = null;


        /**
         * Create menus interface of the Mr. Assistant Chat bot
         * admin which will be used to Control Panel and setting with WordPress.
         *
         * @access public
         *
         * @return void
         */
        public function mrAdminPanel()
        {
            if (current_user_can('manage_options')) {
                // Add Mr. Assistant Menu Page
                add_menu_page(
                    'Mr. Assistant',
                    'Mr. Assistant',
                    'manage_options',
                    'mr-assistant-chatbot',
                    '',
                    'dashicons-mr-assistant',
                    26
                );

                // Add control panel Submenu page
                add_submenu_page(
                    'mr-assistant-chatbot',
                    __('Chat Control Panel', 'mr-assistant'),
                    __('Chat Control Panel', 'mr-assistant'),
                    'manage_options',
                    'mr-assistant-chatbot',
                    array($this, 'mrChatControlPanel')
                );

                // Add Mr. Assistant setting page
                add_submenu_page(
                    'mr-assistant-chatbot',
                    __('Chat Configuration', 'mr-assistant'),
                    __('Chat Configuration', 'mr-assistant'),
                    'manage_options',
                    'mr-assistant-setting',
                    array($this, 'mrExecuteSettingsPage')
                );
            }
        }


        /**
         * This function will set some custom style for
         * admin facing side of Mr. Assistant
         *
         * @return void
         */
        public function mrAdminCss()
        {
            $mr_current_screen = get_current_screen();
            $mrAvatar = MR_ASSISTANT_BASE_URL . 'assets/images/mr-36.svg';
            $custom_css = '
                #wpcontent {
                    padding-left: 0px !important;
                }
                #mr_assistant_admin_iframe{
                    display: block;
                    position: relative;
                    width: 100%;
                    height: calc(100vh - 25px);
                }';
            if ($mr_current_screen->id !=="toplevel_page_mr-assistant-chatbot") {
                $custom_css = "";
            }
            echo '<style>
            .dashicons-mr-assistant {
                background-image: url(' . $mrAvatar . ');
                background-repeat: no-repeat;
                background-position: center; 
            }
            '.$custom_css.'
            </style>';
        }


        /**
         * This function will execute Chat Control Panel interface in the
         * admin facing interface.
         *
         * @return void
         */
        public function mrChatControlPanel()
        {
            if (current_user_can('manage_options')) {

                /**
                 * Actions before Chat Control Panel interface.
                 */
                do_action('mr_assistant_before_console');


                /**
                 * Mr Assistant Chat Control Panel interface
                 */
                echo '<div id="mr_assistant_console_dom"></div>';


                /**
                 * Actions after Chat Control Panel interface.
                 */
                do_action('mr_assistant_after_console');
            }
        }


        /**
         * Mr. Assistant Setting Page Execution.
         * This function will dynamically generate all settings options
         * using through Mr_Options_Tree core class.
         *
         * @return void
         */
        public function mrExecuteSettingsPage()
        {
            $mrLogo = MR_ASSISTANT_BASE_URL . 'assets/images/mr-logo.png';
            echo '<div class="wrap mr-assistant-wrap">';
            echo '<h1 class="wp-heading-inline mr-name"><img src="' . $mrLogo.'" alt="'.MR_ASSISTANT_NAME.'"/>';
            echo '<sup class="mr-version">V-' . MR_ASSISTANT_VERSION . '</sup></h1>';
            $this->mrOptionsTree->mrSettingTabs();
            $this->mrOptionsTree->mrSettingForms();
            echo '</div>';
        }


        /**
         * This function will execute all options of settings
         * using MrOptionsTree core class.
         * Set all setting sections dynamically.
         * Set all settings fields under specific sections interface.
         *
         * @return void
         */
        public function mrSettingsInitialized()
        {
            $this->mrOptionsTree = new MrAssistantOptionsTree();
            $this->mrOptionsTree->mrSectionsInit(MrAssistantSettings::mrSections());
            $this->mrOptionsTree->mrFieldsInit(MrAssistantSettings::mrFields());
            $this->mrOptionsTree->mrAdminInitialize();
        }
    }
endif;
