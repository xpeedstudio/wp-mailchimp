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

        $per_page              = 5;
        $current_page          = $this->get_pagenum();
        $offset                = ( $current_page - 1 ) * $per_page;

        $args = array(
            'offset'         => $offset,
            'posts_per_page' => $per_page,
        );

        if ( isset( $_GET['s'] ) && !empty( $_GET['s'] ) ) {
            $args['s'] = $_GET['s'];
        }

        if ( isset( $_GET['post_status'] ) && !empty( $_GET['post_status'] ) ) {
            $args['post_status'] = $_GET['post_status'];
        }

        if ( isset( $_GET['orderby'] ) && !empty( $_GET['orderby'] ) ) {
            $args['orderby'] = $_GET['orderby'];
        }

        if ( isset( $_GET['order'] ) && !empty( $_GET['order'] ) ) {
            $args['order'] = $_GET['order'];
        }

        $items =  $this->maillist_get_item($args);

        $this->items = $items['maillist'];

        $this->set_pagination_args( array(
            'total_items' => $items['count'],
            'per_page'    => $per_page
        ) );

    }

    public function maillist_get_item($args){

        $default = array(
            'post_type'   => 'xs_wp_mailchimp',
            'post_status'  => 'any',
            'orderby'     => 'DESC',
            'order'       => 'ID',
        );

        $args = array_merge($default,$args);

        $query = new WP_Query( $args );

        $mils_lists = array();

        if ( $query->have_posts() ) {
            $i = 0;
            while ( $query->have_posts() ) {
                $query->the_post();
                $mail_list = $query->posts[$i];
                $mils_lists[ $i ] = array(
                    'post_id'            	=> $mail_list->ID,
                    'post_title'            => $mail_list->post_title,
                    'post_status'           => $mail_list->post_status,
                    'post_date'             => $mail_list->post_date,
                );
                $i++;
            }
        }
        $count = $query->found_posts;
        wp_reset_postdata();

        return array(
            'maillist' => $mils_lists,
            'count' => $count
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
            'post_date'      	=> 'Date'
        );
        return $columns;
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
        return sprintf( '<input type="checkbox" name="maillist_post[]" value="%d" />', $item['post_title'] );
    }

    /**
     * Decide which action is currently performing
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function current_action() {

        if ( isset( $_GET['wpmail_form_search'] ) ) {
            return 'wpmail_form_search';
        }

        return parent::current_action();
    }


    /**
     * List table search box
     *
     * @since 1.0.0
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
            <?php submit_button( $text, 'button', 'wpmail_form_search', false, array( 'id' => 'search-submit' ) ); ?>
        </p>
        <?php
    }
}