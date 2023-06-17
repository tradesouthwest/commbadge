<?php 
/**
 * @package    commbadge
 * @subpackage include/commbadge-postmeta.php
 * @since      1.0.1
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// @id A1
add_action( 'add_meta_boxes',        'commbadge_add_comment_meta_box' );  
// @id A2
add_action( 'edit_comment',          'commbadge_save_custom_meta_box', 10, 3 ); 
// @id A3
add_action( 'comment_post',          'commbadge_saving_comment_meta_data');


// @id F1
add_filter( 'preprocess_comment',    'commbadge_verify_comment_type_data' );
// @id F2 
add_filter( 'comment_form_defaults', 'commbadge_comment_form_title');
// @id F3
add_filter( 'comment_text',          'commbadge_prepend_text_label', 20, 3 );

/**
 * @id A1
 * Add to the admin_init function
 *
 * @since 1.0.2
 */
function commbadge_add_comment_meta_box()
{
    add_meta_box(
        'commbadge_comment_meta_box', 
        __('Comm Badge'), 
        'commbadge_metabox_callback', 
        'comment', 
        'normal', 
        'high', 
        null
    );
}

/**
 * Show the data of comment below comment editor
 *
 * @since 1.0.2
 * @see https://www.pmg.com/blog/adding-extra-fields-to-wordpress-comments
 */
function commbadge_metabox_callback($comment) 
{    
    global $comment;
    $badge = get_comment_meta($comment->comment_ID, 'commbadge_badge', true );
    wp_nonce_field(basename(__FILE__), "commbadge-badge-meta-box-nonce"); 
    ?>
            <table class="form-table editcomment comment_xtra">
            <tbody>
            <tr>
            <td class="first"><?php esc_html_e( 'Comment Classification:', 'commbadge-badge' ); ?></td>
            <td><input id="commbadge_badge" type="text" 
                name="commbadge_badge" size="20" class="code" 
                value="<?php echo esc_attr($badge); ?>" tabindex="1" /></td>
            </tr>
            <tr><td colspan="2"><ul style="line-height: 1">
        <li>cmbd_closed</li>
<li>cmbd_completed</li>
<li>cmbd_reviewing</li>
<li>cmbd_pending</li>
<li>cmbd_planned</li>
<li>cmbd_notplanned</li>
<li>cmbd_archived</li>
<li>cmbd_onhold</li>
<li>cmbd_release</li>
<li>cmbd_prerelease</li>
<li>cmbd_tagged</li>
<li>cmbd_duplicate</li>
<li>cmbd_backlog</li>
<li>cmbd_waiting</li>
<li></li>
</ul></td></tr>
           </tbody>
           </table>
        <?php
}

/**
 * Save the data
 *
 * @since 1.0.2
 * @id A2
 */
function commbadge_save_custom_meta_box($comment_id)
{
    if (!isset($_POST["commbadge-badge-meta-box-nonce"]) || 
    !wp_verify_nonce($_POST["commbadge-badge-meta-box-nonce"], basename(__FILE__)))
        return $comment_id;

    if(!current_user_can("edit_post", $comment_id))
        return $comment_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $comment_id;

    $commenttype             = '';
    if( isset( $_POST["commbadge_badge"] ) )
    {
        $commenttype = wp_filter_nohtml_kses($_POST["commbadge_badge"]);
        update_comment_meta($comment_id, "commbadge_badge", $commenttype);
    }
} 

/** #A3 
 * Save metadata commentmetadata of comment form
 * 
 * @since 1.0.3
 * 
 * @return string $badge Plain text value.
 */
function commbadge_saving_comment_meta_data($comment_id)
{
    if ( ( isset( $_POST['commbadge_badge'] ) ) 
      && ( '' != $_POST['commbadge_badge'] ) ) 
    
        $badge = wp_filter_nohtml_kses($_POST['commbadge_badge']);
        add_comment_meta( $comment_id, 'commbadge_badge', $badge );
}


/**
 * #F1
 * Preprocess metadata commentmetadata
 * 
 * @since 1.0
 *
 * @param string $commentdata Adds field to valid data when passed to wp-comments-post.php
 */

 function commbadge_verify_comment_type_data($commentdata )
 {
     if ( isset( $_POST['commbadge_badge'] ) ) {
         $commentdata['commbadge_badge'] = $_POST['commbadge_badge'];
     }
     return $commentdata;
 
}

/** #F2
 * Customize the Comments form title
 * @param array $args
 * @return $args
 */
function commbadge_comment_form_title ($defaults) {
    $cmdg_title = (empty(get_option( 'commbadge_options' )['commbadge_cstitle_field']))
        ? '' : esc_attr( get_option( 'commbadge_options' )['commbadge_cstitle_field'] );
	$defaults['title_reply'] = __( $cmdg_title, 'commbadge' );

	return $defaults;

}

/** #F3
 * Prepend text to each comment content.
 * @since 1.0.1
 * $comment_text (string), the main filtered argument
 * $comment (object), the current WP_Comment Object instance
 * $args (array), an array of arguments
 * @return string $text
 */
function commbadge_prepend_text_label($comment_text, $comment = 0 )
{
    $metabdg = get_comment_meta( $comment->comment_ID, 'commbadge_badge', true );
    $commid  = ( empty(commbadge_get_comment_object( $comment ) ) ) 
                ? '' : commbadge_get_comment_object( $comment );
    $badglbl = ( empty(get_option( 'commbadge_options' )['commbadge_csdescription_field']))
      ? '' : esc_attr( get_option( 'commbadge_options' )['commbadge_csdescription_field'] );

    $text = '<div id="comm-' . esc_attr( $commid ) . '" class="cmbd-start"><small>' . esc_html($badglbl) . ': </small>
    <span id="' . esc_attr( $metabdg ) . '" title="' . esc_attr( $commid ) . '"></span>
    </div>';

        return $text . $comment_text;
}
 
/**
 * Get comment id 
 * @since 1.0
 * @return int ID Number used as anchor link hash
 */
function commbadge_get_comment_object($comment){

    $comment = get_comment($comment);
    $commLabel = $comment->comment_ID;
    
        return absint( $commLabel );
} 

/**
 * 
 */
function commbadge_get_comment_parent($comment){

    $comment = get_comment($comment);
    $commParent = $comment->comment_post_ID;
    
        return absint( $commParent );
} 
