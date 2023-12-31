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
require_once __DIR__ . "/../include/init.php";

$_PAYMENT_OBJECTS['check'] = new check;

define( 'CHECK_LOGGING', 'Y' );

function ch_mail_error( $msg ) {

	$date = date( "D, j M Y H:i:s O" );

	$headers = "From: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Reply-To: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Return-Path: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "X-Mailer: PHP" . "\r\n";
	$headers .= "Date: $date" . "\r\n";
	$headers .= "X-Sender-IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";

	@mail( SITE_CONTACT_EMAIL, "Error message from " . SITE_NAME . " Million Dollar Script check payment mod. ", $msg, $headers );
}

function ch_log_entry( $entry_line ) {

	if ( CHECK_LOGGING == 'Y' ) {

		$entry_line = "Check:$entry_line\r\n ";
		$log_fp     = fopen( "logs.txt", "a" );
		fputs( $log_fp, $entry_line );
		fclose( $log_fp );
	}
}

#
# Payment Object

class check {

	var $name = "Check / Money Order";
	var $description = "Mail funds by Check / Money Order.";
	var $className = "check";

	function __construct() {

		if ( $this->is_installed() ) {

			$sql = "SELECT * FROM config where `key`='CHECK_ENABLED' OR `key`='CHECK_PAYABLE' OR `key`='CHECK_ADDRESS'  OR `key`='CHECK_CURRENCY' OR `key`='CHECK_EMAIL_CONFIRM'";
			$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

			while ( $row = mysqli_fetch_array( $result ) ) {
				if ( ! defined( $row['key'] ) ) {
					define( $row['key'], $row['val'] );
				}
			}
		}
	}

	function get_currency() {

		return CHECK_CURRENCY;
	}

	function install() {

		$sql = "REPLACE INTO config (`key`, val) VALUES ('CHECK_ENABLED', '')";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('CHECK_CURRENCY', 'USD')";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('CHECK_PAYABLE', '')";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('CHECK_ADDRESS', '')";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('CHECK_EMAIL_CONFIRM', '')";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function uninstall() {

		$sql = "DELETE FROM config where `key`='CHECK_ENABLED'";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "DELETE FROM config where `key`='CHECK_CURRENCY'";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "DELETE FROM config where `key`='CHECK_PAYABLE'";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "DELETE FROM config where `key`='CHECK_ADDRESS'";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "DELETE FROM config where `key`='CHECK_EMAIL_CONFIRM'";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function payment_button( $order_id ) {

		global $label;

		$sql = "SELECT * from orders where order_id=" . intval( $order_id );
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$order_row = mysqli_fetch_array( $result );

		?>
        <center>

            <input type="button" value="<?php echo $label['payment_check_button']; ?>" onclick="window.location='<?php echo BASE_HTTP_PATH . "users/thanks.php?m=" . $this->className . "&order_id=" . $order_row['order_id'] . "&nhezk5=3"; ?>'">
        </center>

		<?php
	}

	function config_form() {

		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'save' ) {

			$check_enabled       = $_REQUEST['check_enabled'];
			$check_currency      = $_REQUEST['check_currency'];
			$check_payable       = $_REQUEST['check_payable'];
			$check_address       = $_REQUEST['check_address'];
			$check_email_confirm = $_REQUEST['check_email_confirm'];
		} else {
			$check_enabled       = CHECK_ENABLED;
			$check_currency      = CHECK_CURRENCY;
			$check_payable       = CHECK_PAYABLE;
			$check_address       = CHECK_ADDRESS;
			$check_email_confirm = CHECK_EMAIL_CONFIRM;
		}

		?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" width="100%" bgcolor="#FFFFFF">

                <tr>
                    <td colspan="2" bgcolor="#e6f2ea">
                        <font face="Verdana" size="1"><b>Check Payment Settings</b><br>(If you leave any field field blank, then it will not show up on the checkout)</font></td>
                </tr>
                <tr>
                    <td width="20%" bgcolor="#e6f2ea"><font face="Verdana" size="1">Payable to Name</font></td>
                    <td bgcolor="#e6f2ea"><font face="Verdana" size="1">
                            <input type="text" name="check_payable" size="29" value="<?php echo $check_payable; ?>"></font></td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea"><font face="Verdana" size="1">Payable to Address</font></td>
                    <td bgcolor="#e6f2ea"><font face="Verdana" size="1">
                            <textarea name="check_address" rows="4"><?php echo $check_address; ?></textarea></font></td>
                </tr>

                <tr>
                    <td bgcolor="#e6f2ea"><font face="Verdana" size="1">Check Currency</font></td>
                    <td bgcolor="#e6f2ea"><font face="Verdana" size="1">
                            <select name="check_currency"><?php currency_option_list( $check_currency ); ?></select></font></td>
                </tr>

                <tr>

                    <td bgcolor="#e6f2ea" colspan=2><font face="Verdana" size="1"><input type="submit" value="Save"></font>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="pay" value="<?php echo $_REQUEST['pay']; ?>">
            <input type="hidden" name="action" value="save">

        </form>

		<?php
	}

	function save_config() {

		$sql = "REPLACE INTO config (`key`, val) VALUES ('CHECK_NAME', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['check_name'] ) . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('CHECK_PAYABLE', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['check_payable'] ) . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('CHECK_ADDRESS', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['check_address'] ) . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('CHECK_CURRENCY', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['check_currency'] ) . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('CHECK_EMAIL_CONFIRM', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['check_email_confirm'] ) . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	// true or false
	function is_enabled() {

		$sql = "SELECT val from `config` where `key`='CHECK_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$row = mysqli_fetch_array( $result );
		if ( isset($row['val']) && $row['val'] == 'Y' ) {
			return true;
		} else {
			return false;
		}
	}

	function is_installed() {

		$sql = "SELECT val from config where `key`='CHECK_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		//$row = mysqli_fetch_array($result);

		if ( mysqli_num_rows( $result ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	function enable() {

		$sql = "UPDATE config set val='Y' where `key`='CHECK_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	function disable() {

		$sql = "UPDATE config set val='N' where `key`='CHECK_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	function process_payment_return() {

		global $label;

		if ( ( $_REQUEST['order_id'] != '' ) && ( $_REQUEST['nhezk5'] != '' ) ) {

			//print_r($_SESSION);

			if ( $_SESSION['MDS_ID'] == '' ) {

				echo "Error: You must be logged in to view this page";
			} else {

				//require ("../users/header.php");
				?>
                <div style='background-color: #ffffff; border-color:#C0C0C0; border-style:solid;padding:10px'>
                    <p align="center">
                    <center>
						<?php

						$sql = "SELECT * from orders where order_id='" . intval( $_REQUEST['order_id'] ) . "' and user_id='" . intval( $_SESSION['MDS_ID'] ) . "'";
						$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
						$order_row = mysqli_fetch_array( $result );

						$check_amount = convert_to_currency( $order_row['price'], $order_row['currency'], CHECK_CURRENCY );
						$check_amount = format_currency( $check_amount, CHECK_CURRENCY, true );

						$label['payment_check_heading'] = str_replace( "%INVOICE_AMOUNT%", $check_amount, $label['payment_check_heading'] );
						//$label['payment_check_note'] = str_replace ("%CONTACT_EMAIL%", SITE_CONTACT_EMAIL, $label['payment_check_note']);
						//$label['payment_check_note'] = str_replace ("%INVOICE_CODE%", $_REQUEST['order_id'], $label['payment_check_note']);

						if ( get_default_currency() != CHECK_CURRENCY ) {
							echo convert_to_default_currency_formatted( $order_row[ 'currency' ], $order_row['price'] ) . " = " . $check_amount;
							echo "<br>";
						} ?>

                        <table width="70%">
                            <tr>
                                <td>
                                    <b><?php echo $label['payment_check_heading']; ?></b><br>
									<?php if ( CHECK_NAME != '' ) { ?>
                                        <b><?php echo $label['payment_check_payable']; ?></b>
                                        <pre><?php echo CHECK_PAYABLE; ?></pre><br>
									<?php } ?>
									<?php if ( CHECK_ADDRESS != '' ) { ?>
                                        <b><?php echo $label['payment_check_address']; ?></b>
                                        <pre><?php echo CHECK_ADDRESS; ?></pre><br>
									<?php } ?>
									<?php /*if ( CHECK_ACCOUNT_NAME != '') { ?>
				<b><?php echo $label['payment_check_currency'];?></b><pre><?php echo CHECK_CURRENCY; ?></pre><br>
				<?php } */ ?>

                                </td>
                            </tr>
                        </table>

                    </p>
                    </center>

                </div>
				<?php
			}
		}
	}

}

?>