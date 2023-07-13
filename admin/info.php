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
<p>System info</p>
You have PHP version: <?php echo phpversion(); ?><br><br>
<?php
if ( function_exists( 'gd_info' ) ) {
	echo 'GD Library version: <pre>' . print_r( gd_info(), true ) . '</pre>';
	if ( ! function_exists( "imagecreatetruecolor" ) ) {
		echo "imagecreatetruecolor() is not supported by your version GD. Using imagecreate() instead.";
	}
} else {
	echo "Not installed! MDS cannot function without the php-gd package.";
}
?><br>
<br>
Your path to your admin directory: <?php echo str_replace( '\\', '/', getcwd() ); ?>/
<hr>
<div class="phpinfo-container">
	<?php
	// @link https://www.mainelydesign.com/blog/view/displaying-phpinfo-without-css-styles
	ob_start();
	phpinfo();
	$pinfo = ob_get_contents();
	ob_end_clean();

	$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo );
	echo $pinfo;
	?>
</div>
