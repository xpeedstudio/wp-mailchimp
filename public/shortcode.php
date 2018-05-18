<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Wp_Mailchimp_Shortcode{

    public static $_instance;

    public $plugin_version = '1.0';

    public $base;

    public $file = __FILE__;


    public function __construct(){
        $this->env_init_shortcode();
    }

    public function env_init_shortcode(){
        add_shortcode('wp_mailchimp', array($this,'xs_show_shortcode'));
    }

    public function xs_show_shortcode($atts, $content = NULL){

        extract(shortcode_atts(
                array(
                    'id' => '',
                ), $atts)
        );
        $api_key = wp_mailchimp_get_option('wp_mailchimp_api_key','mailchimp');
        $list_id = wp_mailchimp_get_option('wp_mailchimp_list','mailchimp');
        ob_start();
        ?>
        <form action="#" method="POST" class="xs-newsletter">
            <label for="xs-newsletter-email"></label>
            <input type="email" name="email" id="xs-newsletter-email" class="xs-newsletter-email" placeholder="Enter your email....">
            <input type="text" name="name" id="xs-newsletter-name" class="xs-newsletter-name" placeholder="Enter your name....">
            <input type="submit" value="subscribe" class="xs_mailchimp_submit">
            <input type="hidden" name="list_id" id="xs_list_id" class="xs_list_id" value="<?php echo esc_attr($list_id); ?>">
        </form>
        <?php
        return ob_get_clean();
    }

    public static function xs_instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new Wp_Mailchimp_Shortcode();
        }
        return self::$_instance;
    }

}

$env_instance = Wp_Mailchimp_Shortcode::xs_instance();

?>