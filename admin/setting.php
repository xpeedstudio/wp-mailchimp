<?php

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */

if ( !class_exists('Wp_Social_Menu_Settings' ) ):
    class Wp_Social_Menu_Settings {
        private $settings_api;
        function __construct() {
            $this->settings_api = new WeDevs_Settings_API;
            add_action( 'admin_init', array($this, 'admin_init') );
            add_action( 'admin_menu', array($this, 'admin_menu') );
        }
        function admin_init() {
            //set the settings
            $this->settings_api->set_sections( $this->get_settings_sections() );
            $this->settings_api->set_fields( $this->get_settings_fields() );
            //initialize settings
            $this->settings_api->admin_init();
        }
        function admin_menu() {
            add_menu_page( 'WP Mailchimp', 'WP Mailchimp', 'edit_theme_options', 'wp-mailchimp', array($this, 'plugin_page') );
            add_submenu_page('wp-mailchimp','Subscribe List','Mail List','edit_theme_options','xs-subscribe-list',array($this, 'subscribe_list'));
        }
        function get_settings_sections() {
            $sections = array(
                array(
                    'id'    => 'mailchimp',
                    'title' => __( 'MailChimp API Settings', 'wp-fundraising' )
                ),
                
            );
            return $sections;
        }
        /**
         * Returns all the settings fields
         *
         * @return array settings fields
         */
        function get_settings_fields() {
            $settings_fields = array(
                'mailchimp' => array(
                    array(
                        'name'  => 'wp_mailchimp_api_key',
                        'label' => esc_html__( 'Api Key', 'wp-mailchimp' ),
                        'desc'  => esc_html__( 'Enter mailchimp api key', 'wp-mailchimp' ),
                        'type'  => 'text'
                    ),
                    array(
                        'name'      => 'wp_mailchimp_list',
                        'label'     => __( 'List', 'wp-mailchimp' ),
                        'desc'      => __( 'Select your list.', 'wp-mailchimp' ),
                        'type'      => 'select',
                        'options'   => xs_wp_mailchimp_list(),
                    ),

                    array(

                        'desc'      => '<input type="button"  class="button button-primary xs-mail-list" value="Load Mail">',
                        'type'      => 'html',
                    ),
                ),

            );
            return $settings_fields;
        }
        function plugin_page() {
            $this->settings_api->show_navigation();
            $this->settings_api->show_forms();
        }

        function subscribe_list(){
            ?>
            <div class="wrap">
                <form method="get">
                    <input type="hidden" name="page" value="xs-subscribe-list">
                    <?php
                    $mail_list = new Xs_Subscribe_List_Table();
                    $mail_list->prepare_items();
                    $mail_list->search_box(__('Search Forms', 'wp-mailchimp'), 'wp-mailchimp-search');
                    $mail_list->display();
                    ?>
                </form>
            </div>
<?php

        }
        /**
         * Get all the pages
         *
         * @return array page names with key value pairs
         */
        function get_pages() {
            $pages = get_pages();
            $pages_options = array();
            if ( $pages ) {
                foreach ($pages as $page) {
                    $pages_options[$page->ID] = $page->post_title;
                }
            }
            return $pages_options;
        }
    }
endif;
new Wp_Social_Menu_Settings();