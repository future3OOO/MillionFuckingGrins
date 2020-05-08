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

session_start();
define( 'NO_HOUSE_KEEP', 'YES' );

require_once __DIR__ . "/../include/init.php";

//include( "login_functions.php" );

//process_login();

if ( isset( $_REQUEST['BID'] ) && $f2->bid( $_REQUEST['BID'] ) != '' ) {
	$BID = $f2->bid( $_REQUEST['BID'] );
} else {
	$BID = 1;
}

$banner_data = load_banner_constants( $BID );

$imagine = new Imagine\Gd\Imagine();

// get the order id
if ( isset( $_REQUEST['block_id'] ) && $_REQUEST['block_id'] != '' ) {
	$sql = "SELECT order_id FROM blocks WHERE block_id='" . intval( $_REQUEST['block_id'] ) . "' AND banner_id='" . $BID . "' ";
} else if ( isset( $_REQUEST['aid'] ) && $_REQUEST['aid'] != '' ) {
	$sql = "SELECT order_id FROM ads WHERE ad_id='" . intval( $_REQUEST['aid'] ) . "' ";
}
$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) );
$row = mysqli_fetch_array( $result );

$size = get_pixel_image_size( $row['order_id'] );

// load all the blocks wot
$sql = "SELECT block_id,x,y,image_data FROM blocks WHERE order_id='" . intval( $row['order_id'] ) . "' ";
$result3 = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) );

$blocks = array();

$i = 0;
while ( $block_row = mysqli_fetch_array( $result3 ) ) {

	$high_x = ! isset( $high_x ) ? $block_row['x'] : $high_x;
	$high_y = ! isset( $high_y ) ? $block_row['y'] : $high_y;
	$low_x  = ! isset( $low_x ) ? $block_row['x'] : $low_x;
	$low_y  = ! isset( $low_y ) ? $block_row['y'] : $low_y;

	if ( $block_row['x'] > $high_x ) {
		$high_x = $block_row['x'];
	}

	if ( $block_row['y'] > $high_y ) {
		$high_y = $block_row['y'];
	}

	if ( $block_row['y'] < $low_y ) {
		$low_y = $block_row['y'];
	}

	if ( $block_row['x'] < $low_x ) {
		$low_x = $block_row['x'];
	}

	$blocks[ $i ]['block_id'] = $block_row['block_id'];
	if ( $block_row['image_data'] == '' ) {
		$blocks[ $i ]['image_data'] = $imagine->load( $banner_data['GRID_BLOCK'] );
	} else {
		$blocks[ $i ]['image_data'] = $imagine->load( base64_decode( $block_row['image_data'] ) );
	}

	$blocks[ $i ]['x'] = $block_row['x'];
	$blocks[ $i ]['y'] = $block_row['y'];

	$i ++;
}

$high_x = ! isset( $high_x ) ? 0 : $high_x;
$high_y = ! isset( $high_y ) ? 0 : $high_y;
$low_x  = ! isset( $low_x ) ? 0 : $low_x;
$low_y  = ! isset( $low_y ) ? 0 : $low_y;

$x_size = ( $high_x + $banner_data['BLK_WIDTH'] ) - $low_x;
$y_size = ( $high_y + $banner_data['BLK_HEIGHT'] ) - $low_y;

$new_blocks = array();
foreach ( $blocks as $block ) {
	$id                = ( $block['x'] - $low_x ) . ( $block['y'] - $low_y );
	$new_blocks[ $id ] = $block;
}

$std_image = $imagine->load( $banner_data['GRID_BLOCK'] );

// grid size
$size = new Imagine\Image\Box( $x_size, $y_size );

// create empty image
$palette = new Imagine\Image\Palette\RGB();
$color   = $palette->color( '#000', 0 );
$image   = $imagine->create( $size, $color );

$block_count = 0;

for ( $y = 0; $y < $y_size; $y += $banner_data['BLK_HEIGHT'] ) {
	for ( $x = 0; $x < $x_size; $x += $banner_data['BLK_WIDTH'] ) {
		if ( isset( $new_blocks[ $x . $y ] ) && $new_blocks[ $x . $y ]['image_data'] != '' ) {
			$image->paste( $new_blocks[ $x . $y ]['image_data'], new Imagine\Image\Point( $x, $y ) );
		}
	}
}

$image->show( "png", array( 'png_compression_level' => 9 ) );
