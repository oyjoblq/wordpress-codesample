<?php
/**
 * Twenty Fifteen functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */


// Disable WooCommerce Image Regeneration for High CPU
add_filter( 'woocommerce_background_image_regeneration', '__return_false' );



/** Custom Login Redirect **/
function my_login_redirect( $redirect_to, $request, $user )
{
	global $user;
	if ( $redirect_to == null || $redirect_to == '' ) {
		if( isset( $user->roles ) && is_array( $user->roles ) ) {
			if(
				in_array( "administrator", $user->roles ) ||
				in_array( "editor", $user->roles ) ||
				in_array( "author", $user->roles ) ||
				in_array( "contributor", $user->roles )
			) {
				return home_url() . '/wp-admin/';
			} elseif ( in_array( "employer", $user->roles ) ) {
				return home_url() . '/wp-admin/post-new.php?post_type=job_listing';
			} elseif ( in_array( "host", $user->roles ) ) {
				return home_url() . '/wp-admin/post-new.php?post_type=tribe_events';
			} else {
				return home_url() . '/account/';
			}
		} else {
			return home_url() . '/account/';
		}
	} else {
		return $redirect_to;
	}
}
add_filter( "login_redirect", "my_login_redirect", 10, 3 );


/** Adding Custom Events Management Link to Admin WP Screen **/
function custom_events_mgmt_link() {
	add_submenu_page(
		'edit.php?post_type=tribe_events',
		'Events Management',
		'Events Management',
		'read_private_pages',
		'../events-management'
	);
}
add_action( 'admin_menu', 'custom_events_mgmt_link' );



/** Custom Tribe Events Functions **/
remove_action( 'tribe_events_single_event_after_the_meta', 'tribe_single_related_events' );



/** Modify WP Users search to search in email, first name, last name **/
function modify_wp_users_search( $vars ) {
	global $pagenow;
	if ( !is_admin() ) {
		return;
	} else {
		if ( $pagenow === 'users.php' ) {

			write_log('search page load');
			$search_term = $vars->get( 'search' );
			write_log('pre-filter search term: '.$search_term);
			write_log($vars);
		} else {
			return;
		}
	}
}
add_action( 'pre_get_users', 'modify_wp_users_search' );


/**
 * Remove "Free -" from ticket page if exists
*/
function remove_free_dash_from_cost( $cost, $post_id, $with_currency_symbol ) {
	return str_replace( 'Free -', '', $cost );
}
add_filter( 'tribe_get_cost', 'remove_free_dash_from_cost', 10, 3 );


/** Custom Functions for the site **/

function sanitizePhoneNumber( $phone ) {
  $phone = str_replace(' ', '', $phone);
  $phone = str_replace('.', '', $phone);
  $phone = str_replace('-', '', $phone);
  $phone = str_replace('(', '', $phone);
  $phone = str_replace(')', '', $phone);
  $phone = str_replace('/', '', $phone);
  $phone = str_replace(',', '', $phone);
	return $phone;
}

function sanitizeWebsiteURL( $url ) {
	if ( $url != null && $url != '' ) {
		if ( strpos( $url, 'http', 0 ) === FALSE ) {
			$url = 'http://' . $url;
		}
	}
	return $url;
}

