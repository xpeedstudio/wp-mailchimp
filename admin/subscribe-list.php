<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Xs_Subscribe_List_Table extends WP_List_Table {

    /**
     * Class constructor
     *
     * @since 2.5
     *
     * @return void
     */

    public function __construct() {
        global $status, $page, $page_status;

        parent::__construct( array(
            'singular' => 'xs-subscribe-list',
            'plural'   => 'xs-subscribe-lists',
            'ajax'     => false
        ) );

    }


    /**
     * Prepare table data
     *
     * @since 2.5
     *
     * @return void
     */


    public function prepare_items() {
        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $per_page              = 1000;
        if ( isset( $_GET['s'] ) && !empty( $_GET['s'] ) ) {
            $args['s'] = $_GET['s'];
        }

        $items =  $this->env_get_item();
        $totalitems = count( $items['maillist']);

        $currentPage = $this->get_pagenum();
        $data = array_slice($items,(($currentPage-1)*$per_page ),$per_page );

        $this->items = $data['maillist'];

        $this->set_pagination_args( array(
            'total_items' => $totalitems,
            'per_page'    => $per_page
        ) );

    }

    public function env_get_item(){

        $api_key = wp_mailchimp_get_option('wp_mailchimp_api_key','mailchimp');
        $list_id = wp_mailchimp_get_option('wp_mailchimp_list','mailchimp');
        $MailChimp = new MailChimp($api_key);
        $lists = $MailChimp->get("lists/" . $list_id . "/members");
        $mail_list = array();

        if (isset($lists['status']) && $lists['status'] == 401) {
            add_action('admin_notices', 'wp_mailchimp_notice');
            $xs_list[0] = esc_html__('Select List', 'wp-mailchimp');
        } else {
            if(is_array($lists) && !empty($lists)){
                $i = 0;
                foreach($lists['members'] as $list){
                    $mail_list[ $i ] = array(
                        'post_title'            =>$list['email_address'],
                        'post_date'             => $list['status'],
                    );
                    //if($i == 2) break;
                    $i++;
                }
            }
        }

        return array(
            'maillist' => $mail_list,
        );
    }

    public function get_sortable_columns() {
        $sortable_columns = array(
            'post_date'  => array('post_date',false),
        );
        return $sortable_columns;
    }



    public function get_columns(){
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'post_title'		=> 'Email',
            'post_date'      	=> 'Status'
        );
        return $columns;
    }

    public function column_post_title($item) {

        $admin_url      = admin_url( 'admin.php?page=xs-subscribe-list&id=' . $item['post_title']  );

        $trash_url      = $admin_url . '&action=unsubscribe';

        $actions['unsubscribe'] = sprintf('<a href="%s">%s</a>',$trash_url, esc_html__('Unsubscribe', 'env'));

        return sprintf('%1$s %2$s', $item['post_title'], $this->row_actions($actions) );
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'post_title':
            case 'post_date':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Checkbox column value
     *
     * @since 2.5
     *
     * @param array $item
     *
     * @return string
     */

    public function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="env_post[]" value="%d" />', $item['post_title'] );
    }

    //    public function process_bulk_action() {
    // 	// Detect when a bulk action is being triggered.
    // 	wp_die( 'Items deleted (or they would be if we had items to delete)!' );
    // 	if ( 'delete' === $this->current_action() ) {
    // 		wp_die( 'Items deleted (or they would be if we had items to delete)!' );
    // 	}
    // }

    public function get_bulk_actions() {
        if ( ! isset( $_GET['post_status'] ) || 'trash' !== $_GET['post_status'] ) {
            $actions['trash'] = __( 'Move to Trash', 'wpuf' );
        }

        if ( isset( $_GET['post_status'] ) && 'trash' === $_GET['post_status'] ) {
            $actions['restore'] = __( 'Restore', 'wpuf' );
            $actions['delete']  = __( 'Delete Permanently', 'wpuf' );
        }

        return $actions;
    }

    /**
     * Decide which action is currently performing
     *
     * @since 2.5
     *
     * @return string
     */
    public function current_action() {

        if ( isset( $_GET['env_form_search'] ) ) {
            return 'env_form_search';
        }
        if ( isset( $_GET['filter_action'] ) ) {
            return 'filter_action';
        }

        return parent::current_action();
    }


    /**
     * Top filters like All, Published, Trash etc
     *
     * @since 2.5
     *
     * @return array
     */
    public function get_views() {
        $status_links   = array();
        $post_status  = array(
            'all'       => __( 'All', 'wpuf' ),
            'publish'   => __( 'Published', 'wpuf' ),
            'trash'     => __( 'Trash', 'wpuf' )
        ) ;
        $current_status = isset( $_GET['post_status'] ) ? $_GET['post_status'] : 'all';

        $post_counts =  wp_count_posts( 'book' );

        foreach ( $post_status as $status => $status_title ) {
            $link = ( 'all' === $status ) ? admin_url( 'admin.php?page=env_books' ) : admin_url( 'admin.php?page=env_books&post_status=' . $status );

            if ( $status === $current_status ) {
                $class = 'current';
            } else {
                $class = '';
            }

            switch ( $status ) {
                case 'all':
                    $count = $post_counts->publish;
                    break;
                case 'publish':
                    $count = $post_counts->publish;
                    break;
                case 'trash':
                    $count = $post_counts->trash;
                    break;
            }

            $status_links[ $status ] = sprintf(
                '<a class="%s" href="%s">%s <span class="count">(%s)</span></a>',
                $class, $link, $status_title, $count
            );
        }

        return $status_links;
    }


    /**
     * List table search box
     *
     * @since 2.5
     *
     * @param string $text
     * @param string $input_id
     *
     * @return void
     */
    public function search_box( $text, $input_id ) {
        if ( empty( $_GET['s'] ) && ! $this->has_items() ) {
            return;
        }

        if ( ! empty( $_GET['orderby'] ) ) {
            echo '<input type="hidden" name="orderby" value="' . esc_attr( $_GET['orderby'] ) . '" />';
        }

        if ( ! empty( $_GET['order'] ) ) {
            echo '<input type="hidden" name="order" value="' . esc_attr( $_GET['order'] ) . '" />';
        }

        if ( ! empty( $_GET['post_status'] ) ) {
            echo '<input type="hidden" name="post_status" value="' . esc_attr( $_GET['post_status'] ) . '" />';
        }

        $input_id = $input_id . '-search-input';

        ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <?php submit_button( $text, 'button', 'env_form_search', false, array( 'id' => 'search-submit' ) ); ?>
        </p>
        <?php
    }
}