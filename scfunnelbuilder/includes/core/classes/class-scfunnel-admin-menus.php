<?php
/**
 * Admin menus
 * 
 * @package
 */
namespace SCFunnelbuilder\Menu;

//use ScFunnels\Admin\SetupWizard;
use SCFunnelbuilder\SCFunnelbuilder;
use SCFunnelbuilder\Modules\ScFunnel_Modules_Manager as Module_Manager;
// use SCFunnelbuilder\Scfunnel_functions;

/**
 * Class Scfunnel_Menus
 *
 * @package Scfunnel
 */
class ScFunnel_Menus
{
    public $instance;
    public function __construct()
    {
        $this->instance = new Module_Manager();
        add_action('admin_menu', [$this, 'sc_funnel_register_plugin_menus'],30);
        add_filter('admin_head', [$this, 'sc_funnel_remove_submenu'], 10, 2);
        add_filter('admin_head', [$this, 'sc_funnel_remove_notices_from_funnel_canvas'], 10, 2);
        // add_action('admin_init', [$this, 'disallow_all_step_view']);
        // add_action('admin_footer', [$this, 'doc_link_with_new_page']);
        
        if( isset($_GET['page']) && ( 'edit_funnel' === $_GET['page']) ) {
			add_filter( "admin_body_class", array($this, 'sc_funnel_folded_menu_class') );
		}
    }


    /**
     * Register plugin menus and submenus
     *
     * @since 1.0.0
     */
    public function sc_funnel_register_plugin_menus()
    {
        add_submenu_page(
            'studiocart',
            __('Funnel Builder','scfunnelbuilder'),
            __('Funnel Builder','scfunnelbuilder'),
            'sc_manager_option', 
            'sc_funnels',
            [$this,'sc_funnel_builder_funnel_lists'],
            6
        );
        add_submenu_page(
            SC_FUNNEL_MAIN_PAGE_SLUG,
            __('Edit Funnel', 'scfunnelbuilder'),
            __('Edit Funnel', 'scfunnelbuilder'),
            'sc_manager_option',
            SC_FUNNEL_EDIT_FUNNEL_SLUG,
            [$this, 'sc_funnel_render_edit_funnel_page']
        );
       
    }    
    /**
     * sc_funnel_builder_funnel_lists
     *
     * @return void
     */
    public function sc_funnel_builder_funnel_lists(){
        //$scfunnelbuilder = new Module_Manager();
        $this->instance->get_admin_modules('funnels')->get_view();
    }
    
    /**
     * sc_funnel_render_edit_funnel_page
     *
     * @return void
     */
    public function sc_funnel_render_edit_funnel_page(){
        $funnel_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $this->instance->get_admin_modules('funnel')->init($funnel_id);
        $this->instance->get_admin_modules('funnel')->get_view();
    }    
    /**
     * add_folded_menu_class
     *
     * @param  mixed $classes
     * @return void
     */
    public function sc_funnel_folded_menu_class($classes) {
		return $classes." folded";
	}

    /**
     * Remove submenu from plugin menu
     *
     * @since 1.0.0
     */
    public function sc_funnel_remove_submenu()
    {
        remove_submenu_page(SC_FUNNEL_MAIN_PAGE_SLUG, 'edit_funnel');
    }
   
    /**
     * sc_funnel_remove_notices_from_funnel_canvas
     *
     * @return void
     */
    public function sc_funnel_remove_notices_from_funnel_canvas() {
    	if (empty($_GET['page'])) {
    		return;
		}
        if (('edit_funnel' == sanitize_text_field( $_GET['page'] ) )) {
            remove_all_actions( 'admin_notices' );
        }
		if ( 'sc_funnels' == sanitize_text_field( $_GET['page'] ) ) {
			add_action('admin_footer', array( $this, 'sc_funnel_remove_admin_notices' ));
		}
    }
   
	/**
	 * sc_funnel_remove_admin_notices
	 *
	 * @return void
	 */
	public function sc_funnel_remove_admin_notices() {
		echo '<style>.update-nag, .updated, .error, .is-dismissible, .notice { display: none; } .scfunnels-notice {display: block;}</style>';
	}
}
