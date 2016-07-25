
    <article <?php post_class( 'article' ); ?> id="post-<?php the_ID(); ?>">
        <header class="entry-header">
            <h2 class="entry-title"><a href="<?php echo esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h2>
            <span><?php edit_post_link( __( ' &#40;Edit&#41;', 'sb2016' ) ); ?></span>
        </header>
        <div class="entry-meta">
            <p><?php _e( 'Filed in', 'sb2016' ); ?> <?php the_category( ', ' ); ?> <?php _e( '| Posted by', 'sb2016' ); ?> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a> <?php _e( 'on', 'sb2016' ); ?> <a href="<?php echo get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j')); ?>"><span><?php echo get_the_time( get_option( 'date_format' ) ) ?></span></a><span><?php edit_post_link( __( ' &#40;Edit&#41;', 'sb2016' ) ); ?></span></p>
            <hr>
        </div>
        <div class="post-thumbnail">
            <a href="<?php echo esc_url( the_permalink() ); ?>"><?php the_post_thumbnail( 'post-thumbnail' ); ?></a>
        </div>
        <div class="entry-content">
            <p><?php the_excerpt( ); ?></p>
            <?php if ( has_tag() ) : ?>
                <div class="entry-meta tags-meta">
                    <?php the_tags(); ?>
                </div>
            <?php endif; ?>
            <a class="btn btn-readmore btn-default" href="<?php echo esc_url( the_permalink() ); ?>"><?php _e( 'Read More', 'sb2016' ); ?></a>
        </div>
        <footer class="entry-footer">
</footer>
    </article>
