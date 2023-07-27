<?php 

class UT_Event_Filter {

    private static $_instance = null;

    public static function get_instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {

        if ( wp_doing_ajax() ) {
            add_action( 'wp_ajax_filter', [$this, 'filter_handler'] );
            add_action( 'wp_ajax_nopriv_filter', [$this, 'filter_handler'] );
        }

        add_action( 'init', [$this, 'catalog_rewrite'] );
        add_filter( 'query_vars', [$this, 'query_vars'] );

    }

    function query_vars($vars) {

        $vars[] = 'filter';

        return $vars;
    }

    function prepare_url($filters, $type = 'filter') {

        

        $redirect = ut_get_permalik_by_template('template-filter.php');
        $paged = $filters['paged'];
        $part = [];

        unset($filters['filter_type']);
        unset($filters['current_url']);
        unset($filters['paged']);

        foreach ($filters as $slug => $filter) {

            $filter = array_filter($filter);
            
            if ( ! $filter ) {
                continue;
            }
            
            $part[] = $slug . '-in-' . implode('-', $filter);
        }

        if ( ! empty($part) ) {
            $redirect .= implode('-and-', $part) . '/';
        }

        if ( $type == 'filter' && $paged > 1 ) {
            $redirect .= 'page/' . $paged . '/';
        }

        return $redirect;
    }

    public function get_params() {

        $var = get_query_var('filter');
        $paged = get_query_var('paged') ?: 1;
        $apply = [];
        // $var = size-in-small-large-and-color-in-white-black
        if (!empty($var)) {
            // $parts = [size-in-small-large, color-in-white-black]
            $parts = explode('-and-', $var);
            foreach ($parts as $part) {
                // $temp = [size, small-large]
                $temp = explode('-in-', $part);
                // $tmp = [small, large]
                $tmp = explode('-', $temp[1]);
                foreach ($tmp as $item) {
                    // $apply[$temp[0]][$item] = 1;
                    $apply[ $temp[0] ][] = $item;
                }
            }
        }

        $apply['paged'] = $paged;

        return $apply;
    }

    public function catalog_rewrite() {

        add_rewrite_rule(
            'filter/([-_a-zA-Z0-9]+)/page/(\d+)/?$',
            'index.php?pagename=filter&filter=$matches[1]&paged=$matches[2]',
            'top'
        );

        add_rewrite_rule(
            'filter/([-_a-zA-Z0-9]+)/?$',
            'index.php?pagename=filter&filter=$matches[1]',
            'top'
        );
    }
    
    public function filter_handler() {

        check_ajax_referer('ut_check', 'ajax_nonce');
        parse_str($_POST['form'], $form);

        $events_html = '';
        $pagination_html = '';
        $per_page = get_option('posts_per_page');
        $args = $this->get_args_filter($form, 'filter'); // get query arguments
        $url = $this->prepare_url($form); // url for update browser
        $pagination_url = $this->prepare_url( $params, 'pagination' ); // url for pagination
        $query = new WP_Query($args);
        $post_count = ((int)$form['paged'] - 1) * (int)$per_page + (int)$query->post_count; // number of events shown along with previous pages

        ob_start();
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                get_template_part('template-parts/content', 'event');
            }
        } else {
            echo '<h3>' . __('No results were found for your parameters.', 'unreal-themes') . '</h3>';
        }
        $events_html = ob_get_clean();

        ob_start();
        $GLOBALS['wp_query'] = $query; // for custom template
        the_posts_pagination( [
            'base' => $pagination_url . 'page/%#%/',
        ] );
        $pagination_html = ob_get_clean();

        wp_reset_query();

        wp_send_json_success([
            'events_html' => $events_html,
            'pagination_html' => $pagination_html,
            'count_posts' => $post_count,
            'found_posts' => $query->found_posts,
            'url' => $url,
        ]);
    }

    public function get_args_filter($data, $type = 'filter') {

        $args['paged'] = $data['paged'];
        $args['orderby'] = 'menu_order title';
        $args['order'] = 'ASC';
        $args['post_status'] = 'publish';
        $args['post_type'] = 'event';

        if (isset($data['years']) && !empty($data['years'])) {
            $args['meta_query'][] = [
                [
                    'key' => '_year',
                    'value' => $data['years'],
                    'compare' => 'IN'
                ]
            ];
        }
       
        if (isset($data['months']) && !empty($data['months'])) {
            $args['meta_query'][] = [
                [
                    'key' => '_month',
                    'value' => $data['months'],
                    'compare' => 'IN'
                ]
            ];
        }

        if (isset($data['location'])) {
            $data['location'] = array_filter($data['location']);
        }

        if (isset($data['location']) && !empty($data['location'])) {
            $args['tax_query'][] = [
                [
                    'taxonomy' => 'location',
                    'field' => 'slug',
                    'terms' => $data['location'],
                ]
            ];
        }

        return $args;
    }

} 