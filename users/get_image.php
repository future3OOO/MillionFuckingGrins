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

define( 'NO_HOUSE_KEEP', 'YES' );

$BID = $f2->bid();

require_once __DIR__ . "/../include/init.php";

$sql = "SELECT * FROM blocks where block_id='" . intval( $_REQUEST['block_id'] ) . "' banner_id='$BID' ";
$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) );
$row = mysqli_fetch_array( $result, MYSQLI_ASSOC );

if ( $row['image_data'] == '' ) {

	if ( $row['status'] == "sold" ) {
		$file_name = BASE_PATH . '/images/ordered_block.png';

		// hard coded above file to save a file read...

		$data = "iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAABGdBTUEAALGPC/xhBQAAABZJREFUKFNj/N/gwIAHAKXxIIYRKg0AB3qe55E8bNQAAAAASUVORK5CYII=";
	} else {
		$file_name = BASE_PATH . '/images/block.png';

		// hard coded above file to save a file read...

		$data = "iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAABGdBTUEAALGPC/xhBQAAABdJREFUKFNjvHLlCgMeAJT+jxswjFBpAOAoCvbvqFc9AAAAAElFTkSuQmCC";
	}
	header( "Content-type: image/x-png" );
	echo base64_decode( $data );
} else {
	header( "Content-type: " . $row['mime_type'] );
	echo base64_decode( $row['image_data'] );
}
