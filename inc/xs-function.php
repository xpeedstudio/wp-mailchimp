<?php
/**
 *
 * public Global Form subcriptions
 *
 */

add_action('wp_ajax_nopriv_user_xs_subscribe_form', 'user_xs_subscribe_form');
add_action('wp_ajax_user_xs_subscribe_form', 'user_xs_subscribe_form');

function user_xs_subscribe_form()
{

    check_ajax_referer('xs_security_check', 'xs_security');
    if (defined('DOING_AJAX') && DOING_AJAX) {

        $name = $_POST['name'];
        $email = $_POST['email'];
        $list_id = $_POST['xs_list_id'];
        $apiKey = wp_mailchimp_get_option('wp_mailchimp_api_key', 'mailchimp');
        $MailChimp = new MailChimp($apiKey);
        $result = $MailChimp->post("lists/" . $list_id . "/members", [
            'email_address' => $email,
            'merge_fields' => ['FNAME' => $name, 'LNAME' => ''],
            'status' => 'subscribed',
        ]);
        if (is_array($result) && !empty($result)) {
            if ($result['status'] == 400) {
                echo $result['title'];
            } elseif ($result['status'] == 'subscribed') {
                echo 'success';
            }
        } else {
            echo 'please configure your mailchimp setting';
        }
        wp_die();
    }
}

if (!function_exists('xs_wp_mailchimp_get_option')) {
    function wp_mailchimp_get_option($option, $section, $default = '')
    {

        $options = get_option($section);

        if (isset($options[$option])) {
            return $options[$option];
        }

        return $default;
    }
}

if (!function_exists('xs_wp_mailchimp_list')) {
    function xs_wp_mailchimp_list()
    {
        $api_key = wp_mailchimp_get_option('wp_mailchimp_api_key', 'mailchimp');
        $MailChimp = new MailChimp($api_key);
        $lists = $MailChimp->get('lists');
        if (isset($lists['status']) && $lists['status'] == 401) {
            add_action('admin_notices', 'wp_mailchimp_notice');
            $xs_list[0] = esc_html__('Select List', 'wp-mailchimp');
        } else {
            $xs_list = array();
            $xs_list[0] = esc_html__('Select List', 'wp-mailchimp');
            if (is_array($lists) && count($xs_list) > 0) {
                foreach ($lists['lists'] as $key => $list) {
                    $xs_list[$list['id']] = $list['name'];
                }
            }

        }
        return $xs_list;
    }
}
if (!function_exists('wp_mailchimp_notice')) {
    function wp_mailchimp_notice()
    {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo esc_html__('Your API key may be invalid, or you\'ve attempted to access the wrong datacenter.!', 'sample-text-domain'); ?></p>
        </div>
        <?php
    }
}
