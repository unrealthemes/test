<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 */



/**
 * Get permalink by template name
 */

function ut_get_permalik_by_template( $template ) {

	$result = '';

	if ( ! empty( $template ) ) {
		$pages = get_pages( [
		    'meta_key'   => '_wp_page_template',
		    'meta_value' => $template
		] );
		$template_id = $pages[0]->ID;
		$page = get_post( $template_id );
		$result = get_permalink( $page );
	}
	
	return $result;
}



/**
 * Get permalink by template name
 */

function ut_get_page_id_by_template( $template ) {

	$result = '';

	if ( ! empty( $template ) ) {
		$pages = get_pages( [
		    'meta_key'   => '_wp_page_template',
		    'meta_value' => $template
		] );
		$result = $pages[0]->ID;
	}
	
	return $result;
}



/**
 * Get name menu by location
 */

function ut_get_title_menu_by_location( $location ) {

    if ( empty( $location ) ) {
    	return false;
	}
    $locations = get_nav_menu_locations();

    if ( ! isset( $locations[ $location ] ) ) {
    	return false;
	}
    $menu_obj = get_term( $locations[ $location ], 'nav_menu' );

    return $menu_obj->name;
}



/** 
 * Admin footer modification
 */   

function ut_remove_footer_admin() {

    echo '<span id="footer-thankyou">Тема разработана <a href="https://unrealthemes.site/" target="_blank"><img src="' . THEME_URI . '/img/unreal.png" width="130"/></a></span>';
}
add_filter('admin_footer_text', 'ut_remove_footer_admin');



/** 
 * Add options page ACF pro
 */ 

// if ( function_exists('acf_add_options_page') ) {
// 	acf_add_options_page([
// 		'page_title'    => __('Настройки темы'),
// 		'menu_title'    => __('Настройки темы'),
// 		'menu_slug'     => 'acf-options',
// 	]);
// }


/**
 * Custom excerpt
 */

// add_filter( 'excerpt_length', function() {
// 	return 23;
// } );

// add_filter('excerpt_more', function( $more ) {
// 	return '...';
// });



/**
 *
 */

function ut_format_size_units( $bytes ) {

    if ( $bytes >= 1073741824 ) {
        $bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
    } elseif ( $bytes >= 1048576 ) {
        $bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
    } elseif ( $bytes >= 1024 ) {
        $bytes = number_format( $bytes / 1024, 2 ) . ' KB';
    } elseif ( $bytes > 1 ) {
        $bytes = $bytes . ' bytes';
    } elseif ( $bytes == 1 ) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}



/**
 *	Remove prefix in default mime type ( application/pdf = pdf )
 */

function ut_mime_type_without_application( $default_mime_type ) {

	$mime_type = '';
	$remove_txts = array( 'application/', 'video/', 'image/', 'audio/' );

	foreach( $remove_txts as $remove_txt ) {

		if ( strstr( $default_mime_type, $remove_txt ) ) {
			$mime_type = str_replace( $remove_txt, "", $default_mime_type );
		}
	}

	return $mime_type;
}



/**
 * Remove feature image and comment for post type "page"
 */

function ut_cpt_support() {
    remove_post_type_support( 'page', 'thumbnail' );
    remove_post_type_support( 'page', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}
// add_action( 'admin_init', 'ut_cpt_support' );



/**
 * Cancel the display of the selected term at the top in the checkbox list of terms
 */

function ut_set_checked_ontop_default( $args ) {
	// change the default parameter to false
	if ( ! isset( $args['checked_ontop'] ) ) {
		$args['checked_ontop'] = false;
	}

	return $args;
}
// add_filter( 'wp_terms_checklist_args', 'ut_set_checked_ontop_default', 10 );



function ut_class_names( $classes ) {

	if ( is_page_template('template-home.php') ) {
		$classes[] = 'home';
	} elseif ( is_singular('post') || is_page_template('template-blog.php') ) {
		$classes[] = 'blog';
	} elseif ( is_singular('product') ) {
		$classes[] = 'curs_page';
	}

	return $classes;
}
// add_filter( 'body_class','ut_class_names' );



function ut_remove_product_editor() {
    remove_post_type_support( 'product', 'editor' );
}
add_action( 'init', 'ut_remove_product_editor' );



/**
 * Declension of a word after a number
 *
 *     // Call examples:
 *     ut_num_decline( $num, 'книга,книги,книг' )
 *     ut_num_decline( $num, 'book,books' )
 *     ut_num_decline( $num, [ 'книга', 'книги', 'книг' ] )
 *     ut_num_decline( $num, [ 'book', 'books' ] )
 *
 * @param int|string   $number       The number followed by the word. You can specify a number in HTML tags.
 * @param string|array $titles       Declension options or first word for a multiple of 1.
 * @param bool         $show_number  Specify 00 here when you do not need to display the number itself.
 *
 * @return string For example: 1 книга, 2 книги, 10 книг.
 *
 * @version 3.1
 */
function ut_num_decline( $number, $titles, $show_number = true ) {

	if( is_string( $titles ) ){
		$titles = preg_split( '/, */', $titles );
	}

	// когда указано 2 элемента
	if( empty( $titles[2] ) ){
		$titles[2] = $titles[1];
	}

	$cases = [ 2, 0, 1, 1, 1, 2 ];

	$intnum = abs( (int) strip_tags( $number ) );

	$title_index = ( $intnum % 100 > 4 && $intnum % 100 < 20 )
		? 2
		: $cases[ min( $intnum % 10, 5 ) ];

	return ( $show_number ? "$number " : '' ) . $titles[ $title_index ];
}



function ut_branding_login() { 

    $logo_id = get_field('logo_header', 'options');
    $logo_url = wp_get_attachment_url( $logo_id, 'full' );
    echo '<style>
			.login h1 a {
                background-image: url(' . $logo_url . ') !important;
                width: 100% !important;
                background-position: center !important;
                background-size: contain !important;
            }
          </style>';
    
}
// add_action( 'login_enqueue_scripts', 'ut_branding_login' );



function ut_custom_login_url( $url ) {
    return home_url();
}
// add_filter( 'login_headerurl', 'ut_custom_login_url' );