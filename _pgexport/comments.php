
    <?php
        if ( post_password_required() ) {
        	return;
        }
    ?>
    <div class="comments-area" id="comments">
        <?php if ( have_comments() ) : ?>
            <h3><?php printf( _nx( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'sb2016' ), number_format_i18n( get_comments_number() ), get_the_title() ); ?></h3>
            <?php wp_list_comments( array(
                    'style' => 'ul'
            ) ); ?>
        <?php endif; ?>
        <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' )  ) : ?>
            <p class="no-comments label label-warning"><?php _e( 'Comments are closed', 'sb2016' ); ?></p>
        <?php endif; ?>
        <div id="comments-pagination">
            <?php paginate_comments_links(); ?>
        </div>
        <?php comment_form(array('title_reply'=>'Leave a comment')); ?>
    </div>
