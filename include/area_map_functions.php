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

// Contributed by Martin 
// AREA render  function
// Million Penny Home Page
function render_map_area( $fh, $data, $b_row ) {
	require_once( BASE_PATH . "/include/ads.inc.php" );

	$BID = $b_row['banner_id'];

	if ( isset( $data['x2'] ) ) {
		$x2 = $data['x2'];
		$y2 = $data['y2'];
	} else {
		$x2 = $data['x1'];
		$y2 = $data['y1'];
	}

	if ( ENABLE_CLOAKING == 'YES' ) {
		$url = $data['url'];
	} else {
		$url = 'click.php?block_id=' . $data['block_id'] . '&BID=' . $BID;
	}

	$ALT_TEXT = "";
	if ( ENABLE_MOUSEOVER == 'YES' || ENABLE_MOUSEOVER == 'POPUP' ) {
		if ( $data['ad_id'] > 0 ) {
			$ALT_TEXT = $data['alt_text'] . '<img src="' . BASE_HTTP_PATH . 'images/periods.gif" border="0">';
			$ALT_TEXT = str_replace( "'", "", $ALT_TEXT );
			$ALT_TEXT = ( str_replace( "\"", '', $ALT_TEXT ) );
		}
	}

	$data_values = array(
		'id'        => $data['ad_id'],
		'block_id'  => $data['block_id'],
		'banner_id' => $BID,
		'alt_text'  => $ALT_TEXT,
		'url'       => $url,
	);

	fwrite( $fh, '<area href="" data-data="' . htmlspecialchars( json_encode( $data_values, JSON_HEX_QUOT | JSON_HEX_APOS ), ENT_QUOTES, 'UTF-8' ) . '" coords="' . $data['x1'] . ',' . $data['y1'] . ',' . ( $x2 + $b_row['block_width'] ) . ',' . ( $y2 + $b_row['block_height'] ) . '"' );
	if ( ENABLE_MOUSEOVER == 'NO' ) {
		fwrite( $fh, " title=\"" . htmlspecialchars( $data['alt_text'], ENT_QUOTES, 'UTF-8' ) . "\" alt=\"" . htmlspecialchars( $data['alt_text'], ENT_QUOTES, 'UTF-8' ) . "\"" );
	}
	fwrite( $fh, ">\n" );
}

/*

This function generates the <AREA> tags
The output is saved into a file.

*/

function process_map( $BID, $map_file = '' ) {

	if ( ! is_numeric( $BID ) ) {
		die();
	}

	$sql  = "UPDATE orders SET published='N' where `status`='expired' ";
	$stmt = mysqli_stmt_init( $GLOBALS['connection'] );
	if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
		die ( mds_sql_error( $sql ) );
	}
	mysqli_stmt_execute( $stmt );
	$error = mysqli_stmt_error( $stmt );
	if ( ! empty( $error ) ) {
		die ( mds_sql_error( $sql ) );
	}
	mysqli_stmt_close( $stmt );

	$sql  = "SELECT * FROM `banners` WHERE `banner_id`=? ";
	$stmt = mysqli_stmt_init( $GLOBALS['connection'] );
	if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
		die ( mds_sql_error( $sql ) );
	}
	mysqli_stmt_bind_param( $stmt, "i", $var1 );
	$var1 = intval( $BID );
	mysqli_stmt_execute( $stmt ) or die( mds_sql_error( $sql ) );

	// TODO: FINISH ADDING MYSQL BACKWARDS COMPATIBILITY
	if ( function_exists( 'mysqli_stmt_get_result' ) ) {
		$result = mysqli_stmt_get_result( $stmt );
	} else {
		$params = [];
		$row    = [];
		$c      = [];
		$meta   = $stmt->result_metadata();
		while ( $field = $meta->fetch_field() ) {
			$params[] = &$row[ $field->name ];
		}

		call_user_func_array( array( $stmt, 'bind_result' ), $params );

		while ( $stmt->fetch() ) {
			foreach ( $row as $key => $val ) {
				$c[ $key ] = $val;
			}
			$result[] = $c;
		}
	}
	$error = mysqli_stmt_error( $stmt );
	if ( ! empty( $error ) ) {
		die ( mds_sql_error( $sql ) );
	}
	mysqli_stmt_close( $stmt );

	$b_row = mysqli_fetch_array( $result );

	if ( ! $b_row['block_width'] ) {
		$b_row['block_width'] = 10;
	}
	if ( ! $b_row['block_height'] ) {
		$b_row['block_height'] = 10;
	}

	if ( ! $map_file ) {
		$map_file = get_map_file_name( $BID );
	}

	// open file
	$fh = fopen( "$map_file", "w" );

	fwrite( $fh, '<map name="main" id="main">' );

	// render client-side click areas
	$sql = "SELECT DISTINCT block_id,
                order_id,
                MIN(x)   AS x1,
                MAX(x)   AS x2,
                MIN(y)   AS y1,
                MAX(y)   AS y2,
                url,
                alt_text,
                ad_id,
                COUNT(*) AS Total
FROM blocks
WHERE published = 'Y'
  AND `status` = 'sold'
  AND banner_id = ?
  AND image_data != ''
GROUP BY order_id, block_id, x, y, url, alt_text, ad_id";

	$stmt = mysqli_stmt_init( $GLOBALS['connection'] );
	if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
		die ( mds_sql_error( $sql ) );
	}

	mysqli_stmt_bind_param( $stmt, "i", $var1 );
	$var1 = intval( $BID );
	mysqli_stmt_execute( $stmt );
	if ( function_exists( 'mysqli_stmt_get_result' ) ) {
		$result = mysqli_stmt_get_result( $stmt );
	} else {
		$params = [];
		$row    = [];
		$c      = [];
		$meta   = $stmt->result_metadata();
		while ( $field = $meta->fetch_field() ) {
			$params[] = &$row[ $field->name ];
		}

		call_user_func_array( array( $stmt, 'bind_result' ), $params );

		while ( $stmt->fetch() ) {
			foreach ( $row as $key => $val ) {
				$c[ $key ] = $val;
			}
			$result[] = $c;
		}
	}
	$error  = mysqli_stmt_error( $stmt );
	if ( ! empty( $error ) ) {
		die ( mds_sql_error( $sql ) );
	}
	mysqli_stmt_close( $stmt );

	while ( $row = mysqli_fetch_array( $result ) ) {

		$found = false;

		// Determine height and width of an optimized rect
		$x_span = $row['x2'] - $row['x1'] + $b_row['block_width'];
		$y_span = $row['y2'] - $row['y1'] + $b_row['block_height'];

		// Determine if reserved space is not equal to a single-ad user's optimized RECT
		if ( ( ( $x_span * $y_span ) / ( $b_row['block_width'] * $b_row['block_height'] ) ) != $row['Total'] ) {

			// Render POLY or RECT (given reasonable possibilities)
			$sql_i = "SELECT DISTINCT url,
                image_data,
                block_id,
                alt_text,
                MIN(x)   AS x1,
                MAX(x)   AS x2,
                y        AS y1,
                y        AS y2,
                ad_id,
                COUNT(*) AS Total
FROM blocks
WHERE (published = 'Y')
  AND (status = 'sold')
  AND (banner_id = ?)
  AND (image_data > '')
  AND (image_data = image_data)
  AND (order_id = ?)
GROUP BY y, x, block_id, ad_id, alt_text, image_data";

			$stmt = mysqli_stmt_init( $GLOBALS['connection'] );
			if ( ! mysqli_stmt_prepare( $stmt, $sql_i ) ) {
				die ( mds_sql_error( $sql_i ) );
			}

			mysqli_stmt_bind_param( $stmt, "ii", $var1, $var2 );
			$var1 = intval( $BID );
			$var2 = intval( $row['order_id'] );
			mysqli_stmt_execute( $stmt );
			if ( function_exists( 'mysqli_stmt_get_result' ) ) {
				$res_i = mysqli_stmt_get_result( $stmt );
			} else {
				$params = [];
				$row    = [];
				$c      = [];
				$meta   = $stmt->result_metadata();
				while ( $field = $meta->fetch_field() ) {
					$params[] = &$row[ $field->name ];
				}

				call_user_func_array( array( $stmt, 'bind_result' ), $params );

				while ( $stmt->fetch() ) {
					foreach ( $row as $key => $val ) {
						$c[ $key ] = $val;
					}
					$res_i[] = $c;
				}
			}
			$error = mysqli_stmt_error( $stmt );
			if ( ! empty( $error ) ) {
				die ( mds_sql_error( $sql_i ) );
			}
			mysqli_stmt_close( $stmt );

			while ( $row_i = mysqli_fetch_array( $res_i ) ) {

				// If the min/max measure does not equal number of boxes, then we have to render this row's boxes individually
				//$box_count = ( ( ( $row_i['x2'] + 10 ) - $row_i['x1'] ) / 10 );
				$box_count = ( ( ( $row_i['x2'] + $b_row['block_width'] ) - $row_i['x1'] ) / $b_row['block_width'] );
				if ( $box_count != $row_i['Total'] ) {
					// must render individually as RECT
					$sql_r = "SELECT ad_id,
       url,
       image_data,
       block_id,
       alt_text,
       x AS x1,
       x AS x2,
       y AS y1,
       y AS y2
FROM blocks
WHERE (published = 'Y')
  AND (status = 'sold')
  AND (banner_id = ?)
  AND (image_data > '')
  AND (image_data = image_data)
  AND (order_id = ?)
  AND (y = ?)";

					$stmt = mysqli_stmt_init( $GLOBALS['connection'] );
					if ( ! mysqli_stmt_prepare( $stmt, $sql_r ) ) {
						die ( mds_sql_error( $sql_r ) );
					}

					mysqli_stmt_bind_param( $stmt, "iii", $var1, $var2, $var3 );
					$var1 = intval( $BID );
					$var2 = intval( $row['order_id'] );
					$var3 = intval( $row_i['y1'] );
					mysqli_stmt_execute( $stmt );
					if ( function_exists( 'mysqli_stmt_get_result' ) ) {
						$res_r = mysqli_stmt_get_result( $stmt );
					} else {
						$params = [];
						$row    = [];
						$c      = [];
						$meta   = $stmt->result_metadata();
						while ( $field = $meta->fetch_field() ) {
							$params[] = &$row[ $field->name ];
						}

						call_user_func_array( array( $stmt, 'bind_result' ), $params );

						while ( $stmt->fetch() ) {
							foreach ( $row as $key => $val ) {
								$c[ $key ] = $val;
							}
							$res_r[] = $c;
						}
					}
					$error = mysqli_stmt_error( $stmt );
					if ( ! empty( $error ) ) {
						die ( mds_sql_error( $sql_r ) );
					}
					mysqli_stmt_close( $stmt );

					while ( $row_r = mysqli_fetch_array( $res_r ) ) {
						// render single block RECT
						render_map_area( $fh, $row_r, $b_row );
						$found = true;
					}
				} else {
					// render multi-block RECT
					render_map_area( $fh, $row_i, $b_row );
					$found = true;
				}
			}
		} else {
			// Render full ad RECT
			render_map_area( $fh, $row, $b_row );
			$found = true;
		}

		// render empty block
		if ( $found == false ) {
			render_map_area( $fh, $row, $b_row );
		}
	}

	fwrite( $fh, "</map>" );
	fclose( $fh );
}

/*

This function outputs the HTML for the display_map.php file.
The structure of output:

<head>
<script>
<!-- Javascript in here ->
</script>
</head>
<body> <!--- render the grid's background image ->

<MAP> <!--- generated by process_map() ->

<AREA></AREA> <!--- generated by process_map() ->
<AREA></AREA> <!--- generated by process_map() ->
<AREA></AREA> <!--- generated by process_map() ->
...

</MAP> <!--- generated by process_map() ->

<img>

</body>

*/

function show_map( $BID = 1 ) {

	if ( ! is_numeric( $BID ) ) {
		die();
	}

	$sql = "SELECT grid_width, grid_height, block_width, block_height, bgcolor, time_stamp
FROM banners
WHERE (banner_id = ?)";

	$stmt = mysqli_stmt_init( $GLOBALS['connection'] );
	if ( ! mysqli_stmt_prepare( $stmt, $sql ) ) {
		die ( mds_sql_error( $sql ) );
	}
	mysqli_stmt_bind_param( $stmt, "i", $var1 );
	$var1 = intval( $BID );
	mysqli_stmt_execute( $stmt );
	if ( function_exists( 'mysqli_stmt_get_result' ) ) {
		$result = mysqli_stmt_get_result( $stmt );
	} else {
		$params = [];
		$row    = [];
		$c      = [];
		$meta   = $stmt->result_metadata();
		while ( $field = $meta->fetch_field() ) {
			$params[] = &$row[ $field->name ];
		}

		call_user_func_array( array( $stmt, 'bind_result' ), $params );

		while ( $stmt->fetch() ) {
			foreach ( $row as $key => $val ) {
				$c[ $key ] = $val;
			}
			$result[] = $c;
		}
	}
	$error  = mysqli_stmt_error( $stmt );
	if ( ! empty( $error ) ) {
		die ( mds_sql_error( $sql ) );
	}
	mysqli_stmt_close( $stmt );

	$b_row = mysqli_fetch_array( $result );

	if ( ! $b_row['block_width'] ) {
		$b_row['block_width'] = 10;
	}
	if ( ! $b_row['block_height'] ) {
		$b_row['block_height'] = 10;
	}

	// include the header
	require_once( BASE_PATH . "/html/header.php" );

	// Displays the grid image map. Use Process Pixels in the admin to update the image map.
	require_once( BASE_PATH . "/include/mds_ajax.php" );
	$mds_ajax = new Mds_Ajax();
	$mds_ajax->show( 'grid', $BID, 'grid' );

	// include footer
	require_once( BASE_PATH . "/html/footer.php" );
}

function get_map_file_name( $BID ) {
	if ( ! is_numeric( $BID ) ) {
		return false;
	}

	return get_banner_dir() . "map_$BID.inc";
}
