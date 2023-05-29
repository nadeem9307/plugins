<?php
/**
 * Module 
 * 
 * @package
 */
namespace SCFunnelbuilder\Modules\Admin\Funnels;
use SCFunnelbuilder\Admin\Module\ScFunnel_Admin_Module;
use SCFunnelbuilder\Store_Data\ScFunnel_Funnel_Store_Data;
use SCFunnelbuilder\Traits\SingletonTrait;

class Module extends ScFunnel_Admin_Module
{
    use SingletonTrait;


    /**
     * List of published funnels
     *
     * @var
     * @since 1.0.0
     */
    protected $funnels;


    /**
     * Ff needs to show pagination
     *
     * @var   bool
     * @since 1.0.0
     */
    protected $pagination = false;


    /**
     * Total number of funnels
     *
     * @var
     * @since 1.0.0
     */
    protected $total_funnels;


    /**
     * Total number of pages
     *
     * @var
     * @since 1.0.0
     */
    protected $total_page = 1;


    /**
     * Current page number
     *
     * @var
     * @since 1.0.0
     */
    protected $current_page = 1;


    /**
     * Offset
     *
     * @var   int
     * @since 1.0.0
     */
    protected $offset = 1;

    protected $utm_settings;


    /**
     * Get view of the funnel list
     *
     * @since 1.0.0
     */
    public function get_view()
    {
        $this->current_page = isset($_GET['pageno']) ? sanitize_text_field($_GET['pageno']) : 1;
        $this->offset = ($this->current_page-1) * SC_FUNNEL_FUNNEL_PER_PAGE;

        $this->init_all_funnels();
        require_once SC_FUNNEL_DIR . 'admin/modules/funnels/views/view.php';
    }


    /**
     * Get arguments for funnel
     * query
     *
     * @return array
     * @since  1.0.0
     */
    public function get_args()
    {
        $args = [
            'post_type'         => SC_FUNNEL_FUNNELS_POST_TYPE,
            'posts_per_page'    => SC_FUNNEL_FUNNEL_PER_PAGE,
            'offset'            => $this->offset,
            'post_status'       => array('publish', 'draft'),
        ];
        if (isset($_GET['s'])) {
            $args['s'] = sanitize_text_field($_GET['s']);

        }
        return $args;
    }


    /**
     * Get all funnel list
     *
     * @param int $limit
     * @param int $offset
     * 
     * @since 1.0.0
     */
    public function init_all_funnels($limit = 10, $offset = 0)
    {
        $args = [
            'post_type'         => SC_FUNNEL_FUNNELS_POST_TYPE,
            'posts_per_page'    => -1,
			'post_status'       => array('publish', 'draft'),
            'suppress_filters'  => true,
            'fields'            => 'ids'
        ];
        if (isset($_GET['s'])) {
            $args['s'] = sanitize_text_field($_GET['s']);
        }
        $all_funnels = get_posts($args);
        $funnels = get_posts($this->get_args());
        $this->funnels = $this->get_formatted_funnel_array($funnels);

        $this->total_funnels = count($all_funnels) ? count($all_funnels) : 0;
        $this->pagination = count($this->funnels) ? true : false;
        if (count($this->funnels)) {
            $this->total_page = ceil($this->total_funnels / SC_FUNNEL_FUNNEL_PER_PAGE);
        }

    }


    /**
     * Get all funnel list
     *
     * @return array
     * @since  1.0.0
     */
    public function get_formatted_funnel_array($funnels)
    {
        $_funnels = [];
        if ($funnels) {
            foreach ($funnels as $funnel) {
                $_funnel = new ScFunnel_Funnel_Store_Data();
                $_funnel->read($funnel->ID);
                $_funnel->set_data($funnel);
                $_funnels[] = $_funnel;
            }
        }
        return $_funnels;
    }


    /**
     * Get name
     * 
     * @return String
     * @since  2.0.4
     */
    public function get_name()
    {
        return __('funnels','scfunnelbuilder');
    }

    /**
     * Init ajax
     */
    public function init_ajax(){
        
    }
}
