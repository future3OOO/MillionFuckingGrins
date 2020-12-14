<?php
/*
 * @package       mds
 * @copyright     (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2020.11.13 08:56:56 EST
 * @license       This program is free software; you can redistribute it and/or modify
 *        it under the terms of the GNU General Public License as published by
 *        the Free Software Foundation; either version 3 of the License, or
 *        (at your option) any later version.
 *
 *        This program is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *        GNU General Public License for more details.
 *
 *        You should have received a copy of the GNU General Public License along
 *        with this program;  If not, see http://www.gnu.org/licenses/gpl-3.0.html.
 *
 *  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *        Million Dollar Script
 *        A pixel script for selling pixels on your website.
 *
 *        For instructions see README.txt
 *
 *        Visit our website for FAQs, documentation, a list team members,
 *        to post any bugs or feature requests, and a community forum:
 *        https://milliondollarscript.com/
 *
 */

function mds_load_wp() {

	$wpdomain = parse_url( WP_URL );

	define( 'COOKIE_DOMAIN', '.' . $wpdomain['host'] );
	define( 'COOKIEPATH', '/' );
	define( 'COOKIEHASH', md5( $wpdomain['host'] ) );

	require_once WP_PATH . '/wp-load.php';
	require_once WP_PATH . '/wp-includes/pluggable.php';
}

function mds_wp_login_check() {
	if ( WP_ENABLED == "YES" && WP_USERS_ENABLED == "YES" ) {
		// If WP integration is enabled then redirect to WP login page if not logged in

		mds_load_wp();

		if ( ! is_user_logged_in() ) {
			wp_redirect( wp_login_url( BASE_HTTP_PATH . 'users/index.php' ) );
			exit;
		}
	}
}