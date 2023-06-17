<?php
/**
 * Plugin Name:       Comment Badges
 * Plugin URI:        https://themes.tradesouthwest.com/wordpress/plugins/
 * Description:       Adds badges and labels to comments
 * Version:           1.0.0
 * Author:            Larry Judd
 * Author URI:        https://tradesouthwest.com
 * @package           commbadge
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Requires CP:       1.4
 * Latest CP:         1.5
 * Requires PHP:      5.4
 * Text Domain:       commbadge
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( !defined( 'COMMBADGE_VER' ) )  { define( 'COMMBADGE_VER', time() ); } 

//activate/deactivate hooks
function commbadge_plugin_activation() 
{
    set_transient( 'commbadge-admin-notice-dependancies', true, 5 );
}
// notice
add_action( 'admin_notices', 'commbadge_admin_notice_dependancies' );
function commbadge_admin_notice_dependancies(){

    /* Check transient, if available display notice */
    if( get_transient( 'commbadge-admin-notice-dependancies' ) ){
        ?>
        <div class="updated notice is-dismissible">
            <?php printf( '<p>%s <strong>%s</strong></p>',
                        esc_html_e( 'Comment Badges plugin works best with ', 'commbadge' ),
                        esc_html_e( 'ClassicPress CMS', 'commbadge' )
            ); 
            ?>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient( 'commbadge-admin-notice-dependancies' );
    }
}
function commbadge_plugin_deactivation() 
{
    return false;
}
/**
 * Plugin Scripts
 *
 * Register and Enqueues plugin scripts
 *
 * @since 1.0.0
 */
function commbadge_addtosite_scripts()
{
    wp_enqueue_style( 'commbadge-public',  
        plugin_dir_url(__FILE__) . 'static/commbadge-public.css',
        array(), 
        COMMBADGE_VER, 
        false 
    );
        
    wp_enqueue_script( 'commbadge-front', 
        plugin_dir_url( __FILE__ ) . 'static/commbadge-front.js', 
        array( ), 
        COMMBADGE_VER, 
        true 
    ); 
}
add_action( 'wp_enqueue_scripts', 'commbadge_addtosite_scripts' );

//load language scripts     
function commbadge_load_text_domain() 
{
    load_plugin_textdomain( 'commbadge', false, 
    basename( dirname( __FILE__ ) ) . '/languages' ); 
}

/**
 * Load all required files
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'commbadge_plugin_activation');
register_deactivation_hook( __FILE__, 'commbadge_plugin_deactivation');
require_once ( plugin_dir_path( __FILE__ ) . 'include/commbadge-core.php'); 
require_once ( plugin_dir_path( __FILE__ ) . 'include/commbadge-functions.php' );
?>