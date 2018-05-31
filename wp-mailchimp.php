<?php
/**
Plugin Name: Wp Mailchimp
Plugin URI:http://xpeedstudio.com
Description: Wp Mailchimp Features is a plugin.
Author: xpeedstudio
Author URI: http://xpeedstudio.com
Version:1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Wp_Mailchimp{

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     */

    public static $_instance;

    /**
     * Plugin Name
     *
     * @since 1.0.0
     *
     */

    public $plugin_name = 'Wp Mailchimp';

    /**
     * Plugin Version
     *
     * @since 1.0.0
     *
     */

    public $plugin_version = '1.0.0';

    /**
     * Plugin File
     *
     * @since 1.0.0
     *
     */

    public $file = __FILE__;

    /**
     * Load Construct
     *
     * @since 1.0.0
     */

    public function __construct(){
        $this->xs_plugin_init();
    }

    /**
     * Plugin Initialization
     *
     * @since 1.0.0
     *
     */

    public function xs_plugin_init(){

        require_once (plugin_dir_path($this->file). 'inc/MailChimp.php');
        require_once (plugin_dir_path($this->file). 'admin/setting-class.php');
        require_once (plugin_dir_path($this->file). 'admin/subscribe-list.php');
        require_once (plugin_dir_path($this->file). 'admin/setting.php');
        require_once (plugin_dir_path($this->file). 'inc/xs-function.php');
        require_once (plugin_dir_path($this->file). 'public/shortcode.php');
        add_action( 'init', array($this, 'register_post_type') );
        add_action( 'wp_enqueue_scripts', array( $this, 'xs_enque_script'));
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enque_script'));

    }

    public function register_post_type(){
        register_post_type( 'xs_wp_mailchimp', array(
            'public'          => false,
            'show_ui'         => false,
            'show_in_menu'    => false,
        ) );
    }
    public function xs_enque_script(){
        wp_enqueue_script( 'xs-wp-mailchimp-ajax', plugin_dir_url($this->file) . 'assets/js/main.js', array('jquery'), '', TRUE );

        /*Ajax Call*/
        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('xs_security_check'),
        );
        wp_localize_script('xs-wp-mailchimp-ajax', 'xs_check_obj', $params);
    }

    public function admin_enque_script(){
        wp_enqueue_script( 'xs-admin-ajax', plugin_dir_url($this->file) . 'assets/js/admin.js', array('jquery'), '', TRUE );

        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('xs_admin_security_check'),
        );
        wp_localize_script('xs-admin-ajax', 'xs_admin_check_obj', $params);
    }

    public static function xs_get_instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new Wp_Mailchimp();
        }
        return self::$_instance;
    }

}
$Xs_Main = Wp_Mailchimp::xs_get_instance();