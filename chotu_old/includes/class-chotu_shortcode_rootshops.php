
<?php
/**
 * Rootshops shortcodes
 *
 * @link       chotu.com
 * @since      1.0.0
 *
 * @package    Chotu
 * @subpackage Chotu/includes
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Rootshops shortcode class.
 */
class chotu_Shortcode_Rootshops
{

    /**
     * Shortcode type.
     *
     * @since 3.2.0
     * @var   string
     */
    protected $type = 'rootshops';

    /**
     * Attributes.
     *
     * @since 3.2.0
     * @var   array
     */
    protected $attributes = array();

    /**
     * Query args.
     *
     * @since 3.2.0
     * @var   array
     */
    protected $query_args = array();

    /**
     * Set custom visibility.
     *
     * @since 3.2.0
     * @var   bool
     */
    protected $custom_visibility = false;

    /**
     * Initialize shortcode.
     *
     * @since 3.2.0
     * @param array  $attributes Shortcode attributes.
     * @param string $type       Shortcode type.
     */
    public function __construct($attributes = array(), $type = 'Rootshops')
    {
        $this->type = $type;
        $this->attributes = $this->parse_attributes($attributes);
        $this->query_args = $this->parse_query_args();
    }

    /**
     * Get shortcode attributes.
     *
     * @since  3.2.0
     * @return array
     */
    public function get_attributes()
    {
        return $this->attributes;
    }

    /**
     * Get query args.
     *
     * @since  3.2.0
     * @return array
     */
    public function get_query_args()
    {
        return $this->query_args;
    }

    /**
     * Get shortcode type.
     *
     * @since  3.2.0
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Get shortcode content.
     *
     * @since  3.2.0
     * @return string
     */
    public function get_content()
    {
        return $this->rootshops_loop();
    }

    /**
     * Parse attributes.
     *
     * @since  3.2.0
     * @param  array $attributes Shortcode attributes.
     * @return array
     */
    protected function parse_attributes($attributes)
    {
        $attributes = $this->parse_legacy_attributes($attributes);

        $attributes = shortcode_atts(
            array(
                'limit' => '-1', // Results limit.
                'columns' => '', // Number of columns.
                'rows' => '', // Number of rows. If defined, limit will be ignored.
                'orderby' => '', // menu_order, title, date, rand, price, popularity, rating, or id.
                'order' => '', // ASC or DESC.
                'ids' => '', // Comma separated IDs.
                'skus' => '', // Comma separated SKUs.
                'category' => '', // Comma separated category slugs or ids.
                'cat_operator' => 'IN', // Operator to compare categories. Possible values are 'IN', 'NOT IN', 'AND'.
                'attribute' => '', // Single attribute slug.
                'terms' => '', // Comma separated term slugs or ids.
                'terms_operator' => 'IN', // Operator to compare terms. Possible values are 'IN', 'NOT IN', 'AND'.
                'tag' => '', // Comma separated tag slugs.
                'tag_operator' => 'IN', // Operator to compare tags. Possible values are 'IN', 'NOT IN', 'AND'.
                'visibility' => 'visible', // Product visibility setting. Possible values are 'visible', 'catalog', 'search', 'hidden'.
                'class' => '', // HTML class.
                'page' => 1, // Page for pagination.
                'paginate' => false, // Should results be paginated.
                'cache' => true, // Should shortcode output be cached.
            ),
            $attributes,
            $this->type
        );

        //if (!absint($attributes['columns'])) {
        //    $attributes['columns'] = wc_get_default_products_per_row();
        //}

        return $attributes;
    }

    /**
     * Parse legacy attributes.
     *
     * @since  3.2.0
     * @param  array $attributes Attributes.
     * @return array
     */
    protected function parse_legacy_attributes($attributes)
    {
        $mapping = array(
            'per_page' => 'limit',
            'operator' => 'cat_operator',
            'filter' => 'terms',
        );

        foreach ($mapping as $old => $new) {
            if (isset($attributes[$old])) {
                $attributes[$new] = $attributes[$old];
                unset($attributes[$old]);
            }
        }

        return $attributes;
    }

    /**
     * Parse query args.
     *
     * @since  3.2.0
     * @return array
     */
    protected function parse_query_args()
    {
        $query_args = array(
            'post_type' => 'rootshop',
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows' => false === wc_string_to_bool($this->attributes['paginate']),
            'orderby' => empty($_GET['orderby']) ? $this->attributes['orderby'] : wc_clean(wp_unslash($_GET['orderby'])), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        );

        $orderby_value = explode('-', $query_args['orderby']);
        $orderby = esc_attr($orderby_value[0]);
        $order = !empty($orderby_value[1]) ? $orderby_value[1] : strtoupper($this->attributes['order']);
        $query_args['orderby'] = $orderby;
        $query_args['order'] = $order;

        // if (wc_string_to_bool($this->attributes['paginate'])) {
        //  $this->attributes['page'] = absint(empty($_GET['product-page']) ? 1 : $_GET['product-page']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        //}

        if (!empty($this->attributes['rows'])) {
            $this->attributes['limit'] = $this->attributes['columns'] * $this->attributes['rows'];
        }

        $ordering_args = WC()->query->get_catalog_ordering_args($query_args['orderby'], $query_args['order']);
        $query_args['orderby'] = $ordering_args['orderby'];
        $query_args['order'] = $ordering_args['order'];
        if ($ordering_args['meta_key']) {
            $query_args['meta_key'] = $ordering_args['meta_key']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
        }
        $query_args['posts_per_page'] = intval($this->attributes['limit']);
        if (1 < $this->attributes['page']) {
            $query_args['paged'] = absint($this->attributes['page']);
        }
        $query_args['meta_query'] = WC()->query->get_meta_query(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
        $query_args['tax_query'] = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query

        // // IDs.
        $this->set_ids_query_args($query_args);

        // Set specific types query args.
        // if (method_exists($this, "set_{$this->type}_query_args")) {
        //  $this->{"set_{$this->type}_query_args"}($query_args);
        //}

        // Categories.
        $this->set_categories_query_args($query_args);

        // Tags.
        $this->set_tags_query_args($query_args);

        //$query_args = apply_filters('woocommerce_shortcode_products_query', $query_args, $this->attributes, $this->type);

        // Always query only IDs.
        $query_args['fields'] = 'ids';

        return $query_args;
    }

    /**
     * Set ids query args.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_ids_query_args(&$query_args)
    {
        if (!empty($this->attributes['ids'])) {
            $ids = array_map('trim', explode(',', $this->attributes['ids']));

            if (1 === count($ids)) {
                $query_args['p'] = $ids[0];
            } else {
                $query_args['post__in'] = $ids;
            }
        }
    }

    /**
     * Set categories query args.
     *
     * @since 3.2.0
     * @param array $query_args Query args.
     */
    protected function set_categories_query_args(&$query_args)
    {
        if (!empty($this->attributes['category'])) {
            $categories = array_map('sanitize_title', explode(',', $this->attributes['category']));
            $field = 'slug';

            if (is_numeric($categories[0])) {
                $field = 'term_id';
                $categories = array_map('absint', $categories);
                // Check numeric slugs.
                foreach ($categories as $cat) {
                    $the_cat = get_term_by('slug', $cat, 'rootshop_cat');
                    if (false !== $the_cat) {
                        $categories[] = $the_cat->term_id;
                    }
                }
            }

            $query_args['tax_query'][] = array(
                'taxonomy' => 'rootshop_cat',
                'terms' => $categories,
                'field' => $field,
                'operator' => $this->attributes['cat_operator'],

                /*
                 * When cat_operator is AND, the children categories should be excluded,
                 * as only products belonging to all the children categories would be selected.
                 */
                'include_children' => 'AND' === $this->attributes['cat_operator'] ? false : true,
            );
        }
    }

    /**
     * Get wrapper classes.
     *
     * @since  3.2.0
     * @param  int $columns Number of columns.
     * @return array
     */
    protected function get_wrapper_classes($columns)
    {
        $classes = array('woocommerce');

        if ('rootshop' !== $this->type) {
            $classes[] = 'columns-' . $columns;
        }

        $classes[] = $this->attributes['class'];

        return $classes;
    }
 
    /**
	 * Run the query and return an array of data, including queried ids and pagination information.
	 *
	 * @since  3.3.0
	 * @return object Object with the following props; ids, per_page, found_posts, max_num_pages, current_page
	 */
	protected function get_query_results() {
        $query = new WP_Query( $this->query_args );
        $paginated = ! $query->get( 'no_found_rows' );
        $results = (object) array(
            'ids'          => wp_parse_id_list( $query->posts ),
            'total'        => $paginated ? (int) $query->found_posts : count( $query->posts ),
            'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
            'per_page'     => (int) $query->get( 'posts_per_page' ),
            'current_page' => $paginated ? (int) max( 1, $query->get( 'paged', 1 ) ) : 1,
        );
		// Remove ordering query arguments which may have been added by get_catalog_ordering_args.
		WC()->query->remove_ordering_args();

		/**
		 * Filter shortcode rootshops query results.
		 *
		 * @since 4.0.0
		 * @param stdClass $results Query results.
		 * @param Chotu_Shortcode_rootshops $this Chotu_Shortcode_rootshops instance.
		 */
		return apply_filters( 'woocommerce_shortcode_rootshops_query_results', $results, $this );
	}
    /**
	 * Loop over found rootshop.
	 *
	 * @since  3.2.0
	 * @return string
	 */
	protected function rootshops_loop() {
		$columns  = absint( $this->attributes['columns'] );
		$classes  = $this->get_wrapper_classes( $columns );
		$rootshops = $this->get_query_results();
		ob_start();

		if ( $rootshops && $rootshops->ids ) {
			// Prime caches to reduce future queries.
			if ( is_callable( '_prime_post_caches' ) ) {
				_prime_post_caches( $rootshops->ids );
			}

			// Setup the loop.
			wc_setup_loop(
				array(
					'columns'      => $columns,
					'name'         => $this->type,
					'is_shortcode' => true,
					'is_search'    => false,
					'is_paginated' => wc_string_to_bool( $this->attributes['paginate'] ),
					'total'        => $rootshops->total,
					'total_pages'  => $rootshops->total_pages,
					'per_page'     => $rootshops->per_page,
					'current_page' => $rootshops->current_page,
				)
			);

			$original_post = $GLOBALS['post'];

			//do_action( "woocommerce_shortcode_before_{$this->type}_loop", $this->attributes );

			// Fire standard shop loop hooks when paginating results so we can show result counts and so on.
			// if ( wc_string_to_bool( $this->attributes['paginate'] ) ) {
			// 	do_action( 'woocommerce_before_shop_loop' );
			// }

			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				foreach ( $rootshops->ids as $rootshop_id ) {
					$GLOBALS['post'] = get_post( $rootshop_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $GLOBALS['post'] );
					// Render product template.
					wc_get_template_part( 'template-parts/posts/content', 'rootshop' );
				}
			}

			$GLOBALS['post'] = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			woocommerce_product_loop_end();

			// Fire standard shop loop hooks when paginating results so we can show result counts and so on.
			if ( wc_string_to_bool( $this->attributes['paginate'] ) ) {
				do_action( 'woocommerce_after_shop_loop' );
			}

			do_action( "woocommerce_shortcode_after_{$this->type}_loop", $this->attributes );

			wp_reset_postdata();
			wc_reset_loop();
		} else {
			do_action( "woocommerce_shortcode_{$this->type}_loop_no_results", $this->attributes );
		}

		return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . ob_get_clean() . '</div>';
	}
    /**
	 * Set tags query args.
	 *
	 * @since 3.3.0
	 * @param array $query_args Query args.
	 */
	protected function set_tags_query_args( &$query_args ) {
		if ( ! empty( $this->attributes['tag'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'rootshop_tag',
				'terms'    => array_map( 'sanitize_title', explode( ',', $this->attributes['tag'] ) ),
				'field'    => 'slug',
				'operator' => $this->attributes['tag_operator'],
			);
		}
	}

}