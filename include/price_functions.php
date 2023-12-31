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

$price_table = array();

function load_price_zones( $banner_id ) {

	global $price_table;

	if ( ! isset( $price_table ) || ! is_array( $price_table ) ) {
		$price_table = array();
	}

	// check if price table is loaded already
	if ( isset( $price_table[0] ) ) {
		if ( $price_table[0] == 1 ) {
			return;
		}
	}

	$banner_data = load_banner_constants( $banner_id );

	$sql = "SELECT * FROM prices where banner_id='" . intval( $banner_id ) . "' ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	$key = 1;
	while ( $row = mysqli_fetch_array( $result ) ) {
		$price_table[ $key ]['row_from'] = $row['row_from'] * $banner_data['BLK_WIDTH'];
		$price_table[ $key ]['row_to']   = $row['row_to'] * $banner_data['BLK_WIDTH'];
		$price_table[ $key ]['col_from'] = $row['col_from'] * $banner_data['BLK_HEIGHT'];
		$price_table[ $key ]['col_to']   = $row['col_to'] * $banner_data['BLK_HEIGHT'];
		$price_table[ $key ]['color']    = $row['color'];
		$price_table[ $key ]['price']    = $row['price'];
		$price_table[ $key ]['currency'] = $row['currency'];
		$key ++;
	}

	// loaded is stored in key 0
	$price_table[0] = 1;
}

function get_zone_color( $banner_id, $row, $col ) {
	global $price_table;

	if ( ! isset( $price_table[0] ) ) {
		load_price_zones( $banner_id );
	}

	$banner_data = load_banner_constants( $banner_id );

	$row += $banner_data['BLK_HEIGHT'];
	$col += $banner_data['BLK_WIDTH'];

	foreach ( $price_table as $key => $val ) {
		// Don't try to process the "loaded" key which is stored at the 0 index of the price table array.
		if ( $key == 0 ) {
			continue;
		}

		if ( ( ( $val['row_from'] <= $row ) && ( $val['row_to'] >= $row ) ) && ( ( $val['col_from'] <= $col ) && ( $val['col_to'] >= $col ) ) ) {

			return $val['color'];
		}
	}

	return "";
}

function get_block_price( $banner_id, $block_id ) {
	// Returns as default currency.

	// get co-ords of the block

	$sql = "select x, y from blocks where block_id='" . intval( $block_id ) . "' and banner_id='" . intval( $banner_id ) . "' ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
	$block_row = mysqli_fetch_array( $result );

	$row = $block_row['x'];
	$col = $block_row['y'];

	$banner_data = load_banner_constants( $banner_id );

	return get_zone_price( $banner_id, $row / $banner_data['BLK_HEIGHT'], $col / $banner_data['BLK_WIDTH'] );
}

function get_zone_price( $banner_id, $row, $col ) {
	global $price_table;

	$banner_data = load_banner_constants( $banner_id );

	$row += $banner_data['BLK_HEIGHT'];
	$col += $banner_data['BLK_WIDTH'];

	if ( isset( $price_table[0] ) ) {
		if ( $price_table[0] != 1 ) {
			load_price_zones( $banner_id );
		}
	} else {
		load_price_zones( $banner_id );
	}

	if ( is_array( $price_table ) && count( $price_table ) > 1 ) {
		foreach ( $price_table as $key => $val ) {
			if ( $key == 0 ) {
				continue;
			}
			if ( ( ( $val['row_from'] <= $row ) && ( $val['row_to'] >= $row ) ) && ( ( $val['col_from'] <= $col ) && ( $val['col_to'] >= $col ) ) ) {
				return convert_to_default_currency( $val['currency'], $val['price'] );
			}
		}
	}

	// If not found then get the default price per block for the grid
	$sql = "select price_per_block as price, currency from banners where  banner_id='" . intval( $banner_id ) . "' ";
	$result2 = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
	$block_row = mysqli_fetch_array( $result2 );

	return convert_to_default_currency( $block_row['currency'], $block_row['price'] );
}

function show_price_area( $banner_id ) {

	$sql = "SELECT * FROM prices where banner_id='" . intval( $banner_id ) . "'";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

	?>

    <map name="prices" id="prices">

		<?php

		while ( $row = mysqli_fetch_array( $result ) ) {
			$row['row_from'] = $row['row_from'] - 1;
			$row['row_to']   = $row['row_to'] - 1;

			$row['col_from'] = $row['col_from'] - 1;
			$row['col_to']   = $row['col_to'] - 1;

			// the x and y coordinates of the upper left and lower right corner
			?>

            <area shape="RECT" coords="<?php echo $row['col_from'] * 10; ?>,<?php echo $row['row_from'] * 10; ?>,<?php echo ( $row['col_to'] * 10 ) + 10; ?>,<?php echo ( $row['row_to'] * 10 ) + 10; ?>" href="" title="<?php echo htmlspecialchars( $row['price'] ); ?>" alt="<?php echo htmlspecialchars( $row['price'] ); ?>" onclick="return false; " target="_blank"/>

			<?php
		}

		?>

    </map>
	<?php
}

function display_price_table( $banner_id ) {

	global $label;

	if ( banner_get_packages( $banner_id ) ) {
		// cannot have custom price zones, this banner has packages.
		return;
	}

	$sql = "SELECT * FROM prices where banner_id='" . intval( $banner_id ) . "' order by row_from";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

	if ( mysqli_num_rows( $result ) > 0 ) {
		?>
        <div class='fancy_heading' width="85%"><?php echo $label['advertiser_pf_table']; ?></div>
        <p>
			<?php echo $label['advertiser_pf_intro']; ?>&nbsp;
        </p>
        <table border="0" cellSpacing="1" cellPadding="3" bgColor="#d9d9d9" width="50%">
            <tr>
                <td><b><font face="Arial" size="2"><?php echo $label['advertiser_pf_price']; ?></font></b></td>
                <td><b><font face="Arial" size="2"><?php echo $label['advertiser_pf_color']; ?></font></b></td>
                <td><b><font face="Arial" size="2"><?php echo $label['advertiser_pf_fromrow']; ?></font></b></td>
                <td><b><font face="Arial" size="2"><?php echo $label['advertiser_pf_torow']; ?></font></b></td>
                <td><b><font face="Arial" size="2"><?php echo $label['advertiser_pf_fromcol']; ?></font></b></td>
                <td><b><font face="Arial" size="2"><?php echo $label['advertiser_pf_tocol']; ?></font></b></td>

            </tr>

			<?php
			while ( $row = mysqli_fetch_array( $result ) ) {
				?>
                <tr bgcolor="#ffffff">
                    <td><font face="Arial" size="2"><?php if ( $row['price'] == 0 ) {
								echo $label['free'];
							} else {
								echo convert_to_default_currency_formatted( $row['currency'], $row['price'], true );
							} ?></font></td>
                    <td bgcolor="<?php if ( $row['color'] == 'yellow' ) {
						echo '#FFFF00';
					} else if ( $row['color'] == 'cyan' ) {
						echo '#00FFFF';
					} else if ( $row['color'] == 'magenta' ) {
						echo '#FF00FF';
					} ?>"><font face="Arial" size="2"><?php

							echo $row['color'];

							?>

                        </font></td>
                    <td><font face="Arial" size="2"><?php echo $row['row_from']; ?></font></td>
                    <td><font face="Arial" size="2"><?php echo $row['row_to']; ?></font></td>
                    <td><font face="Arial" size="2"><?php echo $row['col_from']; ?></font></td>
                    <td><font face="Arial" size="2"><?php echo $row['col_to']; ?></font></td>

                </tr>
				<?php
			}

			?>

        </table>

		<?php
	}
}

// return's the order's price in default currency
function calculate_price( $banner_id, $blocks_str ) {

	if ( $blocks_str == '' ) {
		return 0;
	}

	$blocks = explode( ",", $blocks_str );
	$price  = 0;
	foreach ( $blocks as $block_id ) {
		$price += get_block_price( $banner_id, $block_id );
	}

	return $price;
}

?>