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

/**
 * Get sanitized language code from session
 *
 * @param string $lang
 *
 * @return null|string|string[]
 */
function get_lang( $lang = "" ) {
	if ( empty( $lang ) ) {
		if ( ! isset( $_SESSION['MDS_LANG'] ) ) {
			$_SESSION['MDS_LANG'] = get_default_lang();
		}
		$lang = $_SESSION['MDS_LANG'];
	}

	return preg_replace( '/[^a-zA-Z0-9]/', '', $lang );
}

/**
 * Get the default language.
 *
 * @return null|string|string[]
 */
function get_default_lang() {
	$lang = "EN";

	$sql = "SELECT * FROM lang WHERE `is_default`='Y'";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
	$row = mysqli_fetch_array( $result, MYSQLI_ASSOC );

	if ( ! empty( $row["lang_code"] ) ) {
		$lang = get_lang( $row["lang_code"] );
	}

	return $lang;
}

global $f2;
if ( isset( $_REQUEST["lang"] ) && $_REQUEST["lang"] != '' && basename( $_SERVER['PHP_SELF'] ) != "thanks.php" ) {
	$r_lang = get_lang( $_REQUEST['lang'] );

	$sql = "SELECT * FROM lang WHERE `lang_code`='" . mysqli_real_escape_string( $GLOBALS['connection'], $r_lang ) . "'";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

	if ( mysqli_num_rows( $result ) > 0 ) {
		$_SESSION['MDS_LANG'] = $r_lang;
		// save the requested language
		@setcookie( "MDS_SAVED_LANG", $r_lang, [
			'expires'  => time() + 86400,
			'path'     => '/',
			'secure'   => true,
			'samesite' => 'Strict',
		] );
	} else {
		$sql = "SELECT * FROM lang WHERE `is_default`='Y'";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
		$row                  = mysqli_fetch_array( $result, MYSQLI_ASSOC );
		$_SESSION['MDS_LANG'] = get_lang( $row["lang_code"] );
		// save the requested language
		@setcookie( "MDS_SAVED_LANG", get_lang( $row["lang_code"] ), [
			'expires'  => time() + 86400,
			'path'     => '/',
			'secure'   => true,
			'samesite' => 'Strict',
		] );
		//echo "Invalid language. Reverting to default language.";
	}
} else if ( isset( $_SESSION['MDS_LANG'] ) && $_SESSION['MDS_LANG'] == '' ) {

	// get the default language, or saved language
	if ( isset( $_COOKIE['MDS_SAVED_LANG'] ) && $_COOKIE['MDS_SAVED_LANG'] != '' ) {
		$_SESSION['MDS_LANG'] = $_COOKIE['MDS_SAVED_LANG'];
	} else {

		// check if db is setup yet
		if ( isset( $dbhost ) && isset( $dbusername ) && isset( $database_name ) ) {
			if ( ! empty( $dbhost ) && ! empty( $dbusername ) && ! empty( $database_name ) ) {

				// set lang and locale
				$sql = "SELECT * FROM lang WHERE `is_default`='Y' ";
				if ( $result = mysqli_query( $GLOBALS['connection'], $sql ) ) {
					$row                  = mysqli_fetch_array( $result, MYSQLI_ASSOC );
					$_SESSION['MDS_LANG'] = $row['lang_code'];
					if ( $row['charset'] != '' ) {
						setlocale( LC_TIME, $row['charset'] );
					}
				}
			} else {
				// no db so use defaults
				$_SESSION['MDS_LANG'] = 'EN';
				setlocale( LC_TIME, 'en_US.utf8' );
			}
		}
	}
}

global $AVAILABLE_LANGS;
global $LANG_FILES;

// check if db is setup yet
if ( isset( $dbhost ) && isset( $dbusername ) && isset( $database_name ) ) {
	if ( ! empty( $dbhost ) && ! empty( $dbusername ) && ! empty( $database_name ) ) {

		// load languages into array.. map the language code to the filename
		// if mapping didn't work, default to english..

		$sql = "SELECT * FROM lang ";
		if ( $result = mysqli_query( $GLOBALS['connection'], $sql ) ) {
			while ( $row = mysqli_fetch_array( $result, MYSQLI_ASSOC ) ) {
				$AVAILABLE_LANGS[ $row['lang_code'] ] = $row['name'];
				$LANG_FILES[ $row['lang_code'] ]      = $row['lang_filename'];
			}
			if ( isset( $_SESSION ) && ( isset( $_SESSION['MDS_LANG'] ) && $_SESSION['MDS_LANG'] != '' ) ) {

				// Set language to default in session if it's not valid anymore
				if ( ( ! in_array( get_lang(), $AVAILABLE_LANGS ) ) || ( get_default_lang() != get_lang() ) ) {

					if ( isset( $_COOKIE['MDS_SAVED_LANG'] ) && ! isset( $AVAILABLE_LANGS[ $_COOKIE['MDS_SAVED_LANG'] ] ) ) {
						$_SESSION['MDS_LANG'] = get_default_lang();
						@setcookie( "MDS_SAVED_LANG", $_SESSION['MDS_LANG'], [
							'expires'  => time() + 86400,
							'path'     => '/',
							'secure'   => true,
							'samesite' => 'Strict',
						] );
					} else if ( ! isset( $_COOKIE['MDS_SAVED_LANG'] ) ) {
						$_SESSION['MDS_LANG'] = get_default_lang();
					}
				}

				include dirname( __FILE__ ) . "/" . $LANG_FILES[ $_SESSION['MDS_LANG'] ];
			} else {
				$_SESSION['MDS_LANG'] = "EN";
				include dirname( __FILE__ ) . "/english.php";
			}
		} else {
			$DB_ERROR = mysqli_error( $GLOBALS['connection'] );
		}
	} else {
		// no db so use defaults
		$_SESSION['MDS_LANG'] = "EN";
		include dirname( __FILE__ ) . "/english.php";
	}
}

function mds_stripslashes( &$val, $key ) {
	$val = stripslashes( $val );
}

array_walk( $label, 'mds_stripslashes' );
