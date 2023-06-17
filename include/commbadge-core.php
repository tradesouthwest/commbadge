<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    commbadge
 * @subpackage /includes
 * @author     Larry Judd <tradesouthwest@gmail.com>
 * 
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'commbadge_add_options_page' ); 
add_action( 'admin_init', 'commbadge_register_admin_options' ); 

// Sub-menu $parent_slug, $page_title, $menu_title, $cap, $menu_slug, $funx
function commbadge_add_options_page() 
{
   add_submenu_page(
       'options-general.php',
        esc_html__( 'Comments Badges', 'commbadge' ),
        esc_html__( 'Comments Badge', 'commbadge' ),
        'manage_options',
        'commbadge',
        'commbadge_options_page' 
    );
}   
 
/** a.) Register new settings
 *  $option_group (page), $option_name, $sanitize_callback
 *  --------
 ** b.) Add sections
 *  $id, $title, $callback, $page
 *  --------
 ** c.) Add fields 
 *  $id, $title, $callback, $page, $section, $args = array() 
 *  --------
 ** d.) Options Form Rendering. action="options.php"
 *
 */

// a.) register all settings groups
function commbadge_register_admin_options() 
{
    //options pg
    register_setting( 'commbadge_options', 'commbadge_options' );
     

/**
 * b1.) options section
 */        
    add_settings_section(
        'commbadge_options_section',
        esc_html__( 'Configuration and Settings', 'commbadge' ),
        'commbadge_options_section_cb',
        'commbadge_options'
    ); 
            // c1.) settings 
    add_settings_field(
        'commbadge_cstitle_field',
        esc_attr__('Heading before comment form', 'commbadge'),
        'commbadge_cstitle_field_cb',
        'commbadge_options',
        'commbadge_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'commbadge_options', 
            'name'         => 'commbadge_cstitle_field',
            'value'        => (empty(get_option( 'commbadge_options' )['commbadge_cstitle_field']))
                    ? '' : esc_attr( get_option( 'commbadge_options' )['commbadge_cstitle_field'] ),
            'description'  => esc_html__( 'Shows at top of Add Badge or Label box', 'commbadge' ),
            'tip'          => esc_html__( 'This field is il8n translate ready. Try: Add Badge or Label', 'commbadge' )
        )
    );
    // c2.) settings 
    add_settings_field(
        'commbadge_csdescription_field',
        esc_attr__('Label for badges', 'commbadge'),
        'commbadge_csdescription_field_cb',
        'commbadge_options',
        'commbadge_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'commbadge_options', 
            'name'         => 'commbadge_csdescription_field',
            'value'        => (empty(get_option( 'commbadge_options' )['commbadge_csdescription_field']))
                    ? '' : esc_attr( get_option( 'commbadge_options' )['commbadge_csdescription_field'] ),
            'description'  => esc_html__( 'Label Name', 'commbadge' ),
             'tip'         => esc_html__( 'This field is il8n translate ready', 'commbadge' )
        )
    );
}
/** 
 * name for 'label' field
 * @since 1.0.0
 */
function commbadge_cstitle_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" /><b class="wntip" title="%6$s"> ? </b><br>
        <span class="wndspan">%5$s </span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description'],
        $args['tip']
    );
}

/** 
 * name for 'text' field
 * @since 1.0.0
 */
function commbadge_csdescription_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" /><b class="wntip" title="%6$s"> ? </b><br>
        <span class="wndspan">%5$s </span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description'],
        $args['tip']
    );
}

/**
 ** Section Callbacks
 *  $id, $title, $callback, $page
 */
// section heading cb
function commbadge_options_section_cb()
{    
print( "version: " . COMMBADGE_VER );
} 


// d.) render admin page
function commbadge_options_page() 
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) return;
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    ?>
    <div class="wrap wrap-commbadge-admin">
    
    <h1><span id="SlwOptions" class="dashicons dashicons-admin-tools"></span> 
    <?php echo esc_html( 'CommBadge plugin Options' ); ?></h1>
         
    <form action="options.php" method="post">
    
    <?php //page=commbadge&tab=commbadge_options
        settings_fields(     'commbadge_options' );
        do_settings_sections( 'commbadge_options' ); 
        
        submit_button( 'Save Settings' ); 
    ?>

    </form>
    <table><thead><tr><th>Badge sprite</th><th>id names</th></thead><tbody><tr><td>
    <figure><img src="<?php echo esc_url( plugins_url( 'commbadge/static/imgs/bdgsa.png' ) ); ?>" 
            alt="png" height="300" />
    </figure></td>
    <td><ul style="line-height: 1.2">
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
</ul></td>
    </tr></tbody></table>
    <p><a class="button" href="<?php echo esc_url( site_url('/') . 'wp-content/plugins/commbadge/docs/'); ?>" 
    title="documentation" target="blank">Documentation</a></p>
    </div>
<?php 
} 