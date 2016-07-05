<?php
get_header(); ?>

<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-9">
                <?php if ( have_posts() ) : ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php get_template_part( 'template-parts/content-single' ); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p><?php _e( 'Sorry, no posts matched your criteria.', 'sb2016' ); ?></p>
                <?php endif; ?>
                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
                <?php if ( have_posts() ) : ?>
                    <ul class="pager posts-navigation text-uppercase">
                        <li class="previous">
                            <?php previous_post_link( '%link', __( '%title', 'sb2016' ) ); ?>
                        </li>
                        <li class="next">
                            <?php next_post_link( '%link', __( '%title', 'sb2016' ) ); ?>
                        </li>
                    </ul>
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