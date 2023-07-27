<div class="cards__item">
    <div class="card">
        <a href="<?php the_permalink(); ?>" class="card__preview" title="<?php the_title(); ?>">
            <div class="card__image">
                <?php echo get_the_post_thumbnail( $post->ID, 'full'); ?>
            </div>
        </a>
        <a href="<?php the_permalink(); ?>" class="card__info" title="<?php the_title(); ?>">
            <h2 class="card__title">
                <span class="card__title-link">
                    <?php the_title(); ?>
                </span>
                <div class="card__title-excerpt">
                    <?php the_excerpt(); ?>
                </div>
            </h2>
        </a>   
    </div>
</div>