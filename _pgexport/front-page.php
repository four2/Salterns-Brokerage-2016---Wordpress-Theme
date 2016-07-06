<?php
get_header(); ?>

<div>
    <a href="http://pinegrow.com/">
        <div class="designed-with">
            <?php _e( 'Designed with Pinegrow', 'sb2016' ); ?>
            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/pg_logo.svg" class="designed-with-image">
        </div>
    </a>
</div>
<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-9">
                <?php if ( have_posts() ) : ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php get_template_part( 'template-parts/content-page' ); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p><?php _e( 'Sorry, no posts matched your criteria.', 'sb2016' ); ?></p>
                <?php endif; ?>
                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
            </div>
            <div class="col-sm-3">
                <?php if ( is_active_sidebar( 'right_sidebar' ) ) : ?>
                    <aside id="main_sidebar">
                        <?php dynamic_sidebar( 'right_sidebar' ); ?>
                    </aside>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>                

<?php get_footer(); ?>