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

if ( ! constant('MAIN_PHP') ) {
	exit;
}

$sql = "SELECT * FROM `form_fields` where form_id=1 AND field_type != 'BLANK' AND field_type !='SEPERATOR' AND field_type !='NOTE' ";
$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
while ( $row = mysqli_fetch_array( $result, MYSQLI_ASSOC ) ) {
	$fields[ $row['field_id'] ]['field_id']   = $row['field_id'];
	$fields[ $row['field_id'] ]['field_type'] = $row['field_type'];
}
// Essential fields, always exists.

$fields['ad_id']['field_id']     = 'ad_id';
$fields['order_id']['field_id']  = 'order_id';
$fields['banner_id']['field_id'] = 'banner_id';
$fields['user_id']['field_id']   = 'user_id';
$fields['ad_date']['field_id']   = 'ad_date';

$columns = [];

$sql = "SHOW COLUMNS FROM ads";
$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
while ( $row = mysqli_fetch_row( $result ) ) {
	$columns[ $row[0] ] = $row[0];
}

/*
 * Rules:
 * If exists in both, do nothing
 * If exists in form but not table, add to table
 * if NOT exists form, but is in table, remove from table
 */

$i       = 0;
$change  = '';
foreach ( $fields as $key => $val ) {

	if ( $change == '' ) {
		$sql = "ALTER TABLE `ads` ";
	}

	# If exists in form but not table, add to table
	if ( ( $columns[ $key ] == '' ) && ( $fields[ $key ]['field_id'] != '' ) ) {
		// ADD to table
		if ( $i > 0 ) {
			$sql .= ", ";
		}
		$sql    .= add_field( $key, $fields[ $key ]['field_type'] );
		$change = 'Y';

		$i ++;
	}
}

$i = 0;
foreach ( $columns as $key => $val ) {

	if ( $change == '' ) {
		$sql = "ALTER TABLE `ads` ";
	}

	# if NOT exists form, but is in table, 
	if ( ( $columns[ $key ] != '' ) && ( $fields[ $key ]['field_id'] == '' ) ) {
		// REMOVE from table
		if ( $i > 0 ) {
			$sql .= ", ";
		}
		$sql    .= remove_field( $key );
		$change = 'Y';

		$i ++;
	}
}

if ( $change == 'Y' ) {

	echo "<br />";
	mysqli_query( $GLOBALS['connection'], $sql ) or die ( "SQL: " . $sql . "  ERROR: " . mysqli_error( $GLOBALS['connection'] ) );

	echo "Database Structure Updated.";
}

function add_field( $field_id, $field_type ) {

	return " ADD `$field_id` " . get_definition( $field_type ) . " ";
}

function remove_field( $field_id ) {
	return " DROP  `$field_id` ";
}
