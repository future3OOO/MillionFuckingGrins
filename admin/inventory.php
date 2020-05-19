<?php
/**
 * @package       mds
 * @copyright     (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2020.05.13 12:41:15 EDT
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

require_once __DIR__ . "/../include/init.php";
require( 'admin_common.php' );

$BID = $f2->bid();

if ( isset( $_REQUEST['reset_image'] ) && $_REQUEST['reset_image'] != '' ) {

	$default = get_default_image( $_REQUEST['reset_image'] );

	$sql = "UPDATE banners SET `" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['reset_image'] ) . "`='" . mysqli_real_escape_string( $GLOBALS['connection'], $default ) . "' WHERE banner_id='" . $BID . "' ";

	mysqli_query( $GLOBALS['connection'], $sql );
}

function display_reset_link( $BID, $image_name ) {

	if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'edit' ) {
		?>
        <a onclick="return confirmLink(this, 'Reset this image to deafult, are you sure?');" href='inventory.php?action=edit&BID=<?php echo $BID; ?>&reset_image=<?php echo $image_name; ?>'><font color='red'>x</font></a>
		<?php
	}
}

function is_allowed_grid_file( $image_name ) {

	$ALLOWED_EXT = 'png';
	$parts       = explode( '.', $_FILES[ $image_name ]['name'] );
	$ext         = strtolower( array_pop( $parts ) );
	$ext_list    = preg_split( "/[\s,]+/i", ( $ALLOWED_EXT ) );
	if ( ! in_array( $ext, $ext_list ) ) {
		return false;
	} else {
		return true;
	}
}

function validate_input() {

	$error = "";

	if ( isset( $_REQUEST['name'] ) && $_REQUEST['name'] == '' ) {
		$error .= "- Grid name not filled in<br>";
	}

	if ( isset( $_REQUEST['grid_width'] ) && $_REQUEST['grid_width'] == '' ) {
		$error .= "- Grid Width not filled in<br>";
	}

	if ( isset( $_REQUEST['grid_height'] ) && $_REQUEST['grid_height'] == '' ) {
		$error .= "- Grid Height not filled in<br>";
	}

	if ( isset( $_REQUEST['days_expire'] ) && $_REQUEST['days_expire'] == '' ) {
		$error .= "- Days Expire not filled in<br>";
	}

	if ( isset( $_REQUEST['max_orders'] ) && $_REQUEST['max_orders'] == '' ) {
		$error .= "- Max orders per customer not filled in<br>";
	}

	if ( isset( $_REQUEST['price_per_block'] ) && $_REQUEST['price_per_block'] == '' ) {
		$error .= "- Price per Block  not filled in<br>";
	}

	if ( isset( $_REQUEST['currency'] ) && $_REQUEST['currency'] == '' ) {
		$error .= "- Currency not filled in<br>";
	}

	if ( isset( $_REQUEST['block_width'] ) && ! is_numeric( $_REQUEST['block_width'] ) ) {
		$error .= "- Block width is not valid<br>";
	}

	if ( isset( $_REQUEST['block_height'] ) && ! is_numeric( $_REQUEST['block_height'] ) ) {
		$error .= "- Block height is not valid<br>";
	}

	if ( isset( $_REQUEST['max_blocks'] ) && ! is_numeric( $_REQUEST['max_blocks'] ) ) {
		$error .= "- Max Blocks is not valid<br>";
	}

	if ( isset( $_REQUEST['min_blocks'] ) && ! is_numeric( $_REQUEST['min_blocks'] ) ) {
		$error .= "- Min Blocks is not valid<br>";
	}

	if ( isset( $_FILES['grid_block'] ) && $_FILES['grid_block']['tmp_name'] != '' ) {
		if ( ! is_allowed_grid_file( 'grid_block' ) ) {
			$error .= "- Grid Block must be a valid PNG file.<br>";
		}
	}

	if ( isset( $_FILES['nfs_block'] ) && $_FILES['nfs_block']['tmp_name'] != '' ) {
		if ( ! is_allowed_grid_file( 'nfs_block' ) ) {
			$error .= "- Not For Sale Block must be a valid PNG file.<br>";
		}
	}

	if ( isset( $_FILES['usr_grid_block'] ) && $_FILES['usr_grid_block']['tmp_name'] != '' ) {
		if ( ! is_allowed_grid_file( 'usr_grid_block' ) ) {
			$error .= "- Not For Sale Block must be a valid PNG file.<br>";
		}
	}

	if ( isset( $_FILES['usr_nfs_block'] ) && $_FILES['usr_nfs_block']['tmp_name'] != '' ) {
		if ( ! is_allowed_grid_file( 'usr_nfs_block' ) ) {
			$error .= "- User's Not For Sale Block must be a valid PNG file.<br>";
		}
	}

	if ( isset( $_FILES['usr_ord_block'] ) && $_FILES['usr_ord_block']['tmp_name'] != '' ) {
		if ( ! is_allowed_grid_file( 'usr_ord_block' ) ) {
			$error .= "- User's Ordered Block must be a valid PNG file.<br>";
		}
	}

	if ( isset( $_FILES['usr_res_block'] ) && $_FILES['usr_res_block']['tmp_name'] != '' ) {
		if ( ! is_allowed_grid_file( 'usr_res_block' ) ) {
			$error .= "- User's Reserved Block must be a valid PNG file.<br>";
		}
	}

	if ( isset( $_FILES['usr_sol_block'] ) && $_FILES['usr_sol_block']['tmp_name'] != '' ) {
		if ( ! is_allowed_grid_file( 'usr_sol_block' ) ) {
			$error .= "- User's Sold Block must be a valid PNG file.<br>";
		}
	}

	return $error;
}

function is_default() {
	if ( isset( $_REQUEST['BID'] ) && $_REQUEST['BID'] == 1 ) {
		return true;
	}

	return false;
}

if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'delete' ) {
	if ( is_default() ) {
		echo "<b>Cannot delete</b> - This is the default grid!<br>";
	} else {

		// check orders..

		$sql = "SELECT * FROM orders where status <> 'deleted' and banner_id=" . $BID;
		//echo $sql;
		$res = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
		if ( mysqli_num_rows( $res ) == 0 ) {

			$sql = "DELETE FROM blocks WHERE banner_id='" . $BID . "' ";
			mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

			$sql = "DELETE FROM prices WHERE banner_id='" . $BID . "' ";
			mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

			$sql = "DELETE FROM banners WHERE banner_id='" . $BID . "' ";
			mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

			// DELETE ADS
			$sql = "select * FROM ads where banner_id='" . $BID . "' ";
			$res2 = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
			while ( $row2 = mysqli_fetch_array( $res2 ) ) {

				delete_ads_files( $row2['ad_id'] );
				$sql = "DELETE from ads where ad_id='" . intval( $row2['ad_id'] ) . "' ";
				mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
			}

			@unlink( SERVER_PATH_TO_ADMIN . "../banners/main" . $BID . ".jpg" );
			@unlink( SERVER_PATH_TO_ADMIN . "../banners/main" . $BID . ".png" );
			@unlink( SERVER_PATH_TO_ADMIN . "temp/background" . $BID . ".png" );
		} else {
			echo "<font color='red'><b>Cannot delete</b></font> - this grid contains some orders in the database.<br>";
		}
	}
}

function get_banner_image_data( $b_row, $image_name ) {

	$uploaddir = SERVER_PATH_TO_ADMIN . "temp/";
//print_r($_FILES);
	if ( $_FILES[ $image_name ]['tmp_name'] ) {
		// a new image was uploaded
		$uploadfile = $uploaddir . md5( session_id() ) . $image_name . $_FILES[ $image_name ]['name'];
		move_uploaded_file( $_FILES[ $image_name ]['tmp_name'], $uploadfile );
		$fh       = fopen( $uploadfile, 'rb' );
		$contents = fread( $fh, filesize( $uploadfile ) );
		fclose( $fh );
		//imagecreatefrompng($uploadfile); 
		$contents = addslashes( base64_encode( $contents ) );
		//echo "$image_name<b>$contents</b><br>";
		unlink( $uploadfile );
	} else if ( $b_row[ $image_name ] != '' ) {
		// use the old image
		$contents = addslashes( ( $b_row[ $image_name ] ) );
//echo "using the old file<p>";
	} else {
//echo "using the default file $image_name<p>";
		$contents = addslashes( get_default_image( $image_name ) );
	}

//echo "$image_name<b>$contents</b><br>";
	return $contents;
}

function get_banner_image_sql_values( $BID ) {

	# , grid_block, nfs_block, tile, usr_grid_block, usr_nfs_block, usr_ord_block, usr_res_block, usr_sel_block, usr_sol_block 

	// get banner
	if ( $BID ) {
		$sql = "SELECT * FROM `banners` WHERE `banner_id`='" . intval( $BID ) . "' ";
		//echo "<p>$sql</p>";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) );
		$row = mysqli_fetch_array( $result );
		//print_r($row);
	}

	$sql_str = ", '" . get_banner_image_data( $row, 'grid_block' ) . "' , '" . get_banner_image_data( $row, 'nfs_block' ) . "', '" . get_banner_image_data( $row, 'tile' ) . "', '" . get_banner_image_data( $row, 'usr_grid_block' ) . "', '" . get_banner_image_data( $row, 'usr_nfs_block' ) . "', '" . get_banner_image_data( $row, 'usr_ord_block' ) . "', '" . get_banner_image_data( $row, 'usr_res_block' ) . "', '" . get_banner_image_data( $row, 'usr_sel_block' ) . "', '" . get_banner_image_data( $row, 'usr_sol_block' ) . "'";

	return $sql_str;
}

if ( isset( $_REQUEST['submit'] ) && $_REQUEST['submit'] != '' ) {

	$error = validate_input();

	if ( $error != '' ) {

		echo "<font color='red'>Error: cannot save due to the following errors:</font><br>";
		echo $error;
	} else {

		//	$sql = "REPLACE INTO currencies(code, name, rate, sign, decimal_places, decimal_point, thousands_sep) VALUES ('".$_REQUEST['code']."', '".$_REQUEST['name']."', '".$_REQUEST['rate']."',  '".$_REQUEST['sign']."', '".$_REQUEST['decimal_places']."', '".$_REQUEST['decimal_point']."', '".$_REQUEST['thousands_sep']."') ";

		//echo $sql;grid_block, nfs_block, tile, usr_grid_block, usr_nfs_block, usr_ord_block, usr_res_block, usr_sel_block, usr_sol_block 

		//$image_sql_fields = get_banner_image_sql_fields($BID);
		$image_sql_fields = ', grid_block, nfs_block, tile, usr_grid_block, usr_nfs_block, usr_ord_block, usr_res_block, usr_sel_block, usr_sol_block ';
		$image_sql_values = get_banner_image_sql_values( $BID );
		$now              = ( gmdate( "Y-m-d H:i:s" ) );

		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'new' ) {
			$sql = "INSERT INTO `banners` ( `banner_id` , `grid_width` , `grid_height` , `days_expire` , `price_per_block`, `name`, `currency`, `max_orders`, `block_width`, `block_height`, `max_blocks`, `min_blocks`, `date_updated`, `bgcolor`, `auto_publish`, `auto_approve` $image_sql_fields ) VALUES (NULL, '" . intval( $_REQUEST['grid_width'] ) . "', '" . intval( $_REQUEST['grid_height'] ) . "', '" . intval( $_REQUEST['days_expire'] ) . "', '" . floatval( $_REQUEST['price_per_block'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['name'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['currency'] ) . "', '" . intval( $_REQUEST['max_orders'] ) . "', '" . intval( $_REQUEST['block_width'] ) . "', '" . intval( $_REQUEST['block_height'] ) . "', '" . intval( $_REQUEST['max_blocks'] ) . "', '" . intval( $_REQUEST['min_blocks'] ) . "', '" . $now . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['bgcolor'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['auto_publish'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['auto_approve'] ) . "' $image_sql_values);";
		} else {
			$sql = "REPLACE INTO `banners` ( `banner_id` , `grid_width` , `grid_height` , `days_expire` , `price_per_block`, `name`, `currency`, `max_orders`, `block_width`, `block_height`, `max_blocks`, `min_blocks`, `date_updated`, `bgcolor`, `auto_publish`, `auto_approve` $image_sql_fields ) VALUES ('" . $BID . "', '" . intval( $_REQUEST['grid_width'] ) . "', '" . intval( $_REQUEST['grid_height'] ) . "', '" . intval( $_REQUEST['days_expire'] ) . "', '" . floatval( $_REQUEST['price_per_block'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['name'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['currency'] ) . "', '" . intval( $_REQUEST['max_orders'] ) . "', '" . intval( $_REQUEST['block_width'] ) . "', '" . intval( $_REQUEST['block_height'] ) . "', '" . intval( $_REQUEST['max_blocks'] ) . "', '" . intval( $_REQUEST['min_blocks'] ) . "', '" . $now . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['bgcolor'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['auto_publish'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['auto_approve'] ) . "' $image_sql_values);";
		}

		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

		$BID = mysqli_insert_id( $GLOBALS['connection'] );

		// TODO: Add individual order expiry dates
		$sql = "UPDATE `orders` SET days_expire=" . intval( $_REQUEST['days_expire'] ) . " WHERE banner_id=" . $BID;
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

		$_REQUEST['new'] = '';
		//	$_REQUEST['action'] = '';

	}
}

?>
<?php if ( ( isset( $_REQUEST['new'] ) && $_REQUEST['new'] == '' ) && ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == '' ) ) ) { ?>
    Here you can manage your grid(s):
    <ul>
        <li>Set the expiry of the pixels</li>
        <li>Set the maximum allowed orders per grid</li>
        <li>Set the default price of the pixels</li>
        <li>Set the grid width</li>
        <li>Create and delete new Grids</li>
    </ul>

<?php } ?>
<?php //if ((isset($_REQUEST['new']) && $_REQUEST['new']=='')) { ?>
<input type="button" style="background-color:#66FF33" value="New Grid..." onclick="mds_load_page('inventory.php?action=new&new=1', true)"><br>
<?php //} ?>

<?php

if ( isset( $_REQUEST['new'] ) && $_REQUEST['new'] == '1' ) {
	echo "<h4>New Grid:</h4>";
}
if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'edit' ) {
	echo "<h4>Edit Grid:</h4>";

	$sql = "SELECT * FROM banners WHERE `banner_id`='" . $BID . "' ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
	$row                         = mysqli_fetch_array( $result );
	$_REQUEST['BID']             = $row['banner_id'];
	$_REQUEST['grid_width']      = $row['grid_width'];
	$_REQUEST['grid_height']     = $row['grid_height'];
	$_REQUEST['days_expire']     = $row['days_expire'];
	$_REQUEST['max_orders']      = $row['max_orders'];
	$_REQUEST['price_per_block'] = $row['price_per_block'];
	$_REQUEST['name']            = $row['name'];
	$_REQUEST['currency']        = $row['currency'];
	$_REQUEST['block_width']     = $row['block_width'];
	$_REQUEST['block_height']    = $row['block_height'];
	$_REQUEST['max_blocks']      = $row['max_blocks'];
	$_REQUEST['min_blocks']      = $row['min_blocks'];
	$_REQUEST['bgcolor']         = $row['bgcolor'];
	$_REQUEST['auto_approve']    = $row['auto_approve'];
	$_REQUEST['auto_publish']    = $row['auto_publish'];
}

if ( ( isset( $_REQUEST['new'] ) && $_REQUEST['new'] != '' ) || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'edit' ) ) {

	if ( isset( $_REQUEST['block_width'] ) && ! $_REQUEST['block_width'] ) {
		$_REQUEST['block_width'] = 10;
	}

	if ( isset( $_REQUEST['block_height'] ) && ! $_REQUEST['block_height'] ) {
		$_REQUEST['block_height'] = 10;
	}

	if ( isset( $_REQUEST['max_blocks'] ) && ! $_REQUEST['max_blocks'] ) {
		$_REQUEST['max_blocks'] = '0';
	}

	if ( isset( $_REQUEST['min_blocks'] ) && ! $_REQUEST['min_blocks'] ) {
		$_REQUEST['min_blocks'] = '0';
	}

	if ( isset( $_REQUEST['days_expire'] ) && ! $_REQUEST['days_expire'] ) {
		$_REQUEST['days_expire'] = '0';
	}

	if ( isset( $_REQUEST['max_orders'] ) && ! $_REQUEST['max_orders'] ) {
		$_REQUEST['max_orders'] = '0';
	}

	?>
    <form enctype="multipart/form-data" action='inventory.php' method="post">
        <input type="hidden" value="<?php echo( isset( $_REQUEST['new'] ) ? $_REQUEST['new'] : "" ); ?>" name="new">
        <input type="hidden" value="<?php echo( isset( $_REQUEST['edit'] ) ? $_REQUEST['edit'] : "" ); ?>" name="edit">
        <input type="hidden" value="<?php echo( isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : "" ); ?>" name="action">
        <input type="hidden" value="<?php echo( isset( $_REQUEST['BID'] ) ? $BID : "" ); ?>" name="BID">
        <input type="hidden" value="<?php echo( isset( $_REQUEST['edit_anyway'] ) ? $_REQUEST['edit_anyway'] : "" ); ?>" name="edit_anyway">
        <table border="0" cellSpacing="0" cellPadding="0" width="100%" bgcolor="#ffffff">

            <tr>
                <td width="50%" valign='top'><!-- start left column -->

                    <table border='0' cellSpacing="1" cellPadding="3" bgColor="#d9d9d9">
                        <tr>
                            <td>

                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font size="2"><b>Grid Name</b></font></td>
                            <td><input size="30" type="text" name="name" value="<?php echo( isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : "" ); ?>"/> <font size="2">eg. My Million Pixel Grid</font></td>
                        </tr>
						<?php

						$sql   = "SELECT * FROM blocks where banner_id=" . $BID . " AND status <> 'nfs' limit 1 ";
						$b_res = mysqli_query( $GLOBALS['connection'], $sql );

						if ( ( $row['banner_id'] != '' ) && ( mysqli_num_rows( $b_res ) > 0 ) ) {
							$locked = true;
						} else {
							$locked = false;
						}

						if ( isset( $_REQUEST['edit_anyway'] ) && $_REQUEST['edit_anyway'] != '' ) {

							$locked = false;
						}

						?>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font size="2"><b>Grid Width</b></font></td>
                            <td>
								<?php
								$disabled = "";
								if ( ! $locked ) {
									?>
                                    <input <?php echo $disabled; ?> size="2" type="text" name="grid_width" value="<?php echo $_REQUEST['grid_width']; ?>"/><font size="2"> Measured in blocks (default block size is 10x10 pixels)</font>
								<?php } else {

									echo "<b>" . $_REQUEST['grid_width'];
									echo "<input type='hidden' value='" . $row['grid_width'] . "' name='grid_width'> Blocks.</b> <font size='1'>Note: Cannot change width because the grid is in use by an advertiser. [<a href='inventory.php?action=edit&BID=" . $BID . "&edit_anyway=1'>Edit Anyway</a>]</font>";
								}
								?>
                            </td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea">
                                <font size="2"><b>Grid Height</b></font></td>
                            <td>
								<?php

								if ( ! $locked ) {
									?>
                                    <input <?php echo $disabled; ?> size="2" type="text" name="grid_height" value="<?php echo $_REQUEST['grid_height']; ?>"/><font size="2"> Measured in blocks (default block size is 10x10 pixels)</font>
								<?php } else {

									echo "<b>" . $_REQUEST['grid_height'];
									echo "<input type='hidden' value='" . $row['grid_height'] . "' name='grid_height'> Blocks.</b> <font size='1'> Note: Cannot change height because the grid is in use by an advertiser.[<a href='inventory.php?action=edit&BID=" . $BID . "&edit_anyway=1'>Edit Anyway</a>]</font>";
								}
								?>
                            </td>
                        </tr>

                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font size="2"><b>Price per block</b></font></td>
                            <td><input size="1" type="text" name="price_per_block" value="<?php echo $_REQUEST['price_per_block']; ?>"/><font size="2">(How much for 1 block of pixels?)</font></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font size="2"><b>Currency</b></font></td>
                            <td>
                                <select name="currency">
									<?php
									currency_option_list( $_REQUEST['currency'] );

									?>
                                </select>
                            </td>
                        </tr>

                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font size="2"><b>Days to Expire</b></font></td>
                            <td><input <?php echo $disabled; ?> size="1" type="text" name="days_expire" value="<?php echo $_REQUEST['days_expire']; ?>"/><font size="2">(How many days until pixels expire? Enter 0 for unlimited.)</font></td>
                        </tr>

                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font size="2"><b>Max orders Per Customer</b></font></td>
                            <td><input <?php echo $disabled; ?> size="1" type="text" name="max_orders" value="<?php echo $_REQUEST['max_orders']; ?>"/><font size="2">(How many orders per 1 customer? Enter 0 for unlimited.)</font><br>
                            </td>
                        </tr>

                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font size="2"><b>Max blocks</b></font></td>
                            <td><input size="1" type="text" name="max_blocks" value="<?php echo $_REQUEST['max_blocks']; ?>"/><font size="2">(Maximum amount of blocks the customer is allowerd to purchase? Enter 0 for unlimited.)</font><br>
                            </td>
                        </tr>

                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font size="2"><b>Min blocks</b></font></td>
                            <td><input size="1" type="text" name="min_blocks" value="<?php echo $_REQUEST['min_blocks']; ?>"/><font size="2">(Minumum amount of blocks the customer has to purchase per order? Enter 1 or 0 for no limit.)</font><br>
                            </td>
                        </tr>

                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font size="2"><b>Approve Automatically?</b></font></td>
                            <td>
                                <font size="1" face="Verdana">
                                    <input type="radio" name="auto_approve" value="Y" <?php if ( $_REQUEST['auto_approve'] == 'Y' ) {
										echo " checked ";
									} ?> >Yes. Approve all pixels automatically as they are submitted.<br>
                                    <input type="radio" name="auto_approve" value="N" <?php if ( $_REQUEST['auto_approve'] == 'N' ) {
										echo " checked ";
									} ?> >No, approve manually from the Admin.<br>
                                </font>
                            </td>
                        </tr>

                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font size="2"><b>Publish Automatically?</b></font></td>
                            <td>
                                <font size="1" face="Verdana">
                                    <input type="radio" name="auto_publish" value="Y" <?php if ( $_REQUEST['auto_publish'] == 'Y' ) {
										echo " checked ";
									} ?> >Yes. Process the grid image(s) automatically, every time when the pixels are approved, expired or dis-apprived.<br>
                                    <input type="radio" name="auto_publish" value="N" <?php if ( $_REQUEST['auto_publish'] == 'N' ) {
										echo " checked ";
									} ?> >No, Process manually from the admin<br>
                                </font>
                            </td>
                        </tr>
                    </table>

					<?php

					$size_error_style = "style='font-size:9px; color:#F7DAD5; border-color:#FF6600; border-style: solid'";
					$size_error_msg   = "Error: Invalid size! Must be " . $_REQUEST['block_width'] . "x" . $_REQUEST['block_height'];

					function validate_block_size( $image_name, $BID ) {

						if ( ! $BID ) {

							return true; // new grid...

						}

						$block_w = $_REQUEST['block_width'];
						$block_h = $_REQUEST['block_height'];

						$sql    = "SELECT * FROM banners where banner_id=" . intval( $BID );
						$result = mysqli_query( $GLOBALS['connection'], $sql );
						$b_row  = mysqli_fetch_array( $result );

						if ( $b_row[ $image_name ] == '' ) { // no data, assume that the default image will be loaded..

							return true;
						}

						$imagine = new Imagine\Gd\Imagine();

						$img = $imagine->load( base64_decode( $b_row[ $image_name ] ) );

						$temp_file = SERVER_PATH_TO_ADMIN . "temp/temp_block" . md5( session_id() ) . ".png";
						$img->save( $temp_file );
						$size = $img->getSize();

						unlink( $temp_file );

						if ( $size->getWidth() != $block_w ) {
							return false;
						}

						if ( $size->getHeight() != $block_h ) {
							return false;
						}

						return true;
					}

					?>

                </td>
                <td valign='top' bgcolor="#ffffff">
                    <table id="table1" border='0' cellSpacing="1" cellPadding="3" bgColor="#d9d9d9">
                        <tr bgcolor="#ffffff">
                            <td colspan="3" bgColor="#eaeaea"><b><font face="Arial" size="2">Block Configuration</font></b></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><b><font size="2" face="Arial">Block Size</font></b></td>
                            <td colspan="2">

                                <p>
                                    <input type="text" name="block_width" size="2" style="font-size: 18pt" value="<?php echo $_REQUEST['block_width']; ?>">
                                    &nbsp;<font size="6">X</font>&nbsp;
                                    <input type="text" name="block_height" size="2" style="font-size: 18pt" value="<?php echo $_REQUEST['block_height']; ?>"><br>
                                    <font face="Arial" size="2">(Width X Height, default is 10x10 in pixels)</font></p>

                        </tr>
                        <tr bgcolor="#ffffff">
                            <td colspan="3" bgColor="#eaeaea"><font face="Arial" size="2"><b>Block Graphics -
                                        Displayed on the public Grid</b></font></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font face="Arial" size="2"><b>Grid Block<?php display_reset_link( $BID, 'grid_block' ); ?></b></font></td>
                            <td bgcolor='#867C6F' <?php $valid = validate_block_size( 'grid_block', $BID );
							if ( ! $valid ) {
								echo $size_error_style;
							} ?> ><span><img src="get_block_image.php?t=<?php echo time(); ?>&BID=<?php echo $BID; ?>&image_name=grid_block" border="0"><?php if ( ! $valid ) {
										echo $size_error_msg;
										$valid = '';
									} ?></span></td>
                            <td><input type="file" name="grid_block" size="10"></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><b><font size="2" face="Arial">Not For Sale Block<?php display_reset_link( $BID, 'nfs_block' ); ?></font></b></td>
                            <td bgcolor='#867C6F' <?php $valid = validate_block_size( 'nfs_block', $BID );
							if ( ! $valid ) {
								echo $size_error_style;
							} ?>><img src="get_block_image.php?t=<?php echo time(); ?>&BID=<?php echo $BID; ?>&image_name=nfs_block" border="0"><?php if ( ! $valid ) {
									echo $size_error_msg;
									$valid = '';
								} ?></td>
                            <td><input type="file" name="nfs_block" size="10"></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><b><font size="2" face="Arial">Background Tile<?php display_reset_link( $BID, 'tile' ); ?></font></b></td>
                            <td bgcolor='#867C6F'><img src="get_block_image.php?t=<?php echo time(); ?>&BID=<?php echo $BID; ?>&image_name=tile" border="0"></td>
                            <td><input type="file" name="tile" size="10">(<font size="1" face="Verdana">This tile is used the fill the space behind the grid image. The tile will be seen before the grid image is loaded.) <b>Background color:</b> <input type='text' name='bgcolor' size='7' value='<?php echo $_REQUEST['bgcolor']; ?>'> eg. #ffffff</font></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td colspan="3" bgColor="#eaeaea"><b><font size="2" face="Arial">Block Graphics -
                                        Displayed on the ordering Grid</font></b></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font face="Arial" size="2"><b>Grid Block<?php display_reset_link( $BID, 'usr_grid_block' ); ?></b></font></td>
                            <td bgcolor='#867C6F' <?php $valid = validate_block_size( 'usr_grid_block', $BID );
							if ( ! $valid ) {
								echo $size_error_style;
							} ?>><img src="get_block_image.php?t=<?php echo time(); ?>&BID=<?php echo $BID; ?>&image_name=usr_grid_block" border="0"><?php if ( ! $valid ) {
									echo $size_error_msg;
									$valid = '';
								} ?></td>
                            <td><input type="file" name="usr_grid_block" size="10"></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><b><font size="2" face="Arial">Not For Sale Block<?php display_reset_link( $BID, 'usr_nfs_block' ); ?></font></b></td>
                            <td bgcolor='#867C6F' <?php $valid = validate_block_size( 'usr_nfs_block', $BID );
							if ( ! $valid ) {
								echo $size_error_style;
							} ?> ><img src="get_block_image.php?t=<?php echo time(); ?>&BID=<?php echo $BID; ?>&image_name=usr_nfs_block" border="0"><?php if ( ! $valid ) {
									echo $size_error_msg;
									$valid = '';
								} ?></td>
                            <td><input type="file" name="usr_nfs_block" size="10"></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font face="Arial" size="2"><b>Ordered Block<?php display_reset_link( $BID, 'usr_ord_block' ); ?></b></font></td>
                            <td bgcolor='#867C6F' <?php $valid = validate_block_size( 'usr_ord_block', $BID );
							if ( ! $valid ) {
								echo $size_error_style;
							} ?> ><img src="get_block_image.php?t=<?php echo time(); ?>&BID=<?php echo $BID; ?>&image_name=usr_ord_block" border="0"><?php if ( ! $valid ) {
									echo $size_error_msg;
									$valid = '';
								} ?></td>
                            <td><input type="file" name="usr_ord_block" size="10"></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font face="Arial" size="2"><b>Reserved Block<?php display_reset_link( $BID, 'usr_res_block' ); ?></b></font></td>
                            <td bgcolor='#867C6F' <?php $valid = validate_block_size( 'usr_res_block', $BID );
							if ( ! $valid ) {
								echo $size_error_style;
							} ?> ><img src="get_block_image.php?t=<?php echo time(); ?>&BID=<?php echo $BID; ?>&image_name=usr_res_block" border="0"><?php if ( ! $valid ) {
									echo $size_error_msg;
									$valid = '';
								} ?></td>
                            <td><input type="file" name="usr_res_block" size="10"></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><b><font size="2" face="Arial">Selected Block<?php display_reset_link( $BID, 'usr_sel_block' ); ?></font></b></td>
                            <td bgcolor='#867C6F' <?php $valid = validate_block_size( 'usr_sel_block', $BID );
							if ( ! $valid ) {
								echo $size_error_style;
							} ?> ><img src="get_block_image.php?t=<?php echo time(); ?>&BID=<?php echo $BID; ?>&image_name=usr_sel_block" border="0"><?php if ( ! $valid ) {
									echo $size_error_msg;
									$valid = '';
								} ?></td>
                            <td><input type="file" name="usr_sel_block" size="10"></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td bgColor="#eaeaea"><font face="Arial" size="2"><b>Sold Block<?php display_reset_link( $BID, 'usr_sol_block' ); ?></b></font></td>
                            <td bgcolor='#867C6F' <?php $valid = validate_block_size( 'usr_sol_block', $BID );
							if ( ! $valid ) {
								echo $size_error_style;
							} ?> ><img src="get_block_image.php?t=<?php echo time(); ?>&BID=<?php echo $BID; ?>&image_name=usr_sol_block" border="0"><?php if ( ! $valid ) {
									echo $size_error_msg;
									$valid = '';
								} ?></td>
                            <td><input type="file" name="usr_sol_block" size="10"></td>
                        </tr>
                        <!--
	<tr bgcolor="#ffffff">
	<td colspan="3">
<a href="inventory.php?action=edit&BID=<?php echo $BID ?>&default_all=yes" onclick="return confirmLink(this, 'Reset all blocks to default, are you sure?')"><font color='red' size=1>Reset all blocks to default</font></a></td>
	<tr>
	-->
                    </table>

                </td>

            </tr>
        </table>
        <input type="submit" name="submit" value="Save Grid Settings" style="font-size: 21px;">
    </form>
    <hr>
	<?php

	if ( $locked ) {
		echo "Note: The Grid Width and Grid Height fields are locked because this image has some pixels on order / sold";
	}
}

function render_offer( $price, $currency, $max_orders, $days_expire, $package_id = 0 ) {
	?>
    <font size="2">
		<?php
		if ( $package_id != 0 ) {
			//echo "<font color='#CC0033'>#$package_id </font>";
		}
		?>
        <small>Days:</small> <b><?php if ( $days_expire > 0 ) {
				echo $days_expire;
			} else {
				echo "unlimited";
			} ?></b></font><font size="2"> <small>Max Ord</small>: <b><?php if ( $max_orders > 0 ) {
				echo $max_orders;
			} else {
				echo "unlimited";
			} ?></b></font><font size="2"> <small>Price/100</small>: <b><?php echo $price; ?></font><font size="2"> <?php echo $currency; ?></b></font><br>

	<?php
}

?>

<font size='1'>Note: A grid with 100 rows and 100 columns is a million pixels. Setting this to a larger value may affect the memory & performance of the script.</font><br>

<table border="0" cellSpacing="1" cellPadding="3" bgColor="#d9d9d9">
    <tr bgColor="#eaeaea">
        <td><b><font size="2">Grid ID</b></font></td>
        <td><b><font size="2">Name</b></font></td>
        <td><b><font size="2">Grid Width</b></font></td>
        <td><b><font size="2">Grid Height</b></font></td>
        <!--
		<td><b><font size="2">Days to Exp.</b></font></td>
		<td><b><font size="2">Price /<br>Block</b></font></td>
		<td><b><font size="2">Currency</b></font></td>
		-->
        <td><b><font size="2">Offer</b></font></td>

        <td><b><font size="2">Action</b></font></td>
        <td><b><font size="2">Today's Clicks</b></font></td>
        <td><b><font size="2">Total Clicks</b></font></td>
    </tr>
	<?php
	$result = mysqli_query( $GLOBALS['connection'], "select * FROM banners" ) or die ( mysqli_error( $GLOBALS['connection'] ) );
	while ( $row = mysqli_fetch_array( $result, MYSQLI_ASSOC ) ) {

		?>
        <tr bgcolor="#ffffff">
            <td><font size="2"><?php echo $row['banner_id']; ?></font></td>
            <td><font size="2"><?php echo $row['name']; ?></font></td>
            <td><font size="2"><?php echo $row['grid_width']; ?> blocks</font></td>
            <td><font size="2"><?php echo $row['grid_height']; ?> blocks</font></td>
            <td nowrap>

				<?php

				$banner_packages = banner_get_packages( $row['banner_id'] );

				if ( ! $banner_packages ) {
					// render the default offer
					render_offer( $row['price_per_block'], $row['currency'], $row['max_orders'], $row['days_expire'] );
				} else {

					?>

					<?php while ( $p_row = mysqli_fetch_array( $banner_packages ) ) {

						render_offer( $p_row['price'], $p_row['currency'], $p_row['max_orders'], $p_row['days_expire'], $p_row['package_id'] );

						?>

					<?php }
				} ?>
            </td>
            <td><font size="2"><a href='inventory.php?action=edit&BID=<?php echo $row['banner_id']; ?>'>Edit</a> / <a href="packs.php?BID=<?php echo $row['banner_id']; ?>"> Packages</a><?php if ( $row['banner_id'] != '1' ) { ?> / <a href='inventory.php?action=delete&BID=<?php echo $row['banner_id']; ?>'>Delete</a><?php } ?></font></td>
            <td><font size="2"><?php echo get_clicks_for_today( $row['banner_id'] ); ?></font></td>
            <td><font size="2"><?php echo get_clicks_for_banner( $row['banner_id'] ); ?></font></td>

        </tr>
		<?php
	}
	?>
</table>
