<?php
/*
 Template Name: News Template 2
 */
?>
<?php
get_header(); ?>

<div class="container-fluid">
    <div class="row">
        <div class="well col-md-10 col-md-offset-1">
            <?php _e( 'Latest News Category Template', 'brokerage_2016' ); ?>
        </div>
    </div>
</div>
<section class="">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-9">
                <?php if ( have_posts() ) : ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php get_template_part( 'template-parts/content-home' ); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p><?php _e( 'Sorry, no posts matched your criteria.', 'brokerage_2016' ); ?></p>
                <?php endif; ?>
                <ul class="pager posts-navigation text-uppercase">
                    <?php if ( get_next_posts_link() ) : ?>
                        <li class="previous">
                            <?php next_posts_link( 'Older Posts' ); ?>
                        </li>
                    <?php endif; ?>
                    <?php if ( get_previous_posts_link() ) : ?>
                        <li class="next">
                            <?php previous_posts_link( __( 'Newer Posts', 'brokerage_2016' ) ); ?>
                        </li>
                    <?php endif; ?>
                </ul>
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