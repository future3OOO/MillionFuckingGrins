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

?>
<p>
    Grid HTML - This is the HTML code that you copy and paste to your HTML documents to display the grid<br>
    Stats HTML - Copy and paste into your html file to display the stats<br>
</p>
<table width="100%" border="0" cellSpacing="1" cellPadding="3" bgColor="#d9d9d9">
    <tr bgColor="#eaeaea">
        <td><b><font size="2">Grid ID</b></font></td>
        <td><b><font size="2">Name</b></font></td>
        <td><b><font size="2">Grid HTML</b></font></td>
        <td><b><font size="2">Stats HTML</b></font></td>

    </tr>
	<?php
	$result = mysqli_query( $GLOBALS['connection'], "select * FROM banners" ) or die ( mysqli_error( $GLOBALS['connection'] ) );
	while ( $row = mysqli_fetch_array( $result, MYSQLI_ASSOC ) ) {

		?>

        <tr bgcolor="#ffffff">

            <td><font size="2"><?php echo $row['banner_id']; ?></font></td>
            <td><font size="2"><?php echo $row['name']; ?></font></td>
            <td><textarea onfocus="this.select()" rows='3' cols='35'><?php echo get_html_code( $row['banner_id'] ); ?></textarea></td>
            <td><textarea onfocus="this.select()" rows='3' cols='35'><?php echo get_stats_html_code( $row['banner_id'] ); ?></textarea></td>

        </tr>
		<?php
	}
	?>
</table>
