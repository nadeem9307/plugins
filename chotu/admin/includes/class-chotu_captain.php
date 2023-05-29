<?php
/**
 * Chotu_Captain class
 */

//Ravi Comment

class Chotu_Captain{	
	/**
	 * captain_id
	 *
	 * @var mixed
	 */
	public $captain_id;


	
	/**
	 * __construct
	 *
	 * @param  mixed $captain_id
	 * @return void
	 */
	public function __construct( ) {
		// to list the captains in descending order as per the date of registration in wp-admin
		/**
		 * https://developer.wordpress.org/reference/hooks/users_list_table_query_args/
		 */
		add_filter( 'users_list_table_query_args', function($args){
			$args["orderby"] = "user_registered";
			$args["order"] = "DESC";
			return $args;
		} );


		//  to HIDE the user social profiles in the SEO yoast schema in wp-admin
		/**
		 * https://wp-kama.ru/plugin/yoast/hook/wpseo_schema_person_social_profiles
		 */
		add_filter( 'wpseo_schema_person_social_profiles',function($profiles){
			$profiles = array();
			return $profiles;
		}, 10,1 );

		/* to remove social contact fields of captain in wp-admin side */
		/**
		 * https://developer.wordpress.org/reference/hooks/user_contactmethods/
		 */
		add_filter('user_contactmethods', function(){
			return array();
		}, 99);

		// to load the edit template passed as an arguement in the function
		/**
		 * https://developer.wordpress.org/reference/hooks/edit_user_profile/
		 */
        add_action( 'edit_user_profile', function($captain) {
			chotu_admin_template('chotu_captain.php',$captain);
		});

		// to load the template passed in the function
		/**
		 * https://developer.wordpress.org/reference/hooks/show_user_profile/
		 */
		add_action( 'show_user_profile', function($captain) {
			chotu_admin_template('chotu_captain.php',$captain);
		});

		// update the meta value for Captain Shop Feed Oncreate and Captain Shop Feed History
		/**
		 * https://developer.wordpress.org/reference/hooks/edit_user_profile_update/
		 */
		add_action( 'edit_user_profile_update', function ($captain_id){
			if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $captain_id ) ) {
				return;
			}
			if ( !current_user_can( 'edit_user', $captain_id ) ) { 
				return false; 
			}
			if(!empty($_POST['captain_shop_feed_oncreate'])){
				update_user_meta( $captain_id, 'captain_shop_feed_oncreate', $_POST['captain_shop_feed_oncreate'] );
			}else{
				update_user_meta( $captain_id, 'captain_shop_feed_oncreate', '' );
			}
			if(!empty($_POST['captain_shop_feed_history'])){
				update_user_meta( $captain_id, 'captain_shop_feed_history', $_POST['captain_shop_feed_history'] );
			}else{
				update_user_meta( $captain_id, 'captain_shop_feed_history', '' );
			}
		});

		add_filter( 'woocommerce_customer_meta_fields',function( $show_fields ) {
			unset( $show_fields['shipping'] );
			unset( $show_fields['billing'] );
			return $show_fields;
		});

	}
}
new Chotu_Captain();

function new_modify_user_table( $column ) {
	unset($column['email']);
	unset($column['name']);
	unset($column['role']);
	unset($column['posts']);
    $column['username'] = 'Phone';
    $column['display_name'] = 'User Display Name';
    $column['added_date'] = 'Registered Date/Time';
    $column['captain_rootshop'] = 'Rootshop';
    $column['captain_language'] = 'Language';
    $column['captain_latlong'] = 'Location';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'display_name' :
            return get_the_author_meta( 'display_name', $user_id );
        case 'added_date' :
            return get_the_author_meta( 'display_name', $user_id );
		case 'captain_rootshop' :
			$captain_rootshop =  chotu_get_cpost(get_the_author_meta( 'captain_rootshop', $user_id ),'rootshop');
			return $captain_rootshop->post_title;
		case 'captain_language' :
			return get_the_author_meta( 'captain_language', $user_id );
		case 'captain_latlong' :
			return '<a href="'.get_the_author_meta( 'captain_lat_long', $user_id).'">'.get_the_author_meta( 'captain_lat_long', $user_id).'</a>';
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );