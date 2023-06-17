<?php 
if ( !class_exists('Commbadge_Comment_Walker') ) {
    
    class Commbadge_Comment_Walker extends Walker_Comment {
        protected function html5_comment( $comment, $depth, $args ) {
            $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
            ?>
            <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent' : '', $comment ); ?>>
                <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
                    <footer class="comment-meta">
                        <div class="comment-author vcard">
                            <?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
                            <?php printf( __( '%s <span class="says">says:</span>' ), sprintf( '<span class="fn">%s</span>', get_comment_author_link( $comment ) ) ); ?>
                        </div><!-- .comment-author -->

                        <div class="comment-metadata">

                            <?php 
                            $user_id=$comment->user_id;
                            ?>
                            <p class="commenter-bio"><?php the_author_meta('description',$user_id); ?></p>
                            <p class="commenter-url"><a href="<?php the_author_meta('user_url',$user_id); ?>" target="_blank"><?php the_author_meta('user_url',$user_id); ?></a></p>
                            
                        </div><!-- .comment-metadata -->

                        <?php if ( '0' == $comment->comment_approved ) : ?>
                        <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
                        <?php endif; ?>
                    </footer><!-- .comment-meta -->

                    <div class="comment-content">
                        <?php comment_text(); ?>
                    </div><!-- .comment-content -->

                    <?php
                    comment_reply_link( array_merge( $args, array(
                        'add_below' => 'div-comment',
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'before'    => '<div class="reply">',
                        'after'     => '</div>'
                    ) ) );
                    ?>
                </article><!-- .comment-body -->
            <?php
        }
    }
    
}