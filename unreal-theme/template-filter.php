<?php
/**
 * Template name: Filter
 */

get_header(); 

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443 ? "https://" : "http://";
$page_url = "{$protocol}{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$params = ut_help()->event_filter->get_params(); // get url parameters 
$pagination_url = ut_help()->event_filter->prepare_url( $params, 'pagination' ); // url for pagination
$args = ut_help()->event_filter->get_args_filter( $params, 'filter' ); // get query arguments
$query = new WP_Query( $args );
?>

<section class="catalog-content">
    <div class="container">
		<div class="ut-loader"></div>
        <div class="catalog-header">
            <h2 class="catalog-title"><?php _e('Catalog', 'unreal-themes'); ?></h2>
            <a href="#" class="catalog-trigger js-open-filter"><?php _e('Filters', 'unreal-themes'); ?></a>
        </div>
        <form id="filter_form" action="" method="post">

			<input type="hidden" id="filter_type" name="filter_type" value="filter">
			<input type="hidden" id="paged" name="paged" value="<?php echo get_query_var( 'paged' ) ?: 1; ?>">
			<input type="hidden" id="current_url" name="current_url" value="<?php echo $page_url; ?>">

            <div class="catalog-body">

                <aside class="catalog-filter js-filter">
                    <div class="filter">
                        <div class="filter__body">

                            <?php 
							get_template_part( 
								'template-parts/location', 
								null, 
								[
									'params' => $params,
								] 
							); 
							?>
                           
                           <?php 
							get_template_part( 
								'template-parts/years', 
								null, 
								[
									'params' => $params,
								] 
							); 
							?>
                           
                           <?php 
							get_template_part( 
								'template-parts/months', 
								null, 
								[
									'params' => $params,
								] 
							); 
							?>

                        </div>
                    </div>
                </aside>

                <div class="cards catalog-cards">
                    <?php
                    if ( $query->have_posts() ) :
                        while ( $query->have_posts() ) :
                            $query->the_post();
                            get_template_part('template-parts/content', 'event');
                        endwhile;
                    else :
						echo '<h3>' . __('No results were found for your parameters.', 'unreal-themes') . '</h3>';
                    endif;
                    ?>
                </div>

            </div>
        </form>

        <div class="catalog-footer text-center product-pagination">
			<div class="js-pagination">
				<?php
				$GLOBALS['wp_query'] = $query; // for custom template
				the_posts_pagination( [
					'base' => $pagination_url . 'page/%#%/',
				] );
				wp_reset_query();
				?>
			</div>
		</div>

    </div>
</section>

<?php 
get_footer();