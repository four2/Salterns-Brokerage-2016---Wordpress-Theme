
    <article <?php post_class( 'article' ); ?> id="post-<?php the_ID(); ?>">
        <header class="entry-header">
            <h2 class="entry-title"><?php the_title(); ?></h2>
            <span><?php edit_post_link( __( ' &#40;Edit&#41;', 'sb2016' ) ); ?></span>
        </header>
        <div class="entry-meta">
            <p><?php _e( 'Filed in', 'sb2016' ); ?> <?php the_category( ', ' ); ?> <?php _e( '| Posted by', 'sb2016' ); ?> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a> <?php _e( 'on', 'sb2016' ); ?> <a href="<?php echo get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j')); ?>"><span><?php echo get_the_time( get_option( 'date_format' ) ) ?></span></a></p>
            <hr />
        </div>
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
        <footer class="entry-footer">
            <?php wp_link_pages( array() ); ?>
            <div class="jetpack-sharing-buttons">
                <?php
                        // Move the Jetpack Sharing and Like buttons //
                        // https://jetpack.com/2013/06/10/moving-sharing-icons/ //
                    
                        if ( function_exists( 'sharing_display' ) ) {
                        sharing_display( '', true );
                    }
                    
                    if ( class_exists( 'Jetpack_Likes' ) ) {
                        $custom_likes = new Jetpack_Likes;
                        echo $custom_likes->post_likes( '' );
                    }
                ?>
            </div>
            <?php if ( class_exists( 'Jetpack_RelatedPosts' ) ) : ?>
                <div class="jetpack-related-posts">
                    <?php
                        echo do_shortcode( '[jetpack-related-posts]' );
                    ?>
                </div>
            <?php endif; ?>
        </footer>
    </article>
