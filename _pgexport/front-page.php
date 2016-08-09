<?php
get_header(); ?>

<div class="boat_make container-fluid col-md-12">
    <?php makeWidget() ?>
</div>
<div id="slider01" class="carousel slide header-carousel" data-ride="carousel" data-pause="hover" data-interval="20000">
    <!-- Bulles -->
    <ol class="carousel-indicators">
        <?php
            $sliderloop_args = array(
                'category_name' => 'Home Slider',
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => '10',
                'order' => 'ASC',
                'orderby' => 'modified'
            )
        ?>
        <?php $sliderloop = new WP_Query( $sliderloop_args ); ?>
        <?php if ( $sliderloop->have_posts() ) : ?>
            <?php $sliderloop_item_number = 0; ?>
            <?php while ( $sliderloop->have_posts() ) : $sliderloop->the_post(); ?>
                <li data-target="#slider01" data-slide-to="<?php echo $sliderloop_item_number ?>" class="<?php if( $sliderloop_item_number == 0) echo 'active'; ?>"></li>
                <?php $sliderloop_item_number++; ?>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>
    </ol>
    <div class="carousel-inner">
        <!-- Page 1 -->
        <?php
            $sliderloop_args = array(
                'category_name' => 'Home Slider',
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => '10',
                'ignore_sticky_posts' => true,
                'order' => 'ASC',
                'orderby' => 'modified'
            )
        ?>
        <?php $sliderloop = new WP_Query( $sliderloop_args ); ?>
        <?php if ( $sliderloop->have_posts() ) : ?>
            <?php $sliderloop_item_number = 0; ?>
            <?php while ( $sliderloop->have_posts() ) : $sliderloop->the_post(); ?>
                <div class="item<?php if( $sliderloop_item_number == 0) echo ' active'; ?>">
                    <a href="<?php echo esc_url( the_permalink() ); ?>">
                        <div class="slider">
                            <?php the_post_thumbnail( 'full', array(
                                    'class' => 'img-responsive wp-post-image wp-post-image wp-post-image'
                            ) ); ?>
                            <div class="carousel-caption">
                                <h1><?php the_title(); ?></h1>
                                <p><?php the_excerpt( ); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php $sliderloop_item_number++; ?>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p><?php _e( 'Sorry, no posts matched your criteria.', 'sb2016' ); ?></p>
        <?php endif; ?>
    </div>
    <a class="left carousel-control" href="#slider01" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
    <a class="right carousel-control" href="#slider01" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
</div>
<div class="container site-inner-width-1100">
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
</div>                

<?php get_footer(); ?>