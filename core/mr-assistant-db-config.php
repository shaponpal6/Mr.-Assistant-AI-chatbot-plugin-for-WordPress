<?php

/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('MrAssistantDbConfig')) :


    /**
     * This Class will generate HTML document of the process of
     * realtime database configuration.
     *
     * @category   MrAssistantDbConfig
     * @package    WordPress
     * @subpackage Mr_Assistant
     * @author     Shapon pal <helpmrassistant@gmail.com>
     * @Version    1.0
     */
    class MrAssistantDbConfig
    {

        /**
         * This function will generate the process of
         * realtime database configuration of Mr. Assistant.
         *
         * @return string - html
         */
        public static function mrGenerateDocs()
        {
            $html = '';
            // make docs
            $html .= '<div class="mr-assistant-docs">';
            $html .= '<h2>' . __('How to set up Realtime Database', 'mr-assistant') . '</h2>';
            $html .= '<ul>';
            foreach (self::mrProcesses() as $row) {
                $html .= '<li>' . $row . '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';

            return $html;
        }


        /**
         * This function will generate the field of
         * realtime database rules.
         *
         * @param string $rules - Firebase database rules
         *
         * @return string - html
         */
        public static function mrGenerateRules($rules = '')
        {
            $html = '';
            $html .= '<div class="mr-assistant-rules">';
            $html .= '<p>' . __('Copy this security rules and paste it in Firebase security rules field.', 'mr-assistant') . '</p>';
            $html .= '<p id="mrAssistantRulesStatus"></p>';
            $html .= '<textarea id="mrAssistantRules" readonly="">';
            $html .= ent2ncr($rules);
            $html .= '</textarea>';
            $html .= '</div>';

            return $html;
        }


        /**
         * All process of realtime database configuration
         *
         * @return array
         */
        public static function mrProcesses()
        {
            return array(
                sprintf(__('Create your new %s project', 'mr-assistant'), '<a href="https://console.firebase.google.com/" target="_blank"><img class="mr_assistant_firebase_icon" src="' . MR_ASSISTANT_BASE_URL . 'assets/images/firebase.svg' . '" alt="Firebase"> Firebase</a>'),
                sprintf(__('Click Settings %s and select Project Settings Option', 'mr-assistant'), '<span class="dashicons dashicons-admin-generic"></span>'),
                __('Copy Project ID and Web API key then paste to related Project ID* and Web API key* fields accordingly.', 'mr-assistant'),
                __('Now Go to the Authentication Section and select the Sign-in Methods tab.', 'mr-assistant'),
                __('Here you need to enable 2 Sign-in Methods – Google and Anonymous methods.', 'mr-assistant'),
                __('Then find authorized domains on the same page below and add your Domain name here.', 'mr-assistant'),
                __('Now click the Database menu and create `Realtime Database` of your Firebase project.', 'mr-assistant'),
                sprintf(__('Go to the %s tab and replace this Security Rules from attached below.', 'mr-assistant'), '<a href="https://console.firebase.google.com/project/_/database/my-domain-name/rules" target="_blank">' . __('Database Rules', 'mr-assistant') . '</a>'),
                __('Almost Done!! Now go to your chat control panel and sign-in with your Google Account.', 'mr-assistant')
            );
        }
    }
endif;
