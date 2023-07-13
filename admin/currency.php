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

function is_reserved_currency( $code ) {

	switch ( $code ) {
		case "AUD":
			return true;
			break;
		case "CAD":
			return true;
			break;
		case "EUR":
			return true;
			break;
		case "GBP":
			return true;
			break;
		case "JPY":
			return true;
			break;
		case "USD":
			return true;
			break;
	}

	return false;
}

function validate_input() {

	$error = "";

	if ( trim( $_REQUEST['code'] ) == '' ) {
		$error .= "- Currency code is blank<br>";
	}

	if ( trim( $_REQUEST['name'] ) == '' ) {
		$error .= "- Currency name is blank<br>";
	}

	if ( trim( $_REQUEST['rate'] ) == '' ) {
		$error .= "- Currency rate is blank<br>";
	}

	if ( trim( $_REQUEST['decimal_point'] ) == '' ) {
		$error .= "- Decimal point is blank<br>";
	}

	if ( trim( $_REQUEST['thousands_sep'] ) == '' ) {
		$error .= "- Thousands seperator is blank<br>";
	}

	return $error;
}

if ( $_REQUEST['action'] == 'delete' ) {

	if ( ! is_reserved_currency( $_REQUEST['code'] ) ) {

		$sql = "DELETE FROM currencies WHERE code='" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['code'] ) . "' ";
		mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	} else {

		echo "<p><b>Cannot delete currency: reserved by the system</b></p>";
	}
}

if ( $_REQUEST['action'] == 'set_default' ) {
	$sql = "UPDATE currencies SET is_default = 'N' WHERE code <> '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['code'] ) . "' ";
	mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

	$sql = "UPDATE currencies SET is_default = 'Y' WHERE code = '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['code'] ) . "' ";
	mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
}

if ( $_REQUEST['submit'] != '' ) {

	$error = validate_input();

	if ( $error != '' ) {

		echo "Error: cannot save due to the following errors:<br>";
		echo $error;
	} else {

		$sql = "REPLACE INTO currencies(code, name, rate, sign, decimal_places, decimal_point, thousands_sep, is_default) VALUES ('" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['code'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['name'] ) . "', '" . floatval( $_REQUEST['rate'] ) . "',  '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['sign'] ) . "', '" . intval( $_REQUEST['decimal_places'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['decimal_point'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['thousands_sep'] ) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['is_default'] ) . "') ";

		//echo $sql;

		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

		$_REQUEST['new']    = '';
		$_REQUEST['action'] = '';
	}
}

?>
    <b>All currency rates are relative to the USD. (USD rate is always 1)</b><br>
    All prices will be displayed in the default currency.<br>
    Note: Rates do not update automatically!<br>
    <table border="0" cellSpacing="1" cellPadding="3" bgColor="#d9d9d9">
        <tr bgColor="#eaeaea">
            <td><span style="font-size: x-small; "><b>Currency</b></span></td>
            <td><span style="font-size: x-small; "><b>Code</b></span></td>
            <td><span style="font-size: x-small; "><b>Rate</b></span></td>
            <td><span style="font-size: x-small; "><b>Sign</b></span></td>
            <td><span style="font-size: x-small; "><b>Decimal<br>Places</b></span></td>
            <td><span style="font-size: x-small; "><b>Decimal<br>Point</b></span></td>
            <td><span style="font-size: x-small; "><b>Thousands<br>Seperator</b></span></td>
            <td><span style="font-size: x-small; "><b>Is Default</b></span></td>
            <td><span style="font-size: x-small; "><b>Action</b></span></td>
        </tr>
		<?php

		$result = mysqli_query( $GLOBALS['connection'], "select * FROM currencies order by name" ) or die ( mysqli_error( $GLOBALS['connection'] ) );
		while ( $row = mysqli_fetch_array( $result, MYSQLI_ASSOC ) ) {

			?>

            <tr bgcolor="#ffffff">

                <td><span style="font-size: x-small; "><?php echo $row['name']; ?></span></td>
                <td><span style="font-size: x-small; "><?php echo $row['code']; ?></span></td>
                <td><span style="font-size: x-small; "><?php echo $row['rate']; ?></span></td>
                <td><span style="font-size: x-small; "><?php echo $row['sign']; ?></span></td>
                <td><span style="font-size: x-small; "><?php echo $row['decimal_places']; ?></span></td>
                <td><span style="font-size: x-small; "><?php echo $row['decimal_point']; ?></span></td>
                <td><span style="font-size: x-small; "><?php echo $row['thousands_sep']; ?></span></td>
                <td><span style="font-size: x-small; "><?php echo $row['is_default']; ?></span></td>
                <td><span style="font-size: x-small; "><?php if ( $row['is_default'] != 'Y' ) { ?><a href='currency.php?action=set_default&code=<?php echo $row['code']; ?>'>Set to Default</a> /<?php } ?> <a href='currency.php?action=edit&code=<?php echo $row['code']; ?>'>Edit</a> / <a href='currency.php?action=delete&code=<?php echo $row['code']; ?>'>Delete</a></span></td>

            </tr>
			<?php
		}
		?>
    </table>
    <input type="button" value="New Currency..." onclick="mds_load_page('currency.php?new=1', true)">
<?php

if ( $_REQUEST['new'] == '1' ) {
	echo "<h4>New Currency:</h4>";
	//echo "<p>Note: Make sure that you create a file for your new language in the /lang directory.</p>";
}
if ( $_REQUEST['action'] == 'edit' ) {
	echo "<h4>Edit Currency:</h4>";

	$sql = "SELECT * FROM currencies WHERE `code`='" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['code'] ) . "' ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
	$row                        = mysqli_fetch_array( $result );
	$_REQUEST['name']           = $row['name'];
	$_REQUEST['rate']           = $row['rate'];
	$_REQUEST['sign']           = $row['sign'];
	$_REQUEST['decimal_point']  = $row['decimal_point'];
	$_REQUEST['thousands_sep']  = $row['thousands_sep'];
	$_REQUEST['decimal_places'] = $row['decimal_places'];
	$_REQUEST['is_default']     = $row['is_default'];
}

if ( ( $_REQUEST['new'] != '' ) || ( $_REQUEST['action'] == 'edit' ) ) {

	?>
    <form action='currency.php' method="post">
        <input type="hidden" value="<?php echo $_REQUEST['new'] ?>" name="new">
        <input type="hidden" value="<?php echo $_REQUEST['action'] ?>" name="action">
        <input type="hidden" value="<?php echo $_REQUEST['lang_code'] ?>" name="lang_code">
        <input type="hidden" value="<?php echo $_REQUEST['is_default'] ?>" name="is_default">
        <table border="0" cellSpacing="1" cellPadding="3" bgColor="#d9d9d9">
            <tr bgcolor="#ffffff">
                <td><span style="font-size: x-small; ">Currency Name:</span></td>
                <td><input size="30" type="text" name="name" value="<?php echo $_REQUEST['name']; ?>"/> eg. Korean Won</td>
            </tr>
            <tr bgcolor="#ffffff">
                <td><span style="font-size: x-small; ">Currency Code:</span></td>
                <td><input size="2" type="text" name="code" value="<?php echo $_REQUEST['code']; ?>"/> eg. KRW</td>
            </tr>
            <tr bgcolor="#ffffff">
                <td><span style="font-size: x-small; ">Currency Rate:</span></td>
                <td><input size="5" type="text" name="rate" value="<?php echo $_REQUEST['rate']; ?>"/>($1 USD = x in this currency)</td>
            </tr>
            <tr bgcolor="#ffffff">
                <td><span style="font-size: x-small; ">Currency Sign:</span></td>
                <td><input size="1" type="text" name="sign" value="<?php echo $_REQUEST['sign']; ?>"/>(eg. &#165;)</td>
            </tr>
            <tr bgcolor="#ffffff">
                <td><span style="font-size: x-small; ">Currency Decimals:</span></td>
                <td><input size="1" type="text" name="decimal_places" value="<?php echo $_REQUEST['decimal_places']; ?>"/>(eg. 2)</td>
            </tr>
            <tr bgcolor="#ffffff">
                <td><span style="font-size: x-small; ">Decimal Point:</span></td>
                <td><input size="1" type="text" name="decimal_point" value="<?php echo $_REQUEST['decimal_point']; ?>"/>(eg. .)</td>
            </tr>
            <tr bgcolor="#ffffff">
                <td><span style="font-size: x-small; ">Thousands Seperator:</span></td>
                <td><input size="1" type="text" name="thousands_sep" value="<?php echo $_REQUEST['thousands_sep']; ?>"/>(eg. ,)</td>
            </tr>
        </table>
        <input type="submit" name="submit" value="Submit">
    </form>

	<?php
}
