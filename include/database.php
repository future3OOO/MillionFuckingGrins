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

$dbhost        = MYSQL_HOST;
$dbusername    = MYSQL_USER;
$dbpassword    = MYSQL_PASS;
$database_name = MYSQL_DB;

if ( ! defined( 'MYSQL_PORT' ) ) {
	define( 'MYSQL_PORT', 3306 );
}
if ( ! defined( 'MYSQL_SOCKET' ) ) {
	define( 'MYSQL_SOCKET', "" );
}
$database_port   = MYSQL_PORT;
$database_socket = MYSQL_SOCKET;

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
 * Database Upgrades
 */

// Don't do upgrades on install
if ( $_POST['action'] == "install" ) {
	return;
}

function up_dbver() {
	$sql = "UPDATE `config` SET `val`=`val` + 1 WHERE `key`='dbver';";
	mysqli_query( $GLOBALS['connection'], $sql );
}

// add database version config value
$sql   = "SELECT `val` FROM `config` WHERE `key`='dbver';";
$dbver = mysqli_query( $GLOBALS['connection'], $sql );
if ( mysqli_num_rows( $dbver ) == 0 ) {
	$sql    = "INSERT INTO config(`key`, `val`) VALUES('dbver', 1);";
	$result = mysqli_query( $GLOBALS['connection'], $sql );
	$dbver  = 1;
}
$dbver = intval( $dbver );

if ( $dbver == 1 ) {

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
}
