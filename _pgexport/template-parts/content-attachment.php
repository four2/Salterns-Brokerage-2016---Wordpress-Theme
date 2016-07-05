
    <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
        <header class="entry-header">
            <h2 class="entry-title"><?php the_title(); ?></h2>
            <span><?php edit_post_link( __( ' &#40;Edit&#41;', 'sb2016' ) ); ?></span>
            <hr />
        </header>
        <div class="post-thumbnail">
            <?php if ( wp_attachment_is_image( get_the_ID() ) ) : ?>
                <?php echo wp_get_attachment_image( get_the_ID(), 'full' ) ?>
            <?php endif; ?>
        </div>
        <div class="entry-content">
            <p><?php the_content(); ?></p>
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
