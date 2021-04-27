<?php
/*
 * @package       mds
 * @copyright     (C) Copyright 2021 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2021.01.05 13:41:53 EST
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

require_once __DIR__ . '/admin_common.php';

if ( isset( $_REQUEST['save'] ) && $_REQUEST['save'] != '' ) {

	global $f2;

	echo "Updating config....";

	// Arrays of queries sorted by variable types for preparing for database.
	$types = [

		// strings
		's' => [
			'MDS_LOG_FILE'                => "REPLACE INTO `config` VALUES ('MDS_LOG_FILE', ?);",
			'VERSION_INFO'                => "REPLACE INTO `config` VALUES ('VERSION_INFO', ?);",
			'BASE_HTTP_PATH'              => "REPLACE INTO `config` VALUES ('BASE_HTTP_PATH', ?);",
			'BASE_PATH'                   => "REPLACE INTO `config` VALUES ('BASE_PATH', ?);",
			'SERVER_PATH_TO_ADMIN'        => "REPLACE INTO `config` VALUES ('SERVER_PATH_TO_ADMIN', ?);",
			'UPLOAD_PATH'                 => "REPLACE INTO `config` VALUES ('UPLOAD_PATH', ?);",
			'UPLOAD_HTTP_PATH'            => "REPLACE INTO `config` VALUES ('UPLOAD_HTTP_PATH', ?);",
			'SITE_CONTACT_EMAIL'          => "REPLACE INTO `config` VALUES ('SITE_CONTACT_EMAIL', ?);",
			'SITE_LOGO_URL'               => "REPLACE INTO `config` VALUES ('SITE_LOGO_URL', ?);",
			'SITE_NAME'                   => "REPLACE INTO `config` VALUES ('SITE_NAME', ?);",
			'SITE_SLOGAN'                 => "REPLACE INTO `config` VALUES ('SITE_SLOGAN', ?);",
			'MDS_RESIZE'                  => "REPLACE INTO `config` VALUES ('MDS_RESIZE', ?);",
			'ADMIN_PASSWORD'              => "REPLACE INTO `config` VALUES ('ADMIN_PASSWORD', ?);",
			'DATE_FORMAT'                 => "REPLACE INTO `config` VALUES ('DATE_FORMAT', ?);",
			'GMT_DIF'                     => "REPLACE INTO `config` VALUES ('GMT_DIF', ?);",
			'DATE_INPUT_SEQ'              => "REPLACE INTO `config` VALUES ('DATE_INPUT_SEQ', ?);",
			'OUTPUT_JPEG'                 => "REPLACE INTO `config` VALUES ('OUTPUT_JPEG', ?);",
			'INTERLACE_SWITCH'            => "REPLACE INTO `config` VALUES ('INTERLACE_SWITCH', ?);",
			'BANNER_DIR'                  => "REPLACE INTO `config` VALUES ('BANNER_DIR', ?);",
			'DISPLAY_PIXEL_BACKGROUND'    => "REPLACE INTO `config` VALUES ('DISPLAY_PIXEL_BACKGROUND', ?);",
			'EMAIL_USER_ORDER_CONFIRMED'  => "REPLACE INTO `config` VALUES ('EMAIL_USER_ORDER_CONFIRMED', ?);",
			'EMAIL_ADMIN_ORDER_CONFIRMED' => "REPLACE INTO `config` VALUES ('EMAIL_ADMIN_ORDER_CONFIRMED', ?);",
			'EMAIL_USER_ORDER_COMPLETED'  => "REPLACE INTO `config` VALUES ('EMAIL_USER_ORDER_COMPLETED', ?);",
			'EMAIL_ADMIN_ORDER_COMPLETED' => "REPLACE INTO `config` VALUES ('EMAIL_ADMIN_ORDER_COMPLETED', ?);",
			'EMAIL_USER_ORDER_PENDED'     => "REPLACE INTO `config` VALUES ('EMAIL_USER_ORDER_PENDED', ?);",
			'EMAIL_ADMIN_ORDER_PENDED'    => "REPLACE INTO `config` VALUES ('EMAIL_ADMIN_ORDER_PENDED', ?);",
			'EMAIL_USER_ORDER_EXPIRED'    => "REPLACE INTO `config` VALUES ('EMAIL_USER_ORDER_EXPIRED', ?);",
			'EMAIL_ADMIN_ORDER_EXPIRED'   => "REPLACE INTO `config` VALUES ('EMAIL_ADMIN_ORDER_EXPIRED', ?);",
			'EM_NEEDS_ACTIVATION'         => "REPLACE INTO `config` VALUES ('EM_NEEDS_ACTIVATION', ?);",
			'EMAIL_ADMIN_ACTIVATION'      => "REPLACE INTO `config` VALUES ('EMAIL_ADMIN_ACTIVATION', ?);",
			'EMAIL_ADMIN_PUBLISH_NOTIFY'  => "REPLACE INTO `config` VALUES ('EMAIL_ADMIN_PUBLISH_NOTIFY', ?);",
			'EMAIL_USER_EXPIRE_WARNING'   => "REPLACE INTO `config` VALUES ('EMAIL_USER_EXPIRE_WARNING', ?);",
			'ENABLE_MOUSEOVER'            => "REPLACE INTO `config` VALUES ('ENABLE_MOUSEOVER', ?);",
			'ENABLE_CLOAKING'             => "REPLACE INTO `config` VALUES ('ENABLE_CLOAKING', ?);",
			'VALIDATE_LINK'               => "REPLACE INTO `config` VALUES ('VALIDATE_LINK', ?);",
			'ADVANCED_CLICK_COUNT'        => "REPLACE INTO `config` VALUES ('ADVANCED_CLICK_COUNT', ?);",
			'ADVANCED_VIEW_COUNT'         => "REPLACE INTO `config` VALUES ('ADVANCED_VIEW_COUNT', ?);",
			'USE_SMTP'                    => "REPLACE INTO `config` VALUES ('USE_SMTP', ?);",
			'EMAIL_SMTP_SERVER'           => "REPLACE INTO `config` VALUES ('EMAIL_SMTP_SERVER', ?);",
			'EMAIL_SMTP_USER'             => "REPLACE INTO `config` VALUES ('EMAIL_SMTP_USER', ?);",
			'EMAIL_SMTP_PASS'             => "REPLACE INTO `config` VALUES ('EMAIL_SMTP_PASS', ?);",
			'EMAIL_SMTP_AUTH_HOST'        => "REPLACE INTO `config` VALUES ('EMAIL_SMTP_AUTH_HOST', ?);",
			'EMAIL_POP_SERVER'            => "REPLACE INTO `config` VALUES ('EMAIL_POP_SERVER', ?);",
			'EMAIL_POP_BEFORE_SMTP'       => "REPLACE INTO `config` VALUES ('EMAIL_POP_BEFORE_SMTP', ?);",
			'EMAIL_DEBUG'                 => "REPLACE INTO `config` VALUES ('EMAIL_DEBUG', ?);",
			'USE_AJAX'                    => "REPLACE INTO `config` VALUES ('USE_AJAX', ?);",
			'MEMORY_LIMIT'                => "REPLACE INTO `config` VALUES ('MEMORY_LIMIT', ?);",
			'ERROR_REPORTING'             => "REPLACE INTO `config` VALUES ('ERROR_REPORTING', ?);",
			'REDIRECT_SWITCH'             => "REPLACE INTO `config` VALUES ('REDIRECT_SWITCH', ?);",
			'REDIRECT_URL'                => "REPLACE INTO `config` VALUES ('REDIRECT_URL', ?);",
			'MDS_AGRESSIVE_CACHE'         => "REPLACE INTO `config` VALUES ('MDS_AGRESSIVE_CACHE', ?);",
			'BLOCK_SELECTION_MODE'        => "REPLACE INTO `config` VALUES ('BLOCK_SELECTION_MODE', ?);",
			'WP_ENABLED'                  => "REPLACE INTO `config` VALUES ('WP_ENABLED', ?);",
			'WP_URL'                      => "REPLACE INTO `config` VALUES ('WP_URL', ?);",
			'WP_PATH'                     => "REPLACE INTO `config` VALUES ('WP_PATH', ?);",
			'WP_USERS_ENABLED'            => "REPLACE INTO `config` VALUES ('WP_USERS_ENABLED', ?);",
			'WP_ADMIN_ENABLED'            => "REPLACE INTO `config` VALUES ('WP_ADMIN_ENABLED', ?);",
			'WP_USE_MAIL'                 => "REPLACE INTO `config` VALUES ('WP_USE_MAIL', ?);"
		],

		// integers
		'i' => [
			'DEBUG'               => "REPLACE INTO `config` VALUES ('DEBUG', ?);",
			'MDS_LOG'             => "REPLACE INTO `config` VALUES ('MDS_LOG', ?);",
			'JPEG_QUALITY'        => "REPLACE INTO `config` VALUES ('JPEG_QUALITY', ?);",
			'EMAILS_DAYS_KEEP'    => "REPLACE INTO `config` VALUES ('EMAILS_DAYS_KEEP', ?);",
			'DAYS_RENEW'          => "REPLACE INTO `config` VALUES ('DAYS_RENEW', ?);",
			'DAYS_CONFIRMED'      => "REPLACE INTO `config` VALUES ('DAYS_CONFIRMED', ?);",
			'MINUTES_UNCONFIRMED' => "REPLACE INTO `config` VALUES ('MINUTES_UNCONFIRMED', ?);",
			'DAYS_CANCEL'         => "REPLACE INTO `config` VALUES ('DAYS_CANCEL', ?);",
			'SMTP_PORT'           => "REPLACE INTO `config` VALUES ('SMTP_PORT', ?);",
			'POP3_PORT'           => "REPLACE INTO `config` VALUES ('POP3_PORT', ?);",
			'EMAIL_TLS'           => "REPLACE INTO `config` VALUES ('EMAIL_TLS', ?);",
			'EMAILS_PER_BATCH'    => "REPLACE INTO `config` VALUES ('EMAILS_PER_BATCH', ?);",
			'EMAILS_MAX_RETRY'    => "REPLACE INTO `config` VALUES ('EMAILS_MAX_RETRY', ?);",
			'EMAILS_ERROR_WAIT'   => "REPLACE INTO `config` VALUES ('EMAILS_ERROR_WAIT', ?);",
		]

		// doubles

		// blobs
	];

	$values = array_replace( MDSConfig::defaults(), $_REQUEST );

	foreach ( $types as $type => $queries ) {
		foreach ( $queries as $key => $query ) {
			$stmt = mysqli_stmt_init( $GLOBALS['connection'] );
			if ( ! mysqli_stmt_prepare( $stmt, $query ) ) {
				die ( mds_sql_error( $query ) );
			}

			$var = $values[ $key ];
			mysqli_stmt_bind_param( $stmt, $type, $var );

			mysqli_stmt_execute( $stmt );
			$res   = mysqli_stmt_get_result( $stmt );
			$error = mysqli_stmt_error( $stmt );
			if ( ! empty( $error ) ) {
				die ( mds_sql_error( $query ) );
			}
			mysqli_stmt_close( $stmt );
		}
	}

	$config_str = "<?php
/**
 * Million Dollar Script Configuration
 * Note: Please do not edit this file. Edit in MDS admin under Main Config.
 */

error_reporting( " . $f2->slashes( $values['ERROR_REPORTING'] ) . " );
@ini_set( 'display_errors', 0 );

const MYSQL_HOST = '" . $f2->slashes( $values['MYSQL_HOST'] ) . "';
const MYSQL_USER = '" . $f2->slashes( $values['MYSQL_USER'] ) . "';
const MYSQL_PASS = '" . $f2->slashes( $values['MYSQL_PASS'] ) . "';
const MYSQL_DB   = '" . $f2->slashes( $values['MYSQL_DB'] ) . "';
const MYSQL_PORT = " . intval( $values['MYSQL_PORT'] ) . ";
const MYSQL_SOCKET = '" . $f2->slashes( $values['MYSQL_SOCKET'] ) . "';
";

	// write out the config..
	$file = fopen( "../config.php", "w" );
	fwrite( $file, $config_str );
	fclose( $file );

	?>
    <script>
		$(function () {
			$(document).scrollTop(0);
			window.location.reload();
		});
    </script>
	<?php
}

require_once __DIR__ . "/../include/init.php";

?>

<h3>Main Configuration</h3>
<p>Options on this page affect the running of the pixel advertising system.</p>
<p>Note: <i>Make sure that config.php has write permissions <b>turned on</b> when editing this form. You should turn off write permission after editing this form.</i></p>
<p><b>Tip:</b> Looking for where to settings for the grid? It is set in 'Pixel Inventory' -> <a href="inventory.php">Manage Grids</a>. Click on Edit to edit the grid parameters.</p>
<p>
	<?php
	if ( is_writable( "../config.php" ) ) {
		echo "- config.php is writeable.";
	} else {
		echo "- <div style='color:red;'> Note: config.php is not writable. Give write permissions to config.php if you want to save the changes</div>";
	}

	require( __DIR__ . '/config_form.php' );
	?>
</p>
