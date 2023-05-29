<?php
/**
 * shortcodes
 *
 * @link       chotu.com
 * @since      1.0.0
 *
 * @package    Chotu
 * @subpackage Chotu/includes
 */

defined('ABSPATH') || exit;

/**
 * WooCommerce Shortcodes class.
 */
class Chotu_Rootshop_Shortcodes
{

    /**
     * Init shortcodes.
     */
    public static function init()
    {
        $shortcodes = array(
            'rootshop' => __CLASS__ . '::rootshop',
            'rootshop_category' => __CLASS__ . '::rootshop_category',
            'rootshop_categories' => __CLASS__ . '::rootshop_categories',
            'rootshops' => __CLASS__ . '::rootshops',
        );

        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode(apply_filters("{$shortcode}_shortcode_tag", $shortcode), $function);
        }

        // Alias for pre 2.1 compatibility.
        add_shortcode('woocommerce_messages', __CLASS__ . '::shop_messages');
    }

    /**
     * Shortcode Wrapper.
     *
     * @param string[] $function Callback function.
     * @param array    $atts     Attributes. Default to empty array.
     * @param array    $wrapper  Customer wrapper data.
     *
     * @return string
     */
    public static function shortcode_wrapper(
        $function,
        $atts = array(),
        $wrapper = array(
            'class' => 'woocommerce',
            'before' => null,
            'after' => null,
        )
    ) {
        ob_start();

        // @codingStandardsIgnoreStart
        echo empty($wrapper['before']) ? '<div class="' . esc_attr($wrapper['class']) . '">' : $wrapper['before'];
        call_user_func($function, $atts);
        echo empty($wrapper['after']) ? '</div>' : $wrapper['after'];
        // @codingStandardsIgnoreEnd

        return ob_get_clean();
    }

    /**
     * List rootshops in a category shortcode.
     *
     * @param array $atts Attributes.
     * @return string
     */
    public static function rootshop_category($atts)
    {
        if (empty($atts['category'])) {
            return '';
        }

        $atts = array_merge(
            array(
                'limit' => '-1',
                'columns' => '4',
                'orderby' => 'menu_order title',
                'order' => 'ASC',
                'category' => '',
                'cat_operator' => 'IN',
            ),
            (array) $atts
        );

        $shortcode = new Chotu_Shortcode_Rootshops($atts, 'rootshop_category');

        return $shortcode->get_content();
    }

    /**
     * List all (or limited) rootshop categories.
     *
     * @param array $atts Attributes.
     * @return string
     */
    public static function rootshop_categories($atts)
    {
        if (isset($atts['number'])) {
            $atts['limit'] = $atts['number'];
        }
        // dd($atts);
        $atts = shortcode_atts(
            array(
                'limit' => '-1',
                'orderby' => 'name',
                'order' => 'ASC',
                'columns' => '2',
                'hide_empty' => 1,
                'parent' => '',
                'ids' => '',
            ),
            $atts,
            'rootshop_cat'
        );

        $ids = array_filter(array_map('trim', explode(',', $atts['ids'])));
        $hide_empty = (true === $atts['hide_empty'] || 'true' === $atts['hide_empty'] || 1 === $atts['hide_empty'] || '1' === $atts['hide_empty']) ? 1 : 0;

        // Get terms and workaround WP bug with parents/pad counts.
        $args = array(
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'hide_empty' => $hide_empty,
            'include' => $ids,
            'pad_counts' => true,
            'child_of' => $atts['parent'],
        );

        $rootshop_categories = get_terms('rootshop_cat', $args);
        

        if ('' !== $atts['parent']) {
            $rootshop_categories = wp_list_filter(
                $rootshop_categories,
                array(
                    'parent' => $atts['parent'],
                )
            );
        }

        if ($hide_empty) {
            foreach ($rootshop_categories as $key => $category) {
                if (0 === $category->count) {
                    unset($rootshop_categories[$key]);
                }
            }
        }

        $atts['limit'] = '-1' === $atts['limit'] ? null : intval($atts['limit']);
        if ($atts['limit']) {
            $rootshop_categories = array_slice($rootshop_categories, 0, $atts['limit']);
        }

        $columns = absint($atts['columns']);

        wc_set_loop_prop('columns', $columns);
        wc_set_loop_prop('is_shortcode', true);

        ob_start();

        if ($rootshop_categories) {
            woocommerce_product_loop_start();
            foreach ($rootshop_categories as $category) {
                $GLOBALS['term'] = $category;
                get_template_part('template-parts/posts/category/rootshop','category-loop');
            }

            woocommerce_product_loop_end();
        }

        wc_reset_loop();

        return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
    }

    /**
     * List multiple rootshop shortcode.
     *
     * @param array $atts Attributes.
     * @return string
     */
    public static function rootshops($atts)
    {
        $atts = (array) $atts;
        $type = 'rootshops';

        $shortcode = new Chotu_Shortcode_Rootshops($atts, $type);

        return $shortcode->get_content();
    }

    /**
     * Display a single rootshop.
     *
     * @param array $atts Attributes.
     * @return string
     */
    public static function rootshop($atts)
    {
        if (empty($atts)) {
            return '';
        }

        $atts['skus']    = isset($atts['sku']) ? $atts['sku'] : '';
        $atts['ids']     = isset($atts['id']) ? $atts['id'] : '';
        $atts['limit']   = 1;
        $shortcode = new Chotu_Shortcode_Rootshops((array) $atts, 'rootshop');
        return $shortcode->get_content();
    }

}
