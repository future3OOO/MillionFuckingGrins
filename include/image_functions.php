<?php
/*
 * @package       mds
 * @copyright     (C) Copyright 2022 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2022-02-28 15:54:43 EST
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

function publish_image( $BID ) {

	if ( ! is_numeric( $BID ) ) {
		return;
	}

	$imagine = "";
	if ( class_exists( 'Imagick' ) ) {
		$imagine = new Imagine\Imagick\Imagine();
	} else if ( function_exists( 'gd_info' ) ) {
		$imagine = new Imagine\Gd\Imagine();
	}

	$file_path = SERVER_PATH_TO_ADMIN;
	$dest_path = get_banner_dir() . '/';

	$dest   = "";
	$source = "";
	if ( OUTPUT_JPEG == 'Y' ) {
		$source = $file_path . "temp/temp$BID.jpg";
		$dest   = $dest_path . "main$BID.jpg";
	} else if ( OUTPUT_JPEG == 'N' ) {
		$source = $file_path . "temp/temp$BID.png";
		$dest   = $dest_path . "main$BID.png";
	} else if ( ( OUTPUT_JPEG == 'GIF' ) ) {
		$source = $file_path . "temp/temp$BID.gif";
		$dest   = $dest_path . "main$BID.gif";
	}

	if ( copy( $source, $dest ) ) {
		//echo "Copied " . htmlentities( $source ) . " to " . htmlentities( $dest );
	} else {
		//echo "Failed to copy " . htmlentities( $source ) . " to " . htmlentities( $dest );
	}

	// output the tile image
	if ( DISPLAY_PIXEL_BACKGROUND == "YES" ) {
		$b_row = load_banner_row( $BID );

		if ( $b_row['tile'] == '' ) {
			$b_row['tile'] = get_default_image( 'tile' );
		}
		$tile = $imagine->load( base64_decode( $b_row['tile'] ) );
		$tile->save( $dest_path . "bg-main$BID.gif" );

		//echo "<br />Saved background image to " . $dest_path . "bg-main$BID.gif";
	}

	// update the records
	$sql = "SELECT * FROM blocks WHERE approved='Y' AND status='sold' AND image_data <> '' AND banner_id='" . intval( $BID ) . "' ";
	$r = mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );

	while ( $row = mysqli_fetch_array( $r ) ) {
		// set the 'date_published' only if it was not set before, date_published can only be set once.
		$now = ( gmdate( "Y-m-d H:i:s" ) );
		$sql = "UPDATE orders set `date_published`='$now' where order_id='" . intval( $row['order_id'] ) . "' AND date_published IS NULL ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );

		// update the published status, always updated to Y
		$sql = "UPDATE orders SET `published`='Y' WHERE order_id='" . intval( $row['order_id'] ) . "'  ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );

		$sql = "UPDATE blocks set `published`='Y' where block_id='" . intval( $row['block_id'] ) . "' AND banner_id='" . intval( $BID ) . "'";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
	}

	//Make sure to un-publish any blocks that are not approved...
	$sql = "SELECT block_id, order_id FROM blocks WHERE approved='N' AND status='sold' AND banner_id='" . intval( $BID ) . "' ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
	while ( $row = mysqli_fetch_array( $result ) ) {
		$sql = "UPDATE blocks set `published`='N' where block_id='" . intval( $row['block_id'] ) . "'  AND banner_id='" . intval( $BID ) . "'  ";
		mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );

		$sql = "UPDATE orders set `published`='N' where order_id='" . intval( $row['order_id'] ) . "'  AND banner_id='" . intval( $BID ) . "'  ";
		mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
	}

	// update the time-stamp on the banner
	$sql = "UPDATE banners SET time_stamp='" . time() . "' WHERE banner_id='" . intval( $BID ) . "' ";
	mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
}

function process_image( $BID ) {

	require_once( "output_grid.php" );

	return output_grid( false, SERVER_PATH_TO_ADMIN . "temp/temp$BID", $BID, array(
		'background',
		'orders',
		'nfs_front',
		'grid',
	) );
}

function get_html_code( $BID ) {
	$BID = intval( $BID );

	$sql = "SELECT * FROM banners WHERE banner_id='" . $BID . "'";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	$b_row = mysqli_fetch_array( $result );

	if ( ! $b_row['block_width'] ) {
		$b_row['block_width'] = 10;
	}
	if ( ! $b_row['block_height'] ) {
		$b_row['block_height'] = 10;
	}

	$width  = $b_row['grid_width'] * $b_row['block_width'];
	$height = $b_row['grid_height'] * $b_row['block_height'];

	return '<iframe class="gridframe' . $BID . '" src="' . BASE_HTTP_PATH . 'display_map.php?BID=' . $BID . '&iframe_call=true" style="width:' . $width . 'px;height:' . $height . 'px;" width="' . $width . '" height="' . $height . '"></iframe>';
}

function get_stats_html_code( $BID ) {
	$BID = intval( $BID );

	return '<iframe class="statsframe' . $BID . '" src="' . BASE_HTTP_PATH . 'display_stats.php?BID=' . $BID . '&iframe_call=true" width="150" height="50"></iframe>';
}

/**
 * Calculates restricted dimensions with a maximum of $goal_width by $goal_height
 *
 * @link https://stackoverflow.com/questions/6606445/calculating-width-and-height-to-resize-image/7877615#7877615
 *
 * @param $goal_width
 * @param $goal_height
 * @param $width
 * @param $height
 *
 * @return array
 */
function resize_dimensions( $goal_width, $goal_height, $width, $height ) {
	$return = array( 'width' => $width, 'height' => $height );

	// If the ratio > goal ratio and the width > goal width resize down to goal width
	if ( $width / $height > $goal_width / $goal_height && $width > $goal_width ) {
		$return['width']  = $goal_width;
		$return['height'] = $goal_width / $width * $height;
	} // Otherwise, if the height > goal, resize down to goal height
	else if ( $height > $goal_height ) {
		$return['width']  = $goal_height / $height * $width;
		$return['height'] = $goal_height;
	}

	return $return;
}
