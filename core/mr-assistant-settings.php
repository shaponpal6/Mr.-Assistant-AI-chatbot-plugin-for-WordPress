<?php
/**
 * The admin-specific settings functionality of the plugin.
 *
 * This is used to create setting panel for this plugin
 *
 * @category   MrAssistantSettings
 * @package    WordPress
 * @subpackage Mr_Assistant
 * @author     Shapon pal <helpmrassistant@gmail.com>
 * @Version    1.0
 */


/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('MrAssistantSettings')) :
    /**
     * Class MrSettings
     */
    class MrAssistantSettings
    {

        /**
         * Set all Mr. Assistant settings sections.
         * Need a unique id for every section.
         * Add a section specific name that will appear as tab.
         *
         * @return array - all Mr. Assistant settings sections
         */
        public static function mrSections()
        {
            return array(
                array(
                    'id' => 'mr_assistant_general_settings',
                    'title' => __('General', 'mr-assistant')
                ),
                array(
                    'id' => 'mr_assistant_appearance_settings',
                    'title' => __('Appearance', 'mr-assistant')
                ),
                array(
                    'id' => 'mr_assistant_admin_appearance',
                    'title' => __('Admin Style', 'mr-assistant')
                ),
                array(
                    'id' => 'mr_assistant_response',
                    'title' => __('Response', 'mr-assistant')
                ),
                array(
                    'id' => 'mr_assistant_template_settings',
                    'title' => __('Template', 'mr-assistant')
                ),
                array(
                    'id' => 'mr_assistant_realtime_database',
                    'title' => __('Realtime Database', 'mr-assistant')
                ),
                array(
                    'id' => 'mr_assistant_chatbot_settings',
                    'title' => __('Virtual Assistant', 'mr-assistant')
                ),
                array(
                    'id' => 'mr_assistant_advance_settings',
                    'title' => __('Advanced', 'mr-assistant')
                )
            );
        }


        /**
         * Set all Mr. Assistant settings sections fields.
         * Need a unique id for every field.
         * Add a section specific label that will appear as option title.
         * Set a section specific type that will dynamically generate type specific
         * option field
         * Add a section specific description that will appear under option field.
         * Set your options for this settings option field. Type - radio/file
         * Set default value of option.
         * you can validate option by calling `sanitize-{option type}` in
         * the array element of `sanitize_callback` as callback.
         *
         * @return array settings fields
         */
        public static function mrFields()
        {
            return array(
                'mr_assistant_general_settings' => array(
                    array(
                        'name' => 'mr_assistant',
                        'label' => __('Chat Bot Status', 'mr-assistant'),
                        'desc' => __('This is on/off option for chat widget on the public side only.', 'mr-assistant'),
                        'type' => 'radio',
                        'options' => array(
                            'on' => 'Enable Chat Widget',
                            'off' => 'Disable Chat Widget'
                        ),
                        'default' => 'on',
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'translation',
                        'label' => __('Translation Mode', 'mr-assistant'),
                        'desc' => __('This option will Enable or Disable Chat Control Panel Auto Translation Mode.', 'mr-assistant').'<p><small>'.__('Note: It will overwrite some events in the Chat Control Panel. So After your Translation Completed, You should disable Translation Mode for better Experience.', 'mr-assistant').'</small></p>',
                        'type' => 'radio',
                        'options' => array(
                            'on' => 'Enable Translation mode',
                            'off' => 'Disable Translation mode'
                        ),
                        'default' => 'off',
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'application_key',
                        'label' => __('Application Key', 'mr-assistant'),
                        'desc' => '<strong><a href="https://api.mrassistant.tech/api?app_link=' . get_site_url() . '" target="_blank">' . __('Get your Application key', 'mr-assistant') . '</a></strong><p><small>' . __('The Application key is required to activate the plugin and get free updates.', 'mr-assistant') . '</small></p>',
                        'type' => 'password',
                        'default' => '',
                        'sanitize_callback' => 'sanitize_api_keys'
                    ),
                ),
                'mr_assistant_appearance_settings' => array(
                    array(
                        'name' => 'theme_type',
                        'label' => __('Choose Theme', 'mr-assistant'),
                        'desc' => __('Choose your favourite theme type.', 'mr-assistant') . '<p><small>' . __('Note: you need to choose theme type as use color to use your custom color as theme. Default: use theme.', 'mr-assistant') . '</small></p>',
                        'type' => 'radio',
                        'options' => array(
                            'theme' => 'Use Theme',
                            'color' => 'Use Custom Color'
                        ),
                        'default' => 'theme',
                        'sanitize_callback' => 'sanitize_theme_color'
                    ),
                    array(
                        'name' => 'theme',
                        'label' => '',
                        'desc' => __('Choose a theme for chat widget.', 'mr-assistant'),
                        'type' => 'radio',
                        'options' => self::mrThemeOptionsUI(),
                        'default' => 'theme1',
                        'sanitize_callback' => 'sanitize_theme'
                    ),
                    array(
                        'name' => 'color',
                        'label' => '',
                        'desc' => __('Choose custom theme color for chat widget.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#E91E63',
                        'sanitize_callback' => 'sanitize_color'
                    ),
                    array(
                        'name' => 'chat_icon',
                        'label' => __('Chat ICON', 'mr-assistant'),
                        'desc' => __('Choose a chat icon. You can add an image URL from media library or different sources.', 'mr-assistant'),
                        'type' => 'file',
                        'default' => '',
                        'options' => array(
                            'button_label' => 'Browse icon'
                        ),
                        'sanitize_callback' => 'sanitize_url'
                    ),
                    array(
                        'name' => 'ic_width',
                        'label' => '',
                        'desc' => __('Set chat image width 30px to 100px', 'mr-assistant'),
                        'min' => 3,
                        'max' => 100,
                        'step' => '1',
                        'type' => 'number',
                        'default' => '48',
                        'sanitize_callback' => 'sanitize_integer'
                    ),
                    array(
                        'name' => 'ic_height',
                        'label' => '',
                        'desc' => __('Set chat image height 30px to 100px', 'mr-assistant'),
                        'min' => 30,
                        'max' => 100,
                        'step' => '1',
                        'type' => 'number',
                        'default' => '48',
                        'sanitize_callback' => 'sanitize_integer'
                    ),
                    array(
                        'name' => 'chat_bg',
                        'label' => __('Chat Widget Background', 'mr-assistant'),
                        'desc' => __('Choose chat widget background image that will appear in public facing chat widget. You can add an image URL from media library or different sources.', 'mr-assistant'),
                        'type' => 'file',
                        'default' => '',
                        'options' => array(
                            'button_label' => 'Browse image'
                        ),
                        'sanitize_callback' => 'sanitize_url'
                    ),

                    array(
                        'name' => 'fc1',
                        'label' => __('Admin Message Color', 'mr-assistant'),
                        'desc' => __('Add admin messages color appear in chat widget.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#06132b',
                        'sanitize_callback' => 'sanitize_color'
                    ),
                    array(
                        'name' => 'megs_box',
                        'label' => __('Admin Message Container Background', 'mr-assistant'),
                        'desc' => __('Add admin messages container background color appear in chat widget.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#e5ebf5',
                        'sanitize_callback' => 'sanitize_color'
                    ),
                    array(
                        'name' => 'fc2',
                        'label' => __('Clients Message Color', 'mr-assistant'),
                        'desc' => __('Add client messages color appear in chat widget.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#fff',
                        'sanitize_callback' => 'sanitize_color'
                    ),
                    array(
                        'name' => 'fc3',
                        'label' => __('Name and Date Color', 'mr-assistant'),
                        'desc' => __('Add name and date color appear in chat widget.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#607D8B',
                        'sanitize_callback' => 'sanitize_color'
                    ),

                    array(
                        'name' => 'custom_css',
                        'label' => __('Custom Style', 'mr-assistant'),
                        'desc' => __('Add Custom CSS for public facing Chat widget', 'mr-assistant'),
                        'placeholder' => '.mr-assistant-container{}',
                        'type' => 'textarea',
                        'sanitize_callback' => 'sanitize_css'
                    ),
                    array(
                        'name' => 'font_link',
                        'label' => '',
                        'desc' => __('Add Custom Font for Chat widget', 'mr-assistant'),
                        'placeholder' => '&lt;link href=&quot;https://fonts.googleapis.com/css2?family=Fondamento&display=swap&quot; rel=&quot;stylesheet&quot;&gt;',
                        'type' => 'textarea',
                        'sanitize_callback' => 'sanitize_link'
                    ),
                    array(
                        'name' => 'font_name',
                        'label' => '',
                        'desc' => __('Add font name', 'mr-assistant'),
                        'placeholder' => '&#39;Fondamento&#39;, cursive',
                        'type' => 'text',
                        'sanitize_callback' => 'sanitize_title'
                    ),

                ),
                'mr_assistant_admin_appearance' => array(
                    array(
                        'name' => 'admin_theme_type',
                        'label' => __('Choose Admin Theme', 'mr-assistant'),
                        'desc' => __('Choose your favourite theme type for admin panel.', 'mr-assistant') . '<p><small>' . __('Note: you need to choose theme type as use color to use your custom color as theme. Default: use theme.', 'mr-assistant') . '</small></p>',
                        'type' => 'radio',
                        'options' => array(
                            'theme' => 'Use Theme',
                            'color' => 'Use Custom Color'
                        ),
                        'default' => 'theme',
                        'sanitize_callback' => 'sanitize_theme_color'
                    ),
                    array(
                        'name' => 'admin_theme',
                        'label' => '',
                        'desc' => __('Choose a theme for admin chat control panel.', 'mr-assistant'),
                        'type' => 'radio',
                        'options' => self::mrThemeOptionsUI(),
                        'default' => 'theme1',
                        'sanitize_callback' => 'sanitize_theme'
                    ),
                    array(
                        'name' => 'admin_color',
                        'label' => '',
                        'desc' => __('Choose custom theme color for admin chat control panel.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#E91E63',
                        'sanitize_callback' => 'sanitize_color'
                    ),

                    array(
                        'name' => 'menu_bg',
                        'label' => __('Menu Background', 'mr-assistant'),
                        'desc' => __('Add menu background color for admin chat control panel.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#E91E63',
                        'sanitize_callback' => 'sanitize_color'
                    ),
                    array(
                        'name' => 'menu_font_color',
                        'label' => __('Menu Font Color', 'mr-assistant'),
                        'desc' => __('Add menu font color for admin chat control panel.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#fff',
                        'sanitize_callback' => 'sanitize_color'
                    ),
                    array(
                        'name' => 'panel_bg',
                        'label' => __('Panel Background Color', 'mr-assistant'),
                        'desc' => __('Add panel header background color for admin chat control panel.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#E91E63',
                        'sanitize_callback' => 'sanitize_color'
                    ),
                    array(
                        'name' => 'overview',
                        'label' => __('Overview Background Color', 'mr-assistant'),
                        'desc' => __('Add Overview Background Color for admin Chat control panel.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#E91E63',
                        'sanitize_callback' => 'sanitize_color'
                    ),
                    array(
                        'name' => 'admin_css',
                        'label' => __('CUSTOM STYLE FOR ADMIN CONTROL PANEL', 'mr-assistant'),
                        'desc' => __('Add Custom CSS for admin chat control panel', 'mr-assistant'),
                        'placeholder' => __('.mr-container{}', 'mr-assistant'),
                        'type' => 'textarea',
                        'sanitize_callback' => 'sanitize_css'
                    ),
                    array(
                        'name' => 'admin_font_link',
                        'label' => '',
                        'desc' => __('Add Custom Font for admin chat control panel', 'mr-assistant'),
                        'placeholder' => '&lt;link href=&quot;https://fonts.googleapis.com/css2?family=Fondamento&display=swap&quot; rel=&quot;stylesheet&quot;&gt;',
                        'type' => 'textarea',
                        'sanitize_callback' => 'sanitize_link'
                    ),
                    array(
                        'name' => 'admin_font_name',
                        'label' => '',
                        'desc' => __('Add font name', 'mr-assistant'),
                        'placeholder' => '&#39;Fondamento&#39;, cursive',
                        'type' => 'text',
                        'sanitize_callback' => 'sanitize_title'
                    ),

                ),
                'mr_assistant_response' => array(
                    array(
                        'name' => 'intro',
                        'label' => __('Chat Intro Message', 'mr-assistant'),
                        'desc' => __('This Intro message will appear on public facing chst widget when someone browsing your website.', 'mr-assistant'),
                        'placeholder' => __('Add Intro message', 'mr-assistant'),
                        'type' => 'textarea',
                        'default' => 'Hello there! I\'m your virtual assistant. I can assist you in searching contents, current offer & discount, chat with live person, FAQ & more...',
                        'sanitize_callback' => 'sanitize_title'
                    ),
                    array(
                        'name' => 'intro_box_width',
                        'label' => '',
                        'desc' => __('Set Intro box width 330px to 380px.', 'mr-assistant'),
                        'placeholder' => '360',
                        'min' => 330,
                        'max' => 380,
                        'step' => '1',
                        'type' => 'number',
                        'default' => '360',
                        'sanitize_callback' => 'sanitize_integer'
                    ),
                    array(
                        'name' => 'online_text',
                        'label' => __('Online Message', 'mr-assistant'),
                        'desc' => __('Set Message to notify visitors when you are Online.', 'mr-assistant'),
                        'placeholder' => __('Add Online status', 'mr-assistant'),
                        'type' => 'text',
                        'default' => 'Now we are in Online.',
                        'sanitize_callback' => 'sanitize_title'
                    ),
                    array(
                        'name' => 'offline_text',
                        'label' => __('Offline Message', 'mr-assistant'),
                        'desc' => __('Set Message to notify visitors when you are Offline.', 'mr-assistant'),
                        'placeholder' => __('Add Offline Message', 'mr-assistant'),
                        'type' => 'text',
                        'default' => 'We are typically reply in 1 business day.',
                        'sanitize_callback' => 'sanitize_title'
                    ),
                    array(
                        'name' => 'placeholder',
                        'label' => __('Message Input box Placeholder Text', 'mr-assistant'),
                        'desc' => __('Set Placeholder Text for Message Input box for public facing chat interface.', 'mr-assistant'),
                        'placeholder' => __('Search & Message...', 'mr-assistant'),
                        'type' => 'text',
                        'default' => 'Search & Message...',
                        'sanitize_callback' => 'sanitize_title'
                    ),
                    array(
                        'name' => 'gdpr_check',
                        'label' => __('Allow GDPR Compliance', 'mr-assistant'),
                        'desc' => __('Visitors should acknowledge GDPR Compliance to start a new conversation.', 'mr-assistant'),
                        'type' => 'checkbox',
                        'default' => 'off',
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'gdpr',
                        'label' => __('GDPR Compliance', 'mr-assistant'),
                        'desc' => __('Textarea description', 'mr-assistant'),
                        'placeholder' => __('Add Custom CSS', 'mr-assistant'),
                        'type' => 'textarea',
                        'default' => 'I understand and acknowledge that any of my personal data submitted in `' . esc_html(get_bloginfo('name')) . '` will be processed and transmitted in accordance with the General Data Protection Regulation (GDPR).',
                        'sanitize_callback' => 'sanitize_title'
                    ),
                    array(
                        'name' => 'policy_url',
                        'label' => __('Privacy policy URL', 'mr-assistant'),
                        'desc' => __('Add your website privacy policy URL.', 'mr-assistant'),
                        'placeholder' => esc_url(trailingslashit(get_bloginfo('url'))) . 'policy',
                        'type' => 'url',
                        'default' => '',
                        'sanitize_callback' => 'sanitize_url'
                    ),
                ),
                'mr_assistant_template_settings' => array(
                    array(
                        'name' => 'tmImage',
                        'label' => __('Show Image in Template', 'mr-assistant'),
                        'desc' => __('Enable this option to show image on the template.', 'mr-assistant'),
                        'type' => 'checkbox',
                        'default' => 'on',
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'tmTitle',
                        'label' => __('Show Title in Template', 'mr-assistant'),
                        'desc' => __('Enable this option to show Title on the template.', 'mr-assistant'),
                        'type' => 'checkbox',
                        'default' => 'on',
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'tmContent',
                        'label' => __('Show Content in Template', 'mr-assistant'),
                        'desc' => __('Enable this option to show Content on the template.', 'mr-assistant'),
                        'type' => 'checkbox',
                        'default' => 'on',
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'tmActions',
                        'label' => __('Show Action in Template', 'mr-assistant'),
                        'desc' => __('Enable this option to view Actions(Add to Cart, View Details, price) on the template.', 'mr-assistant'),
                        'type' => 'checkbox',
                        'default' => 'on',
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'tmBg',
                        'label' => __('Card Background Color', 'mr-assistant'),
                        'desc' => __('Choose template replay card background color. Reply card will appear when Mr. Assistant find any result.', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#f1f0f0',
                        'sanitize_callback' => 'sanitize_color'
                    ),
                    array(
                        'name' => 'tmFont',
                        'label' => __('Card Font Color', 'mr-assistant'),
                        'desc' => __('Choose template replay card font color. ', 'mr-assistant'),
                        'type' => 'color',
                        'default' => '#333',
                        'sanitize_callback' => 'sanitize_color'
                    ),
                ),
                'mr_assistant_realtime_database' => array(
                    array(
                        'name' => 'project_id',
                        'label' => __('Project ID*', 'mr-assistant'),
                        'desc' => __('Add Project ID of your Firebase project.', 'mr-assistant'),
                        'placeholder' => __('Add Project ID', 'mr-assistant'),
                        'type' => 'text',
                        'default' => '',
                        'sanitize_callback' => 'sanitize_title'
                    ),
                    array(
                        'name' => 'web_api_key',
                        'label' => __('Web API Key*', 'mr-assistant'),
                        'desc' => __('Add Web API Key of your Firebase project.', 'mr-assistant'),
                        'placeholder' => __('Add Web API Key', 'mr-assistant'),
                        'type' => 'text',
                        'default' => '',
                        'sanitize_callback' => 'sanitize_title'
                    ),
                    array(
                        'name' => 'mrFireDocs',
                        'label' => '',
                        'desc' =>  self::mrFireDocs(),
                        'type' => 'html',
                    ),
                    array(
                        'name' => 'mrFireRules',
                        'label' => '',
                        'desc' =>  self::mrFireRules(),
                        'type' => 'html',
                    ),
                ),
                'mr_assistant_chatbot_settings' => array(
                    array(
                        'name' => 'mrAssistantBot',
                        'label' => __('Virtual Assistant Status', 'mr-assistant'),
                        'desc' => __('This is on/off option for Virtual Assistant.', 'mr-assistant'),
                        'type' => 'radio',
                        'default' => 'on',
                        'options' => array(
                            'on' => 'Enable Virtual Assistant',
                            'off' => 'Disable Virtual Assistant'
                        ),
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'name',
                        'label' => __('Virtual Assistant Name', 'mr-assistant'),
                        'desc' => __('Add Virtual Assistant Name', 'mr-assistant'),
                        'placeholder' => '',
                        'type' => 'text',
                        'default' => 'Mr. Assistant',
                        'sanitize_callback' => 'sanitize_title'
                    ),
                    array(
                        'name' => 'avatar',
                        'label' => __('Virtual Assistant ICON', 'mr-assistant'),
                        'desc' => __('Virtual Assistant ICON will appear as chatbot ICON everywhere but not as chatbot menu ICON. You can add an image/icon URL from media library or different sources.', 'mr-assistant'),
                        'type' => 'file',
                        'default' => '',
                        'options' => array(
                            'button_label' => 'Browse ICON'
                        ),
                        'sanitize_callback' => 'sanitize_url'
                    ),
                    array(
                        'name' => 'menu_avatar',
                        'label' => __('Virtual Assistant Menu ICON', 'mr-assistant'),
                        'desc' => __('Virtual Assistant Menu ICON will appear only as chatbot menu ICON. You can add an image/icon URL from media library or different sources.', 'mr-assistant'),
                        'type' => 'file',
                        'default' => '',
                        'options' => array(
                            'button_label' => 'Browse ICON'
                        ),
                        'sanitize_callback' => 'sanitize_url'
                    ),
                ),
                'mr_assistant_advance_settings' => array(
                    array(
                        'name' => 'dev_mode',
                        'label' => __('Developer Mode', 'mr-assistant'),
                        'desc' => __('Enable Developer Mode on public facing chat widget.', 'mr-assistant'),
                        'type' => 'checkbox',
                        'default' => 'off',
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'admin_dev_mode',
                        'label' => __('Admin Developer Mode', 'mr-assistant'),
                        'desc' => __('Enable Developer Mode on admin chat control panel.', 'mr-assistant'),
                        'type' => 'checkbox',
                        'default' => 'off',
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'cmd',
                        'label' => __('Command Line Mode', 'mr-assistant'),
                        'desc' => __('Command Line Mode on public facing chat widget.', 'mr-assistant'),
                        'type' => 'radio',
                        'default' => 'off',
                        'options' => array(
                            'on' => 'Enable Command Line Mode',
                            'off' => 'Disable Command Line Mode'
                        ),
                        'sanitize_callback' => 'sanitize_on_off'
                    ),
                    array(
                        'name' => 'cmd_html',
                        'label' => '',
                        'desc' => '<code>-mr allow adminchat</code><p>'.__('System will block Admin when they try to chat as anonymous client. You need to use this command to enable admin chat as anonymous client.', 'mr-assistant').'</p>',
                        'type' => 'html',
                    ),
                )
            );
        }


        /**
         * This function will uncover theme name by theme gradient color.
         * It will return all theme when theme name is empty.
         *
         * @param string $theme_name - theme mane
         *
         * @return array|mixed|string
         */
        public static function get_themes($theme_name = '')
        {
            $themes = array(
                'theme1' => 'linear-gradient(to right, #4CAF50, #0abfbc)',
                'theme2' => 'linear-gradient(to left, #ff0099,  #E91E63)',
                'theme3' => 'linear-gradient(to left, #ff0099, #00BCD4)',
                'theme4' => 'linear-gradient(to left, #636363, #a2ab58)',
                'theme5' => 'linear-gradient(to right, #00d2ff, #3a7bd5)',
                'theme6' => 'linear-gradient(to right, #f2709c, #ff9472)',
                'theme7' => 'linear-gradient(to right, #009688, #1dbfbd)',
                'theme8' => 'linear-gradient(to right, #ff4e50, #f9d423)',
                'theme9' => 'linear-gradient(to left, #bdc3c7, #2c3e50)',
            );
            if ($theme_name !== '') {
                return (isset($themes[$theme_name])) ? $themes[$theme_name] : '#E91E63';
            }
            return $themes;
        }


        /**
         * This function will convert all theme as radio option values UI as array
         * using theme gradient color as background color.
         *
         * @return array - radio option values array
         */
        public static function mrThemeOptionsUI()
        {
            return array_map(static function ($theme) {
                return '<div style="display: inline-block; width:200px;height: 30px; background: ' . $theme . '"> </div>';
            }, self::get_themes());
        }


        /**
         * Generate Document HTNL
         *
         * @return string
         */
        public static function mrFireDocs()
        {
            if (class_exists('MrAssistantDbConfig')) {
                return MrAssistantDbConfig::mrGenerateDocs();
            }
        }


        /**
         * Attach Realtime database rules in textarea field
         *
         * @return string
         */
        public static function mrFireRules()
        {
            if (class_exists('MrAssistantDbConfig')) {
                $file = MR_ASSISTANT_BASE_PATH . './assets/database/rules.json';
                if (file_exists($file)) {
                    $rules = file_get_contents($file);
                    return MrAssistantDbConfig::mrGenerateRules($rules);
                }
            }
        }
    }
endif;
