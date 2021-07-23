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

$dbhost        = stripslashes( MYSQL_HOST );
$dbusername    = stripslashes( MYSQL_USER );
$dbpassword    = stripslashes( MYSQL_PASS );
$database_name = stripslashes( MYSQL_DB );

if ( ! defined( 'MYSQL_PORT' ) ) {
	define( 'MYSQL_PORT', 3306 );
}
if ( ! defined( 'MYSQL_SOCKET' ) ) {
	define( 'MYSQL_SOCKET', "" );
}
$database_port   = intval( MYSQL_PORT );
$database_socket = stripslashes( MYSQL_SOCKET );

if ( isset( $dbhost ) && isset( $dbusername ) && isset( $database_name ) && isset( $database_port ) ) {
	if ( ! empty( $dbhost ) && ! empty( $dbusername ) && ! empty( $database_name ) && ! empty( $database_port ) ) {
		if ( isset( $database_socket ) && ! empty( $database_socket ) ) {
			$GLOBALS['connection'] = mysqli_connect( "$dbhost", "$dbusername", "$dbpassword", "$database_name", "$database_port", "$database_socket" );
		} else {
			$GLOBALS['connection'] = mysqli_connect( "$dbhost", "$dbusername", "$dbpassword", "$database_name", "$database_port" );
		}
		if ( mysqli_connect_errno() ) {
			echo mysqli_connect_error();
			exit();
		}
		$db = mysqli_select_db( $GLOBALS['connection'], "$database_name" ) or die( mysqli_error( $GLOBALS['connection'] ) );
		mysqli_set_charset( $GLOBALS['connection'], 'utf8' ) or die( mysqli_error( $GLOBALS['connection'] ) );
	}
}

/**
 * Returns SQL error output for debug purposes.
 *
 * @param $sql
 *
 * @return string
 */
function mds_sql_error( $sql ): string {
	return "<br />SQL:[" . htmlspecialchars( $sql, ENT_QUOTES ) . "]<br />ERROR:[" . htmlspecialchars( mysqli_error( $GLOBALS['connection'] ), ENT_QUOTES ) . "]<br />";
}

/**
 * Log SQL error to debug log and optionally exit.
 *
 * @param $sql
 * @param bool $exit
 */
function mds_sql_log_die( $sql, $exit = true ) {
	global $f2;
	$f2->write_log( 'SQL error: ' . mysqli_error( $GLOBALS['connection'] ) );
	$f2->write_log( '$sql: ' . $sql );

	if ( $exit ) {
		exit;
	}
}

function mds_sql_installed() {
	$exists = mysqli_query( $GLOBALS['connection'], 'SELECT 1 FROM `config` LIMIT 1' );
	if ( $exists === false ) {
		return false;
	}

	return true;
}

/**
 * Database Upgrades
 */

// Don't do upgrades on install
if ( isset( $_POST['action'] ) && $_POST['action'] == "install" ) {
	return;
}

/**
 * Get database version
 *
 * @return int|void
 */
function get_dbver() {
	if ( ! mds_sql_installed() ) {
		return;
	}
	$sql = "SELECT `val` FROM `config` WHERE `key`='dbver';";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) );
	if ( mysqli_num_rows( $result ) == 0 ) {
		// add database version config value
		$sql = "INSERT INTO config(`key`, `val`) VALUES('dbver', 1);";
		mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) );
		$version = 1;
	} else {
		$dbver   = mysqli_fetch_array( $result, MYSQLI_ASSOC );
		$version = intval( $dbver['val'] );
	}

	return $version;
}

/**
 * Increment database version by 1
 */
function up_dbver() {
	$sql = "UPDATE `config` SET `val`=`val` + 1 WHERE `key`='dbver';";
	mysqli_query( $GLOBALS['connection'], $sql );
}

// No DB connection yet
if ( ! isset( $GLOBALS['connection'] ) || $GLOBALS['connection'] == false ) {
	return;
}

$version = get_dbver();

if ( $version == 1 ) {

	// add views table
	$sql    = "SELECT 1 FROM views;";
	$result = mysqli_query( $GLOBALS['connection'], $sql );
	if ( mysqli_num_rows( $result ) == 0 ) {
		$sql = "CREATE TABLE IF NOT EXISTS `views` (
            `banner_id` INT NOT NULL ,
            `block_id` INT NOT NULL ,
            `user_id` INT NOT NULL ,
            `date` date default '1970-01-01',
            `views` INT NOT NULL ,
            PRIMARY KEY ( `banner_id` , `block_id` ,  `date` )
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	// add view_count column to blocks table
	$sql    = "SELECT `view_count` FROM `blocks`;";
	$result = mysqli_query( $GLOBALS['connection'], $sql );
	if ( mysqli_num_rows( $result ) == 0 ) {
		$sql = "ALTER TABLE `blocks` ADD COLUMN `view_count` INT NOT NULL AFTER `click_count`;";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	up_dbver();
} else if ( $version == 2 ) {
	// Change block_info column to LONGTEXT
	$sql = "ALTER TABLE `temp_orders` MODIFY `block_info` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;";
	mysqli_query( $GLOBALS['connection'], $sql );

	up_dbver();
} else if ( $version == 3 ) {

	// Add config variables to config database table

	// Arrays of queries sorted by variable types for preparing for database.
	$types = [

		// strings
		's' => [
			'MDS_LOG_FILE'                => "INSERT INTO `config` VALUES ('MDS_LOG_FILE', ?);",
			'VERSION_INFO'                => "INSERT INTO `config` VALUES ('VERSION_INFO', ?);",
			'BASE_HTTP_PATH'              => "INSERT INTO `config` VALUES ('BASE_HTTP_PATH', ?);",
			'BASE_PATH'                   => "INSERT INTO `config` VALUES ('BASE_PATH', ?);",
			'SERVER_PATH_TO_ADMIN'        => "INSERT INTO `config` VALUES ('SERVER_PATH_TO_ADMIN', ?);",
			'UPLOAD_PATH'                 => "INSERT INTO `config` VALUES ('UPLOAD_PATH', ?);",
			'UPLOAD_HTTP_PATH'            => "INSERT INTO `config` VALUES ('UPLOAD_HTTP_PATH', ?);",
			'SITE_CONTACT_EMAIL'          => "INSERT INTO `config` VALUES ('SITE_CONTACT_EMAIL', ?);",
			'SITE_LOGO_URL'               => "INSERT INTO `config` VALUES ('SITE_LOGO_URL', ?);",
			'SITE_NAME'                   => "INSERT INTO `config` VALUES ('SITE_NAME', ?);",
			'SITE_SLOGAN'                 => "INSERT INTO `config` VALUES ('SITE_SLOGAN', ?);",
			'MDS_RESIZE'                  => "INSERT INTO `config` VALUES ('MDS_RESIZE', ?);",
			'ADMIN_PASSWORD'              => "INSERT INTO `config` VALUES ('ADMIN_PASSWORD', ?);",
			'DATE_FORMAT'                 => "INSERT INTO `config` VALUES ('DATE_FORMAT', ?);",
			'GMT_DIF'                     => "INSERT INTO `config` VALUES ('GMT_DIF', ?);",
			'DATE_INPUT_SEQ'              => "INSERT INTO `config` VALUES ('DATE_INPUT_SEQ', ?);",
			'OUTPUT_JPEG'                 => "INSERT INTO `config` VALUES ('OUTPUT_JPEG', ?);",
			'INTERLACE_SWITCH'            => "INSERT INTO `config` VALUES ('INTERLACE_SWITCH', ?);",
			'BANNER_DIR'                  => "INSERT INTO `config` VALUES ('BANNER_DIR', ?);",
			'DISPLAY_PIXEL_BACKGROUND'    => "INSERT INTO `config` VALUES ('DISPLAY_PIXEL_BACKGROUND', ?);",
			'EMAIL_USER_ORDER_CONFIRMED'  => "INSERT INTO `config` VALUES ('EMAIL_USER_ORDER_CONFIRMED', ?);",
			'EMAIL_ADMIN_ORDER_CONFIRMED' => "INSERT INTO `config` VALUES ('EMAIL_ADMIN_ORDER_CONFIRMED', ?);",
			'EMAIL_USER_ORDER_COMPLETED'  => "INSERT INTO `config` VALUES ('EMAIL_USER_ORDER_COMPLETED', ?);",
			'EMAIL_ADMIN_ORDER_COMPLETED' => "INSERT INTO `config` VALUES ('EMAIL_ADMIN_ORDER_COMPLETED', ?);",
			'EMAIL_USER_ORDER_PENDED'     => "INSERT INTO `config` VALUES ('EMAIL_USER_ORDER_PENDED', ?);",
			'EMAIL_ADMIN_ORDER_PENDED'    => "INSERT INTO `config` VALUES ('EMAIL_ADMIN_ORDER_PENDED', ?);",
			'EMAIL_USER_ORDER_EXPIRED'    => "INSERT INTO `config` VALUES ('EMAIL_USER_ORDER_EXPIRED', ?);",
			'EMAIL_ADMIN_ORDER_EXPIRED'   => "INSERT INTO `config` VALUES ('EMAIL_ADMIN_ORDER_EXPIRED', ?);",
			'EM_NEEDS_ACTIVATION'         => "INSERT INTO `config` VALUES ('EM_NEEDS_ACTIVATION', ?);",
			'EMAIL_ADMIN_ACTIVATION'      => "INSERT INTO `config` VALUES ('EMAIL_ADMIN_ACTIVATION', ?);",
			'EMAIL_ADMIN_PUBLISH_NOTIFY'  => "INSERT INTO `config` VALUES ('EMAIL_ADMIN_PUBLISH_NOTIFY', ?);",
			'EMAIL_USER_EXPIRE_WARNING'   => "INSERT INTO `config` VALUES ('EMAIL_USER_EXPIRE_WARNING', ?);",
			'ENABLE_MOUSEOVER'            => "INSERT INTO `config` VALUES ('ENABLE_MOUSEOVER', ?);",
			'ENABLE_CLOAKING'             => "INSERT INTO `config` VALUES ('ENABLE_CLOAKING', ?);",
			'VALIDATE_LINK'               => "INSERT INTO `config` VALUES ('VALIDATE_LINK', ?);",
			'ADVANCED_CLICK_COUNT'        => "INSERT INTO `config` VALUES ('ADVANCED_CLICK_COUNT', ?);",
			'ADVANCED_VIEW_COUNT'         => "INSERT INTO `config` VALUES ('ADVANCED_VIEW_COUNT', ?);",
			'USE_SMTP'                    => "INSERT INTO `config` VALUES ('USE_SMTP', ?);",
			'EMAIL_SMTP_SERVER'           => "INSERT INTO `config` VALUES ('EMAIL_SMTP_SERVER', ?);",
			'EMAIL_SMTP_USER'             => "INSERT INTO `config` VALUES ('EMAIL_SMTP_USER', ?);",
			'EMAIL_SMTP_PASS'             => "INSERT INTO `config` VALUES ('EMAIL_SMTP_PASS', ?);",
			'EMAIL_SMTP_AUTH_HOST'        => "INSERT INTO `config` VALUES ('EMAIL_SMTP_AUTH_HOST', ?);",
			'EMAIL_POP_SERVER'            => "INSERT INTO `config` VALUES ('EMAIL_POP_SERVER', ?);",
			'EMAIL_POP_BEFORE_SMTP'       => "INSERT INTO `config` VALUES ('EMAIL_POP_BEFORE_SMTP', ?);",
			'EMAIL_DEBUG'                 => "INSERT INTO `config` VALUES ('EMAIL_DEBUG', ?);",
			'USE_AJAX'                    => "INSERT INTO `config` VALUES ('USE_AJAX', ?);",
			'MEMORY_LIMIT'                => "INSERT INTO `config` VALUES ('MEMORY_LIMIT', ?);",
			'REDIRECT_SWITCH'             => "INSERT INTO `config` VALUES ('REDIRECT_SWITCH', ?);",
			'REDIRECT_URL'                => "INSERT INTO `config` VALUES ('REDIRECT_URL', ?);",
			'MDS_AGRESSIVE_CACHE'         => "INSERT INTO `config` VALUES ('MDS_AGRESSIVE_CACHE', ?);",
			'BLOCK_SELECTION_MODE'        => "INSERT INTO `config` VALUES ('BLOCK_SELECTION_MODE', ?);",
			'WP_ENABLED'                  => "INSERT INTO `config` VALUES ('WP_ENABLED', ?);",
			'WP_URL'                      => "INSERT INTO `config` VALUES ('WP_URL', ?);",
			'WP_PATH'                     => "INSERT INTO `config` VALUES ('WP_PATH', ?);",
			'WP_USERS_ENABLED'            => "INSERT INTO `config` VALUES ('WP_USERS_ENABLED', ?);",
			'WP_ADMIN_ENABLED'            => "INSERT INTO `config` VALUES ('WP_ADMIN_ENABLED', ?);",
			'WP_USE_MAIL'                 => "INSERT INTO `config` VALUES ('WP_USE_MAIL', ?);"
		],

		// integers
		'i' => [
			'DEBUG'               => "INSERT INTO `config` VALUES ('DEBUG', ?);",
			'MDS_LOG'             => "INSERT INTO `config` VALUES ('MDS_LOG', ?);",
			'JPEG_QUALITY'        => "INSERT INTO `config` VALUES ('JPEG_QUALITY', ?);",
			'EMAILS_DAYS_KEEP'    => "INSERT INTO `config` VALUES ('EMAILS_DAYS_KEEP', ?);",
			'DAYS_RENEW'          => "INSERT INTO `config` VALUES ('DAYS_RENEW', ?);",
			'DAYS_CONFIRMED'      => "INSERT INTO `config` VALUES ('DAYS_CONFIRMED', ?);",
			'MINUTES_UNCONFIRMED' => "INSERT INTO `config` VALUES ('MINUTES_UNCONFIRMED', ?);",
			'DAYS_CANCEL'         => "INSERT INTO `config` VALUES ('DAYS_CANCEL', ?);",
			'SMTP_PORT'           => "INSERT INTO `config` VALUES ('SMTP_PORT', ?);",
			'POP3_PORT'           => "INSERT INTO `config` VALUES ('POP3_PORT', ?);",
			'EMAIL_TLS'           => "INSERT INTO `config` VALUES ('EMAIL_TLS', ?);",
			'EMAILS_PER_BATCH'    => "INSERT INTO `config` VALUES ('EMAILS_PER_BATCH', ?);",
			'EMAILS_MAX_RETRY'    => "INSERT INTO `config` VALUES ('EMAILS_MAX_RETRY', ?);",
			'EMAILS_ERROR_WAIT'   => "INSERT INTO `config` VALUES ('EMAILS_ERROR_WAIT', ?);",
			'ERROR_REPORTING'     => "INSERT INTO `config` VALUES ('ERROR_REPORTING', ?);"
		]

		// doubles

		// blobs
	];

	foreach ( $types as $type => $queries ) {
		foreach ( $queries as $key => $query ) {
			$stmt = mysqli_stmt_init( $GLOBALS['connection'] );
			if ( ! mysqli_stmt_prepare( $stmt, $query ) ) {
				die ( mds_sql_error( $query ) );
			}

			$var = constant( $key );
			mysqli_stmt_bind_param( $stmt, $type, $var );

			mysqli_stmt_execute( $stmt );

			$error = mysqli_stmt_error( $stmt );
			if ( ! empty( $error ) ) {
				die ( mds_sql_error( $query ) );
			}
			mysqli_stmt_close( $stmt );
		}
	}

	up_dbver();
} else if ( $version == 4 ) {

	// add missing view_count column to users table
	$sql    = "SELECT `view_count` FROM `users`;";
	$result = mysqli_query( $GLOBALS['connection'], $sql );
	if ( $result && mysqli_num_rows( $result ) == 0 ) {
		$sql = "ALTER TABLE `users` ADD COLUMN `view_count` INT(11) NOT NULL default '0' AFTER `click_count`;";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	up_dbver();
} else if ( $version == 5 ) {

	// modify blocks.view_count column to have a default value
	$sql    = "SELECT `view_count` FROM `blocks`;";
	$result = mysqli_query( $GLOBALS['connection'], $sql );
	if ( $result && mysqli_num_rows( $result ) == 0 ) {
		$sql = "ALTER TABLE `blocks` MODIFY COLUMN `view_count` INT(11) NOT NULL default '0';";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	// modify blocks.click_count column to have a default value
	$sql    = "SELECT `click_count` FROM `blocks`;";
	$result = mysqli_query( $GLOBALS['connection'], $sql );
	if ( $result && mysqli_num_rows( $result ) == 0 ) {
		$sql = "ALTER TABLE `blocks` MODIFY COLUMN `click_count` INT(11) NOT NULL default '0';";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	up_dbver();
} else if ($version == 6) {
	$query = "INSERT INTO `config` VALUES ('TIME_FORMAT', ?);";
	$var = 'H:i:s';

	$stmt = mysqli_stmt_init( $GLOBALS['connection'] );
	if ( ! mysqli_stmt_prepare( $stmt, $query ) ) {
		die ( mds_sql_error( $query ) );
	}

	mysqli_stmt_bind_param( $stmt, 's', $var );

	mysqli_stmt_execute( $stmt );

	$error = mysqli_stmt_error( $stmt );
	if ( ! empty( $error ) ) {
		die ( mds_sql_error( $query ) );
	}
	mysqli_stmt_close( $stmt );

	up_dbver();
}
