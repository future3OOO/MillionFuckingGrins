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
require_once __DIR__ . "/admin_common.php";
require_once __DIR__ . '/../include/dynamic_forms.php';
require_once __DIR__ . '/../include/ads.inc.php';

$mode = isset( $_REQUEST['mode'] ) ? $_REQUEST['mode'] : 'view';

?>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000; "></div>
<b>[Ads Form]</b><br/><br/>
<span style="background-color: <?php if ( ( $mode != 'edit' ) ) {
	echo "#FFFFCC";
} ?>; border-style:outset; padding: 5px;"><a href="aform.php?mode=view">View Form</a></span> <span style="background-color:  <?php if ( $mode == 'edit' && ( ! isset( $_REQUEST['NEW_FIELD'] ) || $_REQUEST['NEW_FIELD'] == '' ) ) {
	echo "#FFFFCC";
} ?>; border-style:outset; padding: 5px;"><a href="aform.php?mode=edit">Edit Fields</a></span> <span style="background-color: <?php if ( $mode == 'edit' && ( isset( $_REQUEST['NEW_FIELD'] ) && $_REQUEST['NEW_FIELD'] != '' ) ) {
	echo "#FFFFCC";
} ?>; border-style:outset; padding: 5px;"><a href="aform.php?NEW_FIELD=YES&mode=edit">New Field</a></span>&nbsp; &nbsp; <span style="background-color: #F2F2F2; border-style:outset; padding: 5px;"><a href="atemplate.php">Edit Template</a></span> <span style="background-color: #F2F2F2; border-style:outset; padding: 5px;"><a href="alist2.php">Ad List</a></span>

<br/>
<br/>
<br/>

<?php

global $AVAILABLE_LANGS;

echo '<div style="width:250px;float:left;">Current Language: [' . $_SESSION['MDS_LANG'] . '] Select language:</div>';

?>

<form name="lang_form" style="width:100px;float:left;" action="aform.php">
    <input type="hidden" name="mode" value="<?php echo $mode; ?>"/>
    <select name='lang' onChange="mds_submit(this)">
		<?php
		foreach ( $AVAILABLE_LANGS as $key => $val ) {
			$sel = '';
			if ( $key == $_SESSION['MDS_LANG'] ) {
				$sel = " selected ";
			}
			echo "<option $sel value='" . $key . "'>" . $val . "</option>";
		}

		?>

    </select>
</form>
<div style="clear:both;"></div>
<?php

if ( isset( $_REQUEST['NEW_FIELD'] ) && $_REQUEST['NEW_FIELD'] == 'YES' ) {
	$NEW_FIELD = 'YES';
} else {
	$NEW_FIELD = 'NO';
}

$save = $_REQUEST['save'] ?? '';
if ( $save != '' ) {

	echo "Saving...";

	$error = validate_field_form();
	if ( $error == '' ) {
		$id = mds_save_field( $error, $NEW_FIELD );
		format_field_translation_table( 1 );
		echo "OK!";
		$NEW_FIELD            = "NO";
		$_REQUEST['field_id'] = $id;
	} else {
		echo "<font color='#ff0000'><b>ERROR!</b></font><br>" . $error . '';
	}
}

if ( isset( $_REQUEST['delete'] ) && $_REQUEST['delete'] != '' ) {

	echo "Deleting...";
	$sql    = "SELECT * FROM form_fields WHERE form_id=1 and field_id='" . intval( $_REQUEST['field_id'] ) . "'";
	$result = mysqli_query( $GLOBALS['connection'], $sql );

	$row = mysqli_fetch_array( $result ) or die( mysqli_error( $GLOBALS['connection'] ) );

	if ( is_reserved_template_tag( $row['template_tag'] ) ) {

		echo "<p><font color='red'><b>Cannot Delete:</b>  This field contains a reserved 'Template Tag' and is needed by the system. Click on the 'R' icon next to the field for more information. Instead of deleting, please rename this field / change the type / move up or down. </font></p> ";
	} else {

		echo "Deleting...";
		mds_delete_field( $_REQUEST['field_id'] );
		echo "OK!";
		$_REQUEST['field_id'] = "";
	}
}
if ( is_table_unsaved( "ads" ) ) {

//echo "<br>Note: This form's database structure was not updated yet. <b>Please click 'Save Changes' button to update the database structure.</b>";

	require( 'build_ads_table.php' );
}
?>
<table>

    <tr>
        <td valign="top">
			<?php

			build_sort_fields( 1, 1 );
			build_sort_fields( 1, 2 );
			build_sort_fields( 1, 3 );

			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'move_up' ) {
				move_field_up( 1, $_REQUEST['field_id'] );
			}

			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'move_down' ) {
				move_field_down( 1, $_REQUEST['field_id'] );
			}

			if ( $NEW_FIELD == 'NO' ) {
				display_ad_form( 1, $_REQUEST['mode'] ?? '', '' );
			}
			?>

        </td>
        <td valign="top">

			<?php if ( ( ( $mode == 'edit' ) && ( $_REQUEST['field_id'] ?? '' ) ) || ( $NEW_FIELD == 'YES' ) ) {
				field_form( $NEW_FIELD, $prams ?? [], 1 );
			} ?>

        </td>

    </tr>

</table>
<?php

if ( $mode == 'edit' ) {

	?>

    <img src="images/reserved.gif" width="13" height="13" border="0" alt=""> - This field is reserved by the system, and cannot be deleted. You can however, change the field type / field name, and most other parameters.

	<?php
}

//if ( $mode != 'edit' ) {
//echo "<hr>- Preview of the search form<br><br>";

//display_dynamic_search_form (1);

//}
?>
<script>
	window.setTimeout("window.scrollTo(0,0);", 500);
</script>