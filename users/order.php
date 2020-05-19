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
require_once __DIR__ . "/../include/init.php";

require_once BASE_PATH . "/include/login_functions.php";

process_login();

require_once BASE_PATH . "/html/header.php";

$BID = $f2->bid();

$banner_data  = load_banner_constants( $BID );
$has_packages = banner_get_packages( $BID );

if ( $_REQUEST['order_id'] ) {
	$_SESSION['MDS_order_id'] = $_REQUEST['order_id'];
}

$cannot_get_package = false;

if ( $has_packages && $_REQUEST['pack'] != '' ) {

	// check to make sure this advertiser can order this package
	if ( can_user_get_package( $_SESSION['MDS_ID'], $_REQUEST['pack'] ) ) {

		$sql = "SELECT quantity FROM orders WHERE order_id='" . intval( $_REQUEST['order_id'] ) . "'";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$row      = mysqli_fetch_array( $result );
		$quantity = $row['quantity'];

		$block_count = $quantity / ( $banner_data['block_width'] * $banner_data['block_height'] );

		// Now update the order (overwrite the total & days_expire with the package)
		$pack  = get_package( $_REQUEST['pack'] );
		$total = $pack['price'] * $block_count;

		// convert & round off
		$total = convert_to_default_currency( $pack['currency'], $total );

		$sql = "UPDATE orders SET package_id='" . intval( $_REQUEST['pack'] ) . "', price='" . floatval( $total ) . "',  days_expire='" . intval( $pack['days_expire'] ) . "', currency='" . mysqli_real_escape_string( $GLOBALS['connection'], get_default_currency() ) . "' WHERE order_id='" . intval( $_SESSION['MDS_order_id'] ) . "'";

		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	} else {
		$selected_pack      = $_REQUEST['pack'];
		$_REQUEST['pack']   = '';
		$cannot_get_package = true;
	}
}

// check to make sure MIN_BLOCKS were selected.
$sql = "SELECT block_id FROM blocks WHERE user_id='" . intval( $_SESSION['MDS_ID'] ) . "' AND status='reserved' AND banner_id='$BID' ";
$res = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
$count = mysqli_num_rows( $res );
if ( $count < $banner_data['G_MIN_BLOCKS'] ) {
	$not_enough_blocks = true;
}

?>
    <p>
		<?php
		$label['advertiser_o_navmap'] = str_replace( "%BID%", $BID, $label['advertiser_o_navmap'] );
		echo $label['advertiser_o_navmap'];

		?>
    </p>
<?php

$sql = "SELECT * from orders where order_id='" . intval( $_SESSION['MDS_order_id'] ) . "' and banner_id='$BID'";

$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
$order_row = mysqli_fetch_array( $result );

function display_edit_order_button( $order_id ) {
	global $BID, $label;
	?>
    <input type='button' value="<?php echo $label['advertiser_o_edit_button']; ?>" Onclick="window.location='select.php?&jEditOrder=true&BID=<?php echo $BID; ?>&order_id=<?php echo $order_id; ?>'">
	<?php
}

if ( ( $order_row['order_id'] == '' ) || ( ( $order_row['quantity'] == '0' ) ) ) {
	$label['advertiser_o_nopixels'] = str_replace( "%BID%", $BID, $label['advertiser_o_nopixels'] );
	echo "<h3>" . $label['advertiser_o_nopixels'] . "</a></h3>";
} else if ( $not_enough_blocks ) {

	echo "<h3>" . $label['order_min_blocks'] . "</h3>";
	$label['order_min_blocks_req'] = str_replace( '%MIN_BLOCKS%', $banner_data['G_MIN_BLOCKS'], $label['order_min_blocks_req'] );
	echo "<p>" . $label['order_min_blocks_req'] . "</p>";
	display_edit_order_button( $_SESSION['MDS_order_id'] );
} else {

	if ( ( $has_packages ) && ( $_REQUEST['pack'] == '' ) ) {

		echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
		?>
        <input type="hidden" name="selected_pixels" value="<?php echo $_REQUEST['selected_pixels']; ?>">
        <input type="hidden" name="order_id" value="<?php echo $_REQUEST['order_id']; ?>">
        <input type="hidden" name="BID" value="<?php echo $BID; ?>">
		<?php
		display_package_options_table( $BID, $_REQUEST['pack'], true );
		echo "<input type='button' value='" . $label['advertiser_pack_prev_button'] . "' onclick='window.location=\"select.php?&jEditOrder=true&BID=$BID&order_id=" . $order_row['order_id'] . "\"' >";
		echo "&nbsp; <input type='submit' value='" . $label['advertiser_pack_select_button'] . "'>";
		echo "<form>";

		if ( $cannot_get_package ) {

			$sql = "SELECT * from packages where package_id='" . intval( $selected_pack ) . "'";
			$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) );
			$row = mysqli_fetch_array( $result );

			$label['pack_cannot_select'] = str_replace( "%MAX_ORDERS%", $row['max_orders'], $label['pack_cannot_select'] );

			echo "<p>" . $label['pack_cannot_select'] . "</p>";
		}
	} else {
		display_order( get_current_order_id(), $BID );
		$sql = "select * from users where ID='" . intval( $_SESSION['MDS_ID'] ) . "'";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$u_row = mysqli_fetch_array( $result );

		?>
		<?php display_edit_order_button( $order_row['order_id'] ); ?> &nbsp; &nbsp;
		<?php

		if ( ( $order_row['price'] == 0 ) || ( $u_row['Rank'] == 2 ) ) {
			?>
            <input type='button' value="<?php echo $label['advertiser_o_completebutton']; ?>" Onclick="window.location='publish.php?action=complete&order_id=<?php echo $order_row['order_id']; ?>&BID=<?php echo $BID; ?>&order_id=<?php echo $order_row['order_id']; ?>'">
			<?php
		} else {

			?>
            <input type='button' value="<?php echo $label['advertiser_o_confpay_button']; ?>" Onclick="window.location='payment.php?action=confirm&order_id=<?php echo $order_row['order_id']; ?>&BID=<?php echo $BID; ?>'">
            <hr>
			<?php
		}
	}
}

require_once BASE_PATH . "/html/footer.php";
