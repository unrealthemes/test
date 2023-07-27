<?php

class UT_Event {

    private static $_instance = null;

    static public $months = [
        'January', 
        'February', 
        'March', 
        'April', 
        'May', 
        'June', 
        'July', 
        'August', 
        'September', 
        'October', 
        'November',
        'December'
    ];

    public static function get_instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {

        add_action( 'init', [$this, 'custom_post_type'], 0 );
        add_action( 'init', [$this, 'register_taxonomy'] );
        add_action( 'add_meta_boxes', [$this, 'add_custom_box'] );
        add_action( 'save_post', [$this, 'save_postdata'] );
    }

    function custom_post_type() {
  
        $labels = array(
            'name'                => _x( 'Events', 'Post Type General Name', 'unreal-themes' ),
            'singular_name'       => _x( 'Event', 'Post Type Singular Name', 'unreal-themes' ),
            'menu_name'           => __( 'Events', 'unreal-themes' ),
            'parent_item_colon'   => __( 'Parent Event', 'unreal-themes' ),
            'all_items'           => __( 'All Events', 'unreal-themes' ),
            'view_item'           => __( 'View Event', 'unreal-themes' ),
            'add_new_item'        => __( 'Add New Event', 'unreal-themes' ),
            'add_new'             => __( 'Add New', 'unreal-themes' ),
            'edit_item'           => __( 'Edit Event', 'unreal-themes' ),
            'update_item'         => __( 'Update Event', 'unreal-themes' ),
            'search_items'        => __( 'Search Event', 'unreal-themes' ),
            'not_found'           => __( 'Not Found', 'unreal-themes' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'unreal-themes' ),
        );
              
        $args = array(
            'label'               => __( 'Events', 'unreal-themes' ),
            'description'         => __( 'Event news and reviews', 'unreal-themes' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'excerpt', 'thumbnail' ), 
            'taxonomies'          => array( 'year', 'month', 'location' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
        
        );
            
        register_post_type( 'event', $args );
          
    }

    function register_taxonomy(){
        
        register_taxonomy( 
            'location', 
            [ 'event' ], 
            [
                'label'                 => '', 
                'labels'                => [
                    'name'              => 'Locations',
                    'singular_name'     => 'Location',
                    'search_items'      => 'Search Locations',
                    'all_items'         => 'All Locations',
                    'view_item '        => 'View Location',
                    'parent_item'       => 'Parent Location',
                    'parent_item_colon' => 'Parent Location:',
                    'edit_item'         => 'Edit Location',
                    'update_item'       => 'Update Location',
                    'add_new_item'      => 'Add New Location',
                    'new_item_name'     => 'New Location Name',
                    'menu_name'         => 'Location',
                    'back_to_items'     => '← Back to Location',
                ],
                'description'           => '', 
                'public'                => true,
                'hierarchical'          => true,
                'rewrite'               => true,
                'capabilities'          => [],
                'meta_box_cb'           => null, 
                'show_admin_column'     => false, 
                'show_in_rest'          => null, 
                'rest_base'             => null, 
            ] 
        );
    }

    public static function get_years_array() {

        $years = array_combine( range(date("Y"), 2030), range(date("Y"), 2030) );

        return $years;
    }

    
    function add_custom_box() {

        $screens = array( 'event' );
        add_meta_box( 'ut_sectionid', 'Settings', [$this, 'meta_box_callback'], $screens );
    }

    function meta_box_callback( $post, $meta ) {

        $screens = $meta['args'];
    
        wp_nonce_field( plugin_basename(__FILE__), 'ut_noncename' );
    
        $years = self::get_years_array();
        $months = self::$months;

        $year_val = get_post_meta( $post->ID, '_year', true );
        $month_val = get_post_meta( $post->ID, '_month', true );

        echo '<div class="ut-meta-wrapper">';
            echo '<label for="ut_year">' . __("Year", 'unreal-theme' ) . '</label> ';
            echo '<select id="ut_year" name="ut_year">';
                foreach ( $years as $year ) {
                    echo '<option value="' . $year . '" ' . selected( $year, $year_val ) . '>' . $year . '</option>';
                }
            echo '</select>';
        echo '</div>';
        
        echo '<div class="ut-meta-wrapper">';
            echo '<label for="ut_month">' . __("Month", 'unreal-theme' ) . '</label> ';
            echo '<select id="ut_month" name="ut_month">';
                foreach ( $months as $month ) {
                    echo '<option value="' . $month . '" ' . selected( $month, $month_val ) . '>' . $month . '</option>';
                }
            echo '</select>';
        echo '</div>';
    }

    function save_postdata( $post_id ) {

        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        if ( ! isset($_POST['ut_year']) || ! isset($_POST['ut_month']) ) {
            return;
        }

        $year = sanitize_text_field( $_POST['ut_year'] );
        $month = sanitize_text_field( $_POST['ut_month'] );

        update_post_meta( $post_id, '_year', $year );
        update_post_meta( $post_id, '_month', $month );
    }
    

} 