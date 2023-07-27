<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package unreal-themes
 */

?>

<section class="no-results not-found">

	<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'unreal-themes' ); ?></h1>

	<div class="page-content">

		<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'unreal-themes' ); ?></p>
		
		<?php get_search_form(); ?>

	</div>

</section>
