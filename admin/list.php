<?php
/*
 * @package       mds
 * @copyright     (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2020.11.13 08:56:55 EST
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

$bid_sql = " AND banner_id=$BID ";

if ( ( $BID == 'all' ) || ( $BID == '' ) ) {
	$BID     = '';
	$bid_sql = "  ";
}

$sql = "Select * from banners ";
$res = mysqli_query( $GLOBALS['connection'], $sql );
?>
<form name="bidselect" method="post" action="list.php">
    <input type="hidden" name="old_order_id" value="<?php echo $order_id; ?>">
    Select grid: <select name="BID" onchange="mds_submit(this)">

		<?php
		while ( $row = mysqli_fetch_array( $res ) ) {

			if ( ( $row['banner_id'] == $BID ) && ( $BID != 'all' ) ) {
				$sel = 'selected';
			} else {
				$sel = '';
			}
			echo '<option ' . $sel . ' value=' . $row['banner_id'] . '>' . $row['name'] . '</option>';
		}
		?>
    </select>
</form>
<hr>
<p>
    Here is the list of your top advertisers for the selected grid. <b>To have this list on your own page, copy and paste the following HTML code.</b>

</p>
<?php

$box = '<style type="text/css">
    #bubble {
        position: absolute;
        left: 0;
        top: 0;
        visibility: hidden;
        background-color: #FFFFFF;
        border-color: #33CCFF;
        border-style: solid;
        border-width: 1px;
        padding: 5px;
        margin: 0;
        width: 200px;
        filter: revealtrans();
        font-family: Arial, sans-serif;
        font-size: 11px;
    }

    #content {
        padding: 0;
        margin: 0
    }
</style>
<div onmouseout="hideBubble()" id="bubble">
<span id="content">
</span>
</div>
';
?>

<?php

?>
<TEXTAREA style='font-size: 10px;' rows='10' onfocus="this.select()" cols="90%"><?php echo htmlentities( $box . '<script src="' . BASE_HTTP_PATH . 'top_ads_js.php?BID=' . $BID . '"></script>' ); ?></TEXTAREA>

<hr>

<?php include( BASE_PATH . "/include/top_ads_js.php" ); ?>
