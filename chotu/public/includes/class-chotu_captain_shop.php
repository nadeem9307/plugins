<?php
/**
 * Chotu_Shop
 */
class Chotu_Shop_Public{	
	/**
	 * shop_id
	 *
	 * @var mixed
	 */
	public $shop_id;
	
	/**
	 * __construct
	 *
	 * @param  mixed $shop_id
	 * @return void
	 */
	public function __construct() {
		// to rewrite the captain URL for captain page as per chotu requirements
		/**
		 * https://developer.wordpress.org/reference/hooks/author_rewrite_rules/
		 */
		add_filter( 'author_rewrite_rules', function($author_rewrite) {
			global $wpdb;
			$author_rewrite = array();
			$authors = $wpdb->get_results("SELECT user_nicename AS nicename from $wpdb->users");   
			foreach($authors as $author) {
				$author_rewrite["({$author->nicename})/page/?([0-9]+)/?$"] = 'index.php?author_name=$matches[1]&paged=$matches[2]';
				$author_rewrite["({$author->nicename})/?$"] = 'index.php?author_name=$matches[1]';
			}
			return $author_rewrite;
		} );


		// to set the meta value for the SEO meta key description for the shop set by the captain
		/**
		 * https://wp-kama.ru/plugin/yoast/hook/wpseo_opengraph_desc
		 */
		add_filter( 'wpseo_opengraph_desc', function($description){
			if(is_author()){
				$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
				$captain_id = $author->ID;
				return get_user_meta($captain_id,'description',true);
			}
			return $description;
		} );


		// to set the cover pic the captain shop page for the  SEO yoast meta
		/**
		 * https://wp-kama.ru/plugin/yoast/hook/wpseo_opengraph_image
		 */
		add_filter( 'wpseo_opengraph_image', function($image){
			if(is_author()){
				$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
				$captain_id = $author->ID;
				if(in_array('captain',$author->roles)){
				  return get_user_meta($captain_id,'captain_cover_pic',true);
				}
			}
			return $image;
		},30, 1 );


		//  to set the captain shop name as title for the SEO yoast meta
		/**
		 * https://wp-kama.ru/plugin/yoast/hook/wpseo_opengraph_title
		 */
		add_filter( 'wpseo_opengraph_title', function($title){
			if(is_author()){
				$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
				$captain_id = $author->ID;
				return $author->display_name;  
			}
			return $title;
		},30, 1 );


		//  to set the captain shop URL in SEO yoast meta 
		/**
		 * https://wp-kama.ru/plugin/yoast/hook/wpseo_opengraph_url
		 */
		add_filter( 'wpseo_opengraph_url', function($url){
			if(is_author()){
				return str_replace('author/','',$url); 
			}
			return $url;
		},30, 1 );


		// to set the canonical URL for the SEO yoast meta
		/**
		 * https://developer.yoast.com/features/seo-tags/canonical-urls/api/
		 */
		add_filter( 'wpseo_canonical', function( $canonical ) {
			if ( is_author() ) {
				return str_replace('author/','',$canonical);
			}
			return $canonical;
		}, 10, 1 );


		// to set the schema website to false in the SEO yoast meta
		add_filter( 'wpseo_schema_website', '__return_false' );
		
		//  Overwrite the person schema for a user with captain role
		/**
		 * https://wp-kama.ru/plugin/yoast/function/WPSEO_Schema_Person::get_social_profiles
		 */
		add_filter( 'wpseo_schema_person', function($data){
			if(is_author()){
				$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
				$captain_id = $author->ID;
				if(in_array('captain',$author->roles)){
					return $this->chotu_set_captain_schema($author);
				}
			}
		} );


		//  to set SEO open graph, schema webpage, schema graph pieces as false for the captain role
		/**
		 * https://wp-plugin-api.com/hook/wpseo_frontend_presenters/
		 */
		add_action( 'wpseo_frontend_presenters', function(){
			$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
			if (!empty($author)) {
					$captain_id = $author->ID;
					if(in_array('captain',$author->roles)){
						add_filter( 'wpseo_opengraph_type', '__return_false' );
						add_filter( 'wpseo_schema_webpage', '__return_false');
						add_filter( 'wpseo_schema_graph_pieces', array($this,'chotu_remove_breadcrumbs_from_schema'), 11, 2 );
					}
				}
		} );


		// to redirect the cart page to checkout page by default.
		/**
		 * https://developer.wordpress.org/reference/hooks/template_redirect/
		 */
		add_action( 'template_redirect', function(){
			if(is_page('cart') && !WC()->cart->is_empty()){
				$url = home_url('/checkout');
				wp_redirect($url);
				exit();
			}
		} );


		// to redirect to captain home page when clicked on home if the captain cookie is set
		/**
		 * https://woocommerce.com/document/customise-the-woocommerce-breadcrumb/
		 */
		add_filter( 'woocommerce_breadcrumb_home_url', function($home_url){
			global $chotu_status;
			switch ($chotu_status) {
				case "A":
				break;
				case "B":
				return $home_url.'/'.$_COOKIE['captain'];
				break;
				case "C":
				break;
				case "D":
			}
			return $home_url.'/shop/';
		} );



		// to return the size by which the product has to be displayed in the search
		/**
		 * https://fibosearch.com/documentation/tips-tricks/how-to-change-image-sizes/
		 */
		add_filter( 'dgwt/wcas/suggestion_details/product/thumb_size', function ( $size ) {
			return 'woocommerce_gallery_thumbnail';
		} );


		// to fetch the thumbnail URL of the product whose product ID is passed
		/**
		 * https://fibosearch.com/documentation/tips-tricks/how-to-change-image-size-in-the-search-suggestions/
		 */
		add_filter( 'dgwt/wcas/product/thumbnail_src', function($src, $product_id) {
		  $thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'woocommerce_gallery_thumbnail' );
		  if ( is_array( $thumbnail_url ) && !empty( $thumbnail_url[0] ) ) {
			$src = $thumbnail_url[0];
		  }
		  return $src;
		}, 10, 2);
		/* main woocommerce shop url redirect to /oye url*/
		/**
		 * https://developer.wordpress.org/reference/hooks/template_redirect/
		 */
		add_action( 'template_redirect', function(){
			if(is_shop() && !isset($_GET['s'])){
				wp_redirect(site_url() . '/oye', '302');
			}
		} );
		// disable all the partial search URLS. chotu.com/foo redirects to chotu.com/food-delivery, disable this.
		// is this the right place for this code?
		add_filter('redirect_canonical', function($redirect_url){
			if (is_404()) {
				return false;
				}
			return $redirect_url;
		});
		add_filter( 'woocommerce_continue_shopping_redirect', function($default){
			if(isset($_COOKIE['captain'])){
				return home_url().'/'.$_COOKIE['captain'];
			}else{
				return home_url().'/oye';
			}
		} );
		add_filter( 'woocommerce_return_to_shop_redirect',function() {
			if(isset($_COOKIE['captain'])){
				return home_url().'/'.$_COOKIE['captain'];
			}else{
				return home_url().'/oye';
			}
		} );
	}
	

	/**
	 * chotu_remove_breadcrumbs_from_schema
	 *
	 * @param  mixed $pieces
	 * @param  mixed $context
	 * @return void
	 * to remove the breadcrumbs from schema for captain shop
	 */
	public function chotu_remove_breadcrumbs_from_schema( $pieces, $context ) {
		return \array_filter( $pieces, function( $piece ) {
			return  !$piece instanceof \Yoast\WP\SEO\Generators\Schema\Website &&  !$piece instanceof \Yoast\WP\SEO\Generators\Schema\Breadcrumb;
		} );
	}	


	/**
	 * chotu_set_captain_schema
	 *
	 * @param  mixed $user
	 * @return array
	 * to set the schema for captain shop page with parameters like type,logo,name,address etc 
	 * which is read by Yoast schema
	 */
	public function chotu_set_captain_schema($user){
		$user_id = $user->ID;
		$shopOwner = get_userdata( $user_id );
		$captain_banner_pic= wp_get_attachment_image_url(get_user_meta($user_id,'captain_cover_pic',true),'wa_share');
		if(empty($captain_banner_pic)){
			$captain_banner_pic = get_option('captain_default_cover_pic');
		}
		 $captain_dp = wp_get_attachment_image_url(get_user_meta($user_id,'captain_display_pic',true),'wa_share');
		 $context = array("@context"=> "https://schema.org",
		 "@type" => "LocalBusiness",
		 "logo"=> array($captain_dp),
		 "name" => $shopOwner->display_name,
		 "address" => $this->get_captain_address($user_id),
		 "geo" => $this->get_captain_geos($user_id),
		 "url" => home_url().'/'.$user->user_login,
		 "telephone" => $user->user_login,
		 //"priceRange" => "₹₹",
		 "currenciesAccepted" => "₹",
		 "paymentAccepted" => "Cash, phonepe, gpay, paytm",
		 "areaServed" => $this->get_captain_areas_served($user_id),
		//  "keywords" => "organic, vegan",
		 "slogan" => "Pakka local",
		 "hasOfferCatalog"=>array(),
		 "review" => array(),
		 "aggregateRating"=>array(),
		 "photo"=> $captain_banner_pic,
		 "description" => get_user_meta($user_id,'description',true),
		 "openingHoursSpecification" => array(
			 array(
				 "@type" => "OpeningHoursSpecification",
				  "dayOfWeek" => array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"),
				 "opens" => "00:00",
				 "closes" => "23:59"
				 )
			 )
		 );
		 return $context;
	}
	
	

	/**
	 * get_captain_address
	 *
	 * @param  mixed $user_id
	 * @return array
	 * to return the captain shop address which is used in setting schema
	 */
	public function get_captain_address($user_id){
		$captain_address = get_user_meta($user_id,'captain_display_address',true);
        $data = array("@type" => "PostalAddress","streetAddress" => $captain_address,"addressLocality" => "","addressRegion" => "","addressCountry" => "IN");
        return $data;
	}	



	/**
	 * get_captain_geos
	 *
	 * @param  mixed $user_id
	 * @return array
	 * to fetch the captain lat long which is set in schema for the captain shop page
	 */
	public function get_captain_geos($user_id){
		$lat_longs = array();
        $lat_long = get_user_meta($user_id, 'captain_lat_long', true);
       if($lat_long){
            $lat_longs = explode(",",$lat_long);
            $data =  array("@type" => "GeoCoordinates","latitude" => $lat_longs[0],"longitude" => $lat_longs[1] );
        }else{
           $data = array("@type" => "GeoCoordinates"); 
        }
        return $data;
	}
	
	

	/**
	 * get_captain_areas_served
	 *
	 * @param  mixed $user_id
	 * @return array[]
	 */
	public function get_captain_areas_served($user_id){
		$served_areas = array();
        $captain_captain_areas = explode(",",get_user_meta($user_id,'captain_area_served',true));
        if(!empty($captain_geography)){
            foreach ($captain_captain_areas as $key => $areas) {
                $served_areas[$key]['@type']= "AdministrativeArea";
                $served_areas[$key]['name']= $areas;
            }
        }
        return $served_areas;
	}	
	
}
new Chotu_Shop_Public();