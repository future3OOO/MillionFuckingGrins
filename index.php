<?php
/**
 * @package       mds
 * @copyright     (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2020.05.08 17:42:17 EDT
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

// check if a config.php exists, if not then rename the default one and redirect to install
if ( ! file_exists( __DIR__ . "/config.php" ) ) {
	if ( file_exists( __DIR__ . "/config-default.php" ) ) {
		if ( rename( __DIR__ . "/config-default.php", __DIR__ . "/config.php" ) ) {
			$host     = $_SERVER['HTTP_HOST'];
			$uri      = rtrim( dirname( $_SERVER['PHP_SELF'] ), '/\\' );
			$protocol = ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) ? "https://" : "http://";
			$loc      = $protocol . $host . $uri . "/admin/install.php";
			header( "Location: $loc" );
			header( "X-Frame-Options: allow-from " . $protocol . $host . $uri . "/" );
		}
	}
	echo "The file config.php was not found and I was unable to automatically rename it. You may have to manually rename config-default.php to config.php and then visit $loc to install the script.";
	exit;
}

// include the config file
require_once __DIR__ . "/include/init.php";

global $f2;
$BID = $f2->bid();

// include the header
require_once( __DIR__ . "/html/header.php" );

// Displays the grid image map. Use Process Pixels in the admin to update the image map.
require_once( __DIR__ . "/include/mds_ajax.php" );
$mds_ajax = new Mds_Ajax();
$mds_ajax->show( 'grid', $BID, 'grid' );

// include footer
require_once( __DIR__ . "/html/footer.php" );
