
    <article <?php post_class( 'article' ); ?> id="post-<?php the_ID(); ?>">
        <header class="entry-header">
            <h2 class="entry-title"><?php the_title(); ?></h2>
            <span><?php edit_post_link( __( ' &#40;Edit&#41;', 'sb2016' ) ); ?></span>
        </header>
        <div class="entry-meta">
            <p><?php _e( 'Filed in:', 'sb2016' ); ?> <?php the_terms( $post->ID, 'download_category', ' ', ', ', '' ); ?> <?php _e( 'Tags:', 'sb2016' ); ?> <?php the_terms( $post->ID, 'download_tag', '', ', ', '' ); ?></p>
            <hr>
        </div>
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
        <footer class="entry-footer">
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
        </footer>
    </article>
