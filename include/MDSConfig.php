<?php
/*
 * @package       mds
 * @copyright     (C) Copyright 2022 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2022-01-30 17:07:25 EST
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

/**
 * Class MDSConfig
 */
class MDSConfig {
	private static $config;

	/**
	 * MDSConfig constructor.
	 */
	public function __construct() {
	}

	/**
	 * Default config values.
	 *
	 * @return array
	 */
	public static function defaults(): array {

		$rootpathinfo = pathinfo( __DIR__ . '/../' );
		$BASE_PATH    = $rootpathinfo['dirname'];

		require_once realpath( __DIR__ . '/../include/version.php' );
		$build_date = get_mds_build_date();
		$version    = get_mds_version();

		return array(
			'DEBUG'                       => false,
			'MDS_LOG'                     => false,
			'MDS_LOG_FILE'                => $BASE_PATH . '/.mds.log',
			'BUILD_DATE'                  => $build_date,
			'VERSION_INFO'                => $version,
			'BASE_HTTP_PATH'              => '/',
			'BASE_PATH'                   => $BASE_PATH . '/',
			'SERVER_PATH_TO_ADMIN'        => __DIR__,
			'UPLOAD_PATH'                 => $BASE_PATH . '/upload_files/',
			'UPLOAD_HTTP_PATH'            => $BASE_PATH . '/upload_files/',
			'SITE_CONTACT_EMAIL'          => 'test@example.com',
			'SITE_LOGO_URL'               => 'https://milliondollarscript.com/logo.gif',
			'SITE_NAME'                   => 'Million Dollar Script ' . $version,
			'SITE_SLOGAN'                 => 'This is the Million Dollar Script Example. 1 pixel = 1 cent',
			'MDS_RESIZE'                  => 'YES',
			'MYSQL_HOST'                  => '',
			'MYSQL_USER'                  => '',
			'MYSQL_PASS'                  => '',
			'MYSQL_DB'                    => '',
			'MYSQL_PORT'                  => 3306,
			'MYSQL_SOCKET'                => '',
			'ADMIN_PASSWORD'              => 'ok',
			'DATE_FORMAT'                 => 'Y-M-d',
			'GMT_DIF'                     => date_default_timezone_get(),
			'DATE_INPUT_SEQ'              => 'YMD',
			'OUTPUT_JPEG'                 => 'N',
			'JPEG_QUALITY'                => '75',
			'INTERLACE_SWITCH'            => 'YES',
			'BANNER_DIR'                  => 'pixels/',
			'DISPLAY_PIXEL_BACKGROUND'    => 'NO',
			'EMAIL_USER_ORDER_CONFIRMED'  => 'YES',
			'EMAIL_ADMIN_ORDER_CONFIRMED' => 'YES',
			'EMAIL_USER_ORDER_COMPLETED'  => 'YES',
			'EMAIL_ADMIN_ORDER_COMPLETED' => 'YES',
			'EMAIL_USER_ORDER_PENDED'     => 'YES',
			'EMAIL_ADMIN_ORDER_PENDED'    => 'YES',
			'EMAIL_USER_ORDER_EXPIRED'    => 'YES',
			'EMAIL_ADMIN_ORDER_EXPIRED'   => 'YES',
			'EM_NEEDS_ACTIVATION'         => 'YES',
			'EMAIL_ADMIN_ACTIVATION'      => 'YES',
			'EMAIL_ADMIN_PUBLISH_NOTIFY'  => 'YES',
			'EMAIL_USER_EXPIRE_WARNING'   => '',
			'EMAILS_DAYS_KEEP'            => '30',
			'DAYS_RENEW'                  => '7',
			'DAYS_CONFIRMED'              => '7',
			'MINUTES_UNCONFIRMED'         => '60',
			'DAYS_CANCEL'                 => '3',
			'ENABLE_MOUSEOVER'            => 'POPUP',
			'ENABLE_CLOAKING'             => 'YES',
			'VALIDATE_LINK'               => 'NO',
			'ADVANCED_CLICK_COUNT'        => 'YES',
			'ADVANCED_VIEW_COUNT'         => 'YES',
			'USE_SMTP'                    => '',
			'EMAIL_SMTP_SERVER'           => '',
			'EMAIL_SMTP_USER'             => '',
			'EMAIL_SMTP_PASS'             => '',
			'EMAIL_SMTP_AUTH_HOST'        => '',
			'SMTP_PORT'                   => '465',
			'POP3_PORT'                   => '995',
			'EMAIL_TLS'                   => '1',
			'EMAIL_POP_SERVER'            => '',
			'EMAIL_POP_BEFORE_SMTP'       => 'NO',
			'EMAIL_DEBUG'                 => 'NO',
			'EMAILS_PER_BATCH'            => '12',
			'EMAILS_MAX_RETRY'            => '15',
			'EMAILS_ERROR_WAIT'           => '20',
			'USE_AJAX'                    => 'SIMPLE',
			'MEMORY_LIMIT'                => '128M',
			'REDIRECT_SWITCH'             => 'NO',
			'REDIRECT_URL'                => 'https://www.example.com',
			'MDS_AGRESSIVE_CACHE'         => 'NO',
			'BLOCK_SELECTION_MODE'        => 'YES',
			'ERROR_REPORTING'             => 0,
			'WP_ENABLED'                  => 'NO',
			'WP_URL'                      => '',
			'WP_PATH'                     => '',
			'WP_USERS_ENABLED'            => 'NO',
			'WP_ADMIN_ENABLED'            => 'NO',
			'WP_USE_MAIL'                 => 'NO',
		);
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public static function format( $value ): string {
		global $f2;

		return $f2->value( $value, false );
	}

	/**
	 * Get config value from the database.
	 *
	 * @param $key
	 * @param bool $format
	 *
	 * @return false|object
	 */
	public static function get( $key, $format = false ) {
		if ( isset( self::$config ) && is_array( self::$config ) && isset( self::$config[ $key ] ) ) {
			if ( $format ) {
				return self::format( self::$config[ $key ] );
			}

			return self::$config[ $key ];
		}

		$sql = "SELECT `val` FROM `config` WHERE `key` = ?";

		$stmt = mysqli_stmt_init( $GLOBALS['connection'] );
		if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
			die ( mds_sql_error( $sql ) );
		}

		mysqli_stmt_bind_param( $stmt, "s", $key );
		mysqli_stmt_execute( $stmt );

		mysqli_stmt_bind_result( $stmt, $value );
//		$result = mysqli_stmt_get_result( $stmt );
//		$value  = mysqli_fetch_field( $result );

		$error = mysqli_stmt_error( $stmt );
		if ( ! empty( $error ) ) {
			die ( mds_sql_error( $sql ) );
		}

		mysqli_stmt_close( $stmt );

		if ( $format ) {
			return self::format( $value );
		}

		return $value;
	}

	/**
	 * Load all of the config from the database.
	 *
	 * @return array|false|string[]|null
	 */
	public static function load() {
		$sql    = "SELECT `key`, `val` FROM `config`";
		$result = mysqli_query( $GLOBALS['connection'], $sql );
		$rows   = [];
		while ( $row = $result->fetch_assoc() ) {
			$rows[] = $row;
		}

		if ( ! is_array( $rows ) ) {
			return null;
		}

		$config = [];

		// normalize the array
		foreach ( $rows as $row ) {
			$config[ $row['key'] ] = $row['val'];
		}

		// Add MySQL details from config.php
		$config['MYSQL_HOST']   = MYSQL_HOST;
		$config['MYSQL_USER']   = MYSQL_USER;
		$config['MYSQL_PASS']   = MYSQL_PASS;
		$config['MYSQL_DB']     = MYSQL_DB;
		$config['MYSQL_PORT']   = MYSQL_PORT;
		$config['MYSQL_SOCKET'] = MYSQL_SOCKET;

		self::$config = $config;

		return $config;
	}
}