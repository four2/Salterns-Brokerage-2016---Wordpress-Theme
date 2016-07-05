
    <article <?php post_class( 'article' ); ?> id="post-<?php the_ID(); ?>">
        <header class="entry-header">
            <h2 class="entry-title"><a href="<?php echo esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h2>
            <span><?php edit_post_link( __( ' &#40;Edit&#41;', 'sb2016' ) ); ?></span>
        </header>
        <div class="entry-meta">
            <p><?php the_taxonomies( array(
                        'before' => '<p class=\"meta\">',
                        'sep' => ' | ',
                        'after' => '<p class=\"meta\">',
                        'template' => '%s: %l'
                )); ?></p>
            <hr />
        </div>
        <div class="post-thumbnail">
            <a href="<?php echo esc_url( the_permalink() ); ?>"><?php the_post_thumbnail( 'post-thumbnail' ); ?></a>
        </div>
        <div class="entry-content">
            <?php the_excerpt( ); ?>
            <a class="btn btn-readmore btn-default" href="<?php echo esc_url( the_permalink() ); ?>"><?php _e( 'Read More', 'sb2016' ); ?></a>
        </div>
        <footer class="entry-footer"></footer>
    </article>
