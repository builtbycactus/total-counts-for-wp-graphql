<?php
/**
 * Plugin Name:     Total Counts for WPGraphQL
 * Plugin URI:      https://github.com/builtbycactus/total-counts-for-wp-graphql
 * Description:     Adds Total Count to the WPGraphQL Schema
 * Author:          Built By Cactus
 * Author URI:      https://builtbycactus.co.uk/
 * Text Domain:     cactus-gqltc
 * Domain Path:     /languages
 * Version:         0.0.2
 */

namespace Cactus\GQLTC;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initialize the plugin
 */
function init() {
	/**
	 * If WPGraphQL is not active, show the admin notice
	 */
	if ( ! class_exists( 'WPGraphQL' ) ) {
		add_action( 'admin_init', __NAMESPACE__ . '\show_admin_notice' );
	} else {
		add_filter( 'graphql_connection_page_info', __NAMESPACE__ . '\resolve_total_field', 10, 2 );
		add_filter( 'graphql_connection_query_args', __NAMESPACE__ . '\count_total_rows' );
		add_filter( 'graphql_register_types', __NAMESPACE__ . '\register_total_field' );
	}
}

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Show admin notice to admins if this plugin is active but either ACF and/or WPGraphQL
 * are not active
 *
 * @return bool
 */
function show_admin_notice() {
	/**
	 * For users with lower capabilities, don't show the notice
	 */
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	add_action(
		'admin_notices',
		function () {
			?>
            <div class="error notice">
                <p><?php _e( 'WPGraphQL must be active for "total-counts-for-wpgraphql" to work',
						'cactus-gqltc' ); ?></p>
            </div>
			<?php
		}
	);

	return true;
}

/**
 * Resolve the total field.
 *
 * @param $page_info
 * @param $connection
 *
 * @return mixed
 */
function resolve_total_field( $page_info, $connection ) {
	$page_info['total'] = null;
	if ( $connection->get_query() instanceof \WP_Query ) {
		if ( isset( $connection->get_query()->found_posts ) ) {
			$page_info['total'] = (int) $connection->get_query()->found_posts;
		}
	} elseif ( $connection->get_query() instanceof \WP_User_Query ) {
		if ( isset( $connection->get_query()->total_users ) ) {
			$page_info['total'] = (int) $connection->get_query()->total_users;
		}
	}

	return $page_info;
}

/**
 * Tell the underlying WP_Query to count the total number of rows.
 *
 * @param $args
 *
 * @return mixed
 */
function count_total_rows( $args ) {
	$args['no_found_rows'] = false;
	$args['count_total']   = true;

	return $args;
}

/**
 * Register a total field for queries.
 */
function register_total_field() {
	register_graphql_field( 'WPPageInfo', 'total', [
		'type' => 'Int',
	] );
}

;