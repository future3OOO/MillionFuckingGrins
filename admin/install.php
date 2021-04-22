<?php
/*
 * @package       mds
 * @copyright     (C) Copyright 2021 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2021.01.05 13:41:52 EST
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

$renamed = false;

if ( isset( $_REQUEST['action'] ) ) {

	if ( $_REQUEST['action'] == 'install' && file_exists( __DIR__ . "/../config.php" ) ) {
		save_db_config();
		require_once __DIR__ . "/../include/init.php";
		install_db();
	} else if ( $_REQUEST['action'] == 'delete' ) {
		unlink( __DIR__ . '/install.php' );
		echo "Deleted!";
		exit;
	}
} else {

	if ( file_exists( __DIR__ . "/../config.php" ) ) {
		require_once __DIR__ . "/../config.php";
	} else {
		if ( file_exists( __DIR__ . "/../config-default.php" ) ) {
			if ( rename( __DIR__ . "/../config-default.php", __DIR__ . "/../config.php" ) ) {
				$renamed = true;
				require_once __DIR__ . "/../config.php";
			} else {
				echo "Error renaming config-default.php to config.php. Please check that file permissions are correct and that the file exists.";
				exit;
			}
		}
	}
}

require_once __DIR__ . "/../include/database.php";
require_once __DIR__ . "/../include/functions2.php";

global $f2;
$f2 = new functions2();

if ( isset( $GLOBALS['connection'] ) && $GLOBALS['connection'] !== false ) {
	?>
    <h2>Database successfully Installed.</h2>
    <h3>Next Steps</h3>
    <ol>
        <li><a target="_blank" href="install.php?action=delete">Click here</a> to delete this file (/admin/install.php) from the server.</li>
        <li><a target="_blank" href="<?php echo $f2->value( BASE_HTTP_PATH ); ?>admin/">Go to Admin</a> &gt; Main Config and configure it to your liking. Default password is: <?php echo htmlspecialchars( ADMIN_PASSWORD, ENT_QUOTES, 'UTF-8' ); ?></li>
        <li>Install, enable and configure a payment module under Payment Modules.</li>
        <li>Edit your grid settings under Manage Grids.</li>
        <li>Run the Process Pixels task from the admin area to generate your initial grid image.</li>
    </ol>
	<?php
	die();
}

?>
    <h2 style="text-align:center;">Million Dollar Script - Database Installation</h2>
    <div style="font-family:'Arial', sans-serif;padding:10px;background-color:rgba(214,241,255,0.98);border-radius:10px;text-align:center;font-weight:bold;width:50%;margin:0 auto;line-height:1.5;">
        Having issues installing and want the developer to do it for you?<br/>
        <a target="_blank" href="https://milliondollarscript.com/install-service/">Order the premium install service</a>!
    </div>
    <p>
        Please fill in the form and click install.<br>
        Please make sure that the MySQL user has all the permissions to use the database (Admin privileges).<br>
    </p>
<?php
if ( $renamed === true ) {
	echo "- config-default.php renamed to config.php. (OK)</br>";
}
if ( is_writable( "../config.php" ) ) {
	echo "- config.php is writeable. (OK)<br>";
} else {
	echo "- Note: config.php is not writable. Give write permissions (666) to config.php if you want to save the changes<br>";
}
if ( is_writable( "../pixels/" ) ) {
	echo "- pixels/ directory is writeable. (OK)<br>";
} else {
	echo "- pixels/ directory is not writable. Give write permissions (777) to pixels/ directory<br>";
}

if ( is_writable( "temp/" ) ) {
	echo "- admin/temp directory is writeable. (OK)<br>";
} else {
	echo "- admin/temp directory is not writable. Give write permissions (777) to admin/temp directory<br>";
}

if ( is_writable( "../lang/english.php" ) ) {
	echo "- lang/english.php file is writeable. (OK)<br>";
} else {
	echo "- lang/english.php file is not writable. Give write permissions (666) to lang/english.php file<br>";
}

if ( is_writable( "../upload_files/docs/" ) ) {
	echo "- upload_files/docs/ directory is writeable. (OK)<br>";
} else {
	echo "- upload_files/docs/ directory is not writable. Give write permissions (777) to upload_files/docs/ directory<br>";
}

if ( is_writable( "../upload_files/images/" ) ) {
	echo "- upload_files/images/ directory is writeable. (OK)<br>";
} else {
	echo "- upload_files/images/ directory is not writable. Give write permissions (777) to upload_files/docs/ directory<br>";
}

// check HTMLPurifier permissions
if ( is_writable( "../vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer" ) ) {
	echo "- /vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer is writeable. (OK)<br>";
} else {
	echo "- Note: /vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer is not writable. Give write permissions (try 755 or 777 if that doesn't work) to /vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer<br>";
}

?>
    <form method="post" action="install.php">
        <input type="hidden" name="action" value="install">

        <h2>Mysql Settings</h2>

        <h3>MySQL Server Hostname</h3>
        <label>
            <input type="text" name="MYSQL_HOST" size="29" value="<?php echo defined( 'MYSQL_HOST' ) ? $f2->value( MYSQL_HOST ) : "localhost"; ?>">
        </label>

        <h3>MySQL Database Username</h3>

        <label>
            <input type="text" name="MYSQL_USER" size="29" value="<?php echo defined( 'MYSQL_USER' ) ? $f2->value( MYSQL_USER ) : ""; ?>">
        </label>

        <h3>MySQL Database Name</h3>
        <label>
            <input type="text" name="MYSQL_DB" size="29" value="<?php echo defined( 'MYSQL_DB' ) ? $f2->value( MYSQL_DB ) : ""; ?>">
        </label>

        <h3>MySQL Database Password</h3>
        <label>
            <input type="password" name="MYSQL_PASS" size="29" value="<?php echo defined( 'MYSQL_PASS' ) ? $f2->value( MYSQL_PASS ) : ""; ?>">
        </label>

        <h3>MySQL Port</h3>
        <label>
            <input type="text" name="MYSQL_PORT" size="29" value="<?php echo defined( 'MYSQL_PORT' ) ? $f2->value( MYSQL_PORT ) : "3306"; ?>">
        </label>

        <h3>MySQL Socket (optional)</h3>
        <label>
            <input type="text" name="MYSQL_SOCKET" size="29" value="<?php echo defined( 'MYSQL_SOCKET' ) ? $f2->value( MYSQL_SOCKET ) : ""; ?>">
        </label>

        <br/>
        <br/>
        <input type="submit" value="Install">
    </form>

<?php
// https://stackoverflow.com/a/63741626/311458
function is_ssl() {
	if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https" ) {
		return true;
	} else if ( isset( $_SERVER['HTTPS'] ) ) {
		return true;
	} else if ( $_SERVER['SERVER_PORT'] == 443 ) {
		return true;
	} else {
		return false;
	}
}

if ( ! is_ssl() ) {
	?>
    <p>It is strongly recommended to use https to install and access your site.</p>
	<?php
}

function save_db_config() {
	require_once __DIR__ . '/../include/functions2.php';
	$f2 = new functions2();

	$filename = __DIR__ . '/../config.php';
	$handle   = fopen( $filename, "w" );

	$config_str = "<?php
/**
 * Million Dollar Script Configuration
 * Note: Please do not edit this file. Edit in MDS admin under Main Config.
 */

error_reporting( E_ALL & ~E_NOTICE );
@ini_set( 'display_errors', 0 );

const MYSQL_HOST = '" . $f2->slashes( $_REQUEST['MYSQL_HOST'] ) . "';
const MYSQL_USER = '" . $f2->slashes( $_REQUEST['MYSQL_USER'] ) . "';
const MYSQL_PASS = '" . $f2->slashes( $_REQUEST['MYSQL_PASS'] ) . "';
const MYSQL_DB   = '" . $f2->slashes( $_REQUEST['MYSQL_DB'] ) . "';
const MYSQL_PORT = " . intval( $_REQUEST['MYSQL_PORT'] ) . ";
const MYSQL_SOCKET = '" . $f2->slashes( $_REQUEST['MYSQL_SOCKET'] ) . "';
";

	fwrite( $handle, $config_str );

	fclose( $handle );
}

function query_parser( $q ) {
	// strip the comments from the query
	while ( $n = strpos( $q, '--' ) ) {
		$k = @strpos( $q, "\n", $n + 1 );
		if ( ! $k ) {
			$k = strlen( $q );
		}
		$q = substr( $q, 0, $n ) . substr( $q, $k + 1 );
	}

	$queries = preg_split( "/;;;/", $q );

	return $queries;
}

function multiple_query( $q ) {
	require_once __DIR__ . "/../include/database.php";

	$queries = query_parser( $q );
	$n       = count( $queries );
	$results = array();

	for ( $i = 0; $i < $n; $i ++ ) {
		$results[ $i ] = array(
			mysqli_query( $GLOBALS['connection'], $queries[ $i ] ),
			mysqli_errno( $GLOBALS['connection'] ),
			mysqli_error( $GLOBALS['connection'] ),
			$queries[ $i ]
		);
	}

	return $results;
}

function install_db() {

	// compare MySQL version, versions newer than 5.6.5 require a different set of queries
	$mysql_server_info = mysqli_get_server_info( $GLOBALS['connection'] );

	// TODO: check for lower versions of MariaDB

	if ( version_compare( $mysql_server_info, '5.6.5' ) >= 0 ) {

		$sql = "

        CREATE TABLE `ads` (
          `ad_id` int(11) NOT NULL auto_increment,
          `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '0',
          `ad_date` datetime default CURRENT_TIMESTAMP,
          `order_id` int(11) default '0',
          `banner_id` int(11) NOT NULL default '0',
          `1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`ad_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `banners` (
          `banner_id` int(11) NOT NULL auto_increment,
          `grid_width` int(11) NOT NULL default '0',
          `grid_height` int(11) NOT NULL default '0',
          `days_expire` mediumint(9) default '0',
          `price_per_block` float NOT NULL default '0',
          `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'USD',
          `publish_date` datetime default CURRENT_TIMESTAMP,
          `max_orders` int(11) NOT NULL default '0',
          `block_width` int(11) NOT NULL default '10',
          `block_height` int(11) NOT NULL default '10',
          `grid_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `nfs_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `tile` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_grid_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_nfs_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_ord_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_res_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_sel_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_sol_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `max_blocks` int(11) NOT NULL default '0',
          `min_blocks` int(11) NOT NULL default '0',
          `date_updated` datetime default CURRENT_TIMESTAMP,
          `bgcolor` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '#FFFFFF',
          `auto_publish` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `auto_approve` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `time_stamp` int(11) default NULL,
          PRIMARY KEY  (`banner_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `banners` VALUES (1, 100, 100, 0, 100, 'Million Pixels. (1000x1000)', 'USD', NULL, 1, 10, 10, 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAHklEQVR4nGO8cuUKA27AwsDAoK2tjUuaCY/W4SwNAJbvAxP1WmxKAAAAAElFTkSuQmCC', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAFUlEQVR4nGP8//8/A27AhEduBEsDAKXjAxF9kqZqAAAAAElFTkSuQmCC', 'iVBORw0KGgoAAAANSUhEUgAAAHgAAAB4AQMAAAADqqSRAAAABlBMVEXW19b///9ZVCXjAAAAJklEQVR4nGNgQAP197///Y8gBpw/6r5R9426b9R9o+4bdd8wdB8AiRh20BqKw9IAAAAASUVORK5CYII=', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAHklEQVR4nGO8cuUKA27AwsDAoK2tjUuaCY/W4SwNAJbvAxP1WmxKAAAAAElFTkSuQmCC', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAFUlEQVR4nGP8//8/A27AhEduBEsDAKXjAxF9kqZqAAAAAElFTkSuQmCC', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAFElEQVR4nGP83+DAgBsw4ZEbwdIAJ/sB02xWjpQAAAAASUVORK5CYII=', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAE0lEQVR4nGP8/58BD2DCJzlypQF0BwISHGyJPgAAAABJRU5ErkJggg==', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAE0lEQVR4nGNk+M+ABzDhkxy50gBALQETmXEDiQAAAABJRU5ErkJggg==', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAEklEQVR4nGP8z4APMOGVHbHSAEEsAROxCnMTAAAAAElFTkSuQmCC', 500, 0, '2007-02-17 10:48:32', '#FFffFF', 'Y', 'Y', 1171775611);;;

        CREATE TABLE `categories` (
          `category_id` int(11) NOT NULL default '0',
          `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `parent_category_id` int(11) NOT NULL default '0',
          `obj_count` int(11) NOT NULL default '0',
          `form_id` int(11) NOT NULL default '0',
          `allow_records` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'Y',
          `list_order` smallint(6) NOT NULL default '1',
          `search_set` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `seo_fname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default NULL,
          `seo_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default NULL,
          `seo_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default NULL,
          `seo_keys` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default NULL,
          PRIMARY KEY  (`category_id`),
          KEY `composite_index` (`parent_category_id`,`category_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `form_fields` (
          `form_id` int(11) NOT NULL default '0',
          `field_id` int(11) NOT NULL auto_increment,
          `section` tinyint(4) NOT NULL default '1',
          `reg_expr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '-noname-',
          `field_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'TEXT',
          `field_sort` tinyint(4) NOT NULL default '0',
          `is_required` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `display_in_list` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `is_in_search` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `error_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_init` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_width` tinyint(4) NOT NULL default '20',
          `field_height` tinyint(4) NOT NULL default '0',
          `list_sort_order` tinyint(4) NOT NULL default '0',
          `search_sort_order` tinyint(4) NOT NULL default '0',
          `template_tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_hidden` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_anon` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `category_init_id` int(11) NOT NULL default '0',
          `is_cat_multiple` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `cat_multiple_rows` tinyint(4) NOT NULL default '1',
          `is_blocked` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `multiple_sel_all` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `is_prefill` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          PRIMARY KEY  (`field_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `form_fields` VALUES (1, 1, 1, 'not_empty', 'Ad Text', 'TEXT', 1, 'Y', '', '', 'was not filled in', '', 80, 0, 0, 0, 'ALT_TEXT', '', '', '', 0, '', 0, '', '', '');;;
        INSERT INTO `form_fields` VALUES (1, 2, 1, 'url', 'URL', 'TEXT', 2, 'Y', '', '', 'is not valid.', '', 80, 0, 0, 0, 'URL', '', '', '', 0, '', 0, '', '', '');;;
        INSERT INTO `form_fields` VALUES (1, 3, 1, '', 'Additional Image', 'IMAGE', 3, '', '', '', '', '', 0, 0, 0, 0, 'IMAGE', '', '', '(This image will be displayed in a tooltip popup when your blocks are clicked)', 0, '', 0, '', '', '');;;

        CREATE TABLE `form_field_translations` (
          `field_id` int(11) NOT NULL default '0',
          `lang` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_label` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `error_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          PRIMARY KEY  (`field_id`,`lang`),
          KEY `field_id` (`field_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `form_field_translations` VALUES (1, 'EN', 'Ad Text', 'was not filled in', '');;;
        INSERT INTO `form_field_translations` VALUES (2, 'EN', 'URL', 'is not valid.', '');;;
        INSERT INTO `form_field_translations` VALUES (3, 'EN', 'Additional Image', '', '(This image will be displayed in a tooltip popup when your blocks are clicked)');;;

        CREATE TABLE `form_lists` (
          `form_id` int(11) NOT NULL default '0',
          `field_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `sort_order` int(11) NOT NULL default '0',
          `field_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '0',
          `template_tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `column_id` int(11) NOT NULL auto_increment,
          `admin` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `truncate_length` smallint(4) NOT NULL default '0',
          `linked` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `clean_format` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_bold` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_sortable` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `no_wrap` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`column_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `form_lists` VALUES (1, 'TIME', 1, 'ad_date', 'DATE', 1, 'N', 0, 'N', 'N', 'N', 'Y', 'N');;;
        INSERT INTO `form_lists` VALUES (1, 'EDITOR', 2, '1', 'ALT_TEXT', 2, 'N', 0, 'Y', 'N', 'N', 'Y', 'N');;;
        INSERT INTO `form_lists` VALUES (1, 'TEXT', 3, '2', 'URL', 3, 'N', 0, 'N', 'N', 'N', 'N', 'N');;;

        CREATE TABLE `temp_orders` (
          `session_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `blocks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `order_date` datetime default CURRENT_TIMESTAMP,
          `price` float NOT NULL default '0',
          `quantity` int(11) NOT NULL default '0',
          `banner_id` int(11) NOT NULL default '1',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'USD',
          `days_expire` int(11) NOT NULL default '0',
          `date_stamp` datetime default CURRENT_TIMESTAMP,
          `package_id` int(11) NOT NULL default '0',
          `ad_id` int(11) default '0',
          `block_info` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          PRIMARY KEY  (`session_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `blocks` (
          `block_id` int(11) NOT NULL default '0',
          `user_id` int(11) default NULL,
          `status` set('reserved','sold','free','ordered','nfs') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `x` int(11) NOT NULL default '0',
          `y` int(11) NOT NULL default '0',
          `image_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `alt_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `approved` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `published` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'USD',
          `order_id` int(11) NOT NULL default '0',
          `price` float default NULL,
          `banner_id` int(11) NOT NULL default '1',
          `ad_id` INT(11)  NOT NULL default '0',
          `click_count` INT NOT NULL,
          `view_count` INT NOT NULL,
         PRIMARY KEY  (`block_id`,`banner_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `clicks` (
            `banner_id` INT NOT NULL ,
            `block_id` INT NOT NULL ,
            `user_id` INT NOT NULL ,
            `date` date default '1970-01-01',
            `clicks` INT NOT NULL ,
            PRIMARY KEY ( `banner_id` , `block_id` ,  `date` ) 
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `views` (
            `banner_id` INT NOT NULL ,
            `block_id` INT NOT NULL ,
            `user_id` INT NOT NULL ,
            `date` date default '1970-01-01',
            `views` INT NOT NULL ,
            PRIMARY KEY ( `banner_id` , `block_id` ,  `date` ) 
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `config` (
          `key` varchar(100) NOT NULL default '',
          `val` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`key`)
        );;;

        INSERT INTO `config` VALUES ('EXPIRE_RUNNING', 'NO');;;
        INSERT INTO `config` VALUES ('LAST_EXPIRE_RUN', '1138243912');;;
        INSERT INTO `config` VALUES ('SELECT_RUNNING', 'NO');;;
        INSERT INTO `config` VALUES ('dbver', 3);;;

        INSERT INTO `config` VALUES ('MDS_LOG', false);;;
        INSERT INTO `config` VALUES ('MDS_LOG_FILE', '" . realpath( __DIR__ . '/.mds.log' ) . "');;;
        INSERT INTO `config` VALUES ('VERSION_INFO', '2.1');;;
        INSERT INTO `config` VALUES ('BASE_HTTP_PATH', '/');;;
        INSERT INTO `config` VALUES ('BASE_PATH', '" . realpath( __DIR__ ) . "');;;
        INSERT INTO `config` VALUES ('SERVER_PATH_TO_ADMIN', '" . realpath( __DIR__ . '/admin/' ) . "');;;
        INSERT INTO `config` VALUES ('UPLOAD_PATH', '" . realpath( __DIR__ . '/upload_files/' ) . "');;;
        INSERT INTO `config` VALUES ('UPLOAD_HTTP_PATH', '/upload_files/');;;
        INSERT INTO `config` VALUES ('SITE_CONTACT_EMAIL', 'test@example.com');;;
        INSERT INTO `config` VALUES ('SITE_LOGO_URL', 'https://milliondollarscript.com/logo.gif');;;
        INSERT INTO `config` VALUES ('SITE_NAME', 'Million Dollar Script');;;
        INSERT INTO `config` VALUES ('SITE_SLOGAN', 'This is the Million Dollar Script Example. 1 pixel = 1 cent');;;
        INSERT INTO `config` VALUES ('MDS_RESIZE', 'YES');;;
        INSERT INTO `config` VALUES ('ADMIN_PASSWORD', 'ok');;;
        INSERT INTO `config` VALUES ('DATE_FORMAT', 'Y-M-d');;;
        INSERT INTO `config` VALUES ('GMT_DIF', '" . date_default_timezone_get() . "');;;
        INSERT INTO `config` VALUES ('DATE_INPUT_SEQ', 'YMD');;;
        INSERT INTO `config` VALUES ('OUTPUT_JPEG', 'N');;;
        INSERT INTO `config` VALUES ('JPEG_QUALITY', '75');;;
        INSERT INTO `config` VALUES ('INTERLACE_SWITCH', 'YES');;;
        INSERT INTO `config` VALUES ('BANNER_DIR', 'pixels/');;;
        INSERT INTO `config` VALUES ('DISPLAY_PIXEL_BACKGROUND', 'NO');;;
        INSERT INTO `config` VALUES ('EMAIL_USER_ORDER_CONFIRMED', 'YES');;;
        INSERT INTO `config` VALUES ('EMAIL_ADMIN_ORDER_CONFIRMED', 'YES');;;
        INSERT INTO `config` VALUES ('EMAIL_USER_ORDER_COMPLETED', 'YES');;;
        INSERT INTO `config` VALUES ('EMAIL_ADMIN_ORDER_COMPLETED', 'YES');;;
        INSERT INTO `config` VALUES ('EMAIL_USER_ORDER_PENDED', 'YES');;;
        INSERT INTO `config` VALUES ('EMAIL_ADMIN_ORDER_PENDED', 'YES');;;
        INSERT INTO `config` VALUES ('EMAIL_USER_ORDER_EXPIRED', 'YES');;;
        INSERT INTO `config` VALUES ('EMAIL_ADMIN_ORDER_EXPIRED', 'YES');;;
        INSERT INTO `config` VALUES ('EM_NEEDS_ACTIVATION', 'YES');;;
        INSERT INTO `config` VALUES ('EMAIL_ADMIN_ACTIVATION', 'YES');;;
        INSERT INTO `config` VALUES ('EMAIL_ADMIN_PUBLISH_NOTIFY', 'YES');;;
        INSERT INTO `config` VALUES ('EMAIL_USER_EXPIRE_WARNING', '');;;
        INSERT INTO `config` VALUES ('EMAILS_DAYS_KEEP', '30');;;
        INSERT INTO `config` VALUES ('DAYS_RENEW', '7');;;
        INSERT INTO `config` VALUES ('DAYS_CONFIRMED', '7');;;
        INSERT INTO `config` VALUES ('MINUTES_UNCONFIRMED', '60');;;
        INSERT INTO `config` VALUES ('DAYS_CANCEL', '3');;;
        INSERT INTO `config` VALUES ('ENABLE_MOUSEOVER', 'POPUP');;;
        INSERT INTO `config` VALUES ('ENABLE_CLOAKING', 'YES');;;
        INSERT INTO `config` VALUES ('VALIDATE_LINK', 'NO');;;
        INSERT INTO `config` VALUES ('ADVANCED_CLICK_COUNT', 'YES');;;
        INSERT INTO `config` VALUES ('USE_SMTP', '');;;
        INSERT INTO `config` VALUES ('EMAIL_SMTP_SERVER', '');;;
        INSERT INTO `config` VALUES ('EMAIL_SMTP_USER', '');;;
        INSERT INTO `config` VALUES ('EMAIL_SMTP_PASS', '');;;
        INSERT INTO `config` VALUES ('EMAIL_SMTP_AUTH_HOST', '');;;
        INSERT INTO `config` VALUES ('SMTP_PORT', '465');;;
        INSERT INTO `config` VALUES ('POP3_PORT', '995');;;
        INSERT INTO `config` VALUES ('EMAIL_TLS', '1');;;
        INSERT INTO `config` VALUES ('EMAIL_POP_SERVER', '');;;
        INSERT INTO `config` VALUES ('EMAIL_POP_BEFORE_SMTP', 'NO');;;
        INSERT INTO `config` VALUES ('EMAIL_DEBUG', 'NO');;;
        INSERT INTO `config` VALUES ('EMAILS_PER_BATCH', '12');;;
        INSERT INTO `config` VALUES ('EMAILS_MAX_RETRY', '15');;;
        INSERT INTO `config` VALUES ('EMAILS_ERROR_WAIT', '20');;;
        INSERT INTO `config` VALUES ('USE_AJAX', 'SIMPLE');;;
        INSERT INTO `config` VALUES ('MEMORY_LIMIT', '128M');;;
        INSERT INTO `config` VALUES ('REDIRECT_SWITCH', 'NO');;;
        INSERT INTO `config` VALUES ('REDIRECT_URL', 'http://www.example.com');;;
        INSERT INTO `config` VALUES ('MDS_AGRESSIVE_CACHE', 'NO');;;
        INSERT INTO `config` VALUES ('BLOCK_SELECTION_MODE', 'YES');;;
        INSERT INTO `config` VALUES ('ERROR_REPORTING', 0);;;
        INSERT INTO `config` VALUES ('WP_ENABLED', 'NO');;;
        INSERT INTO `config` VALUES ('WP_URL', '');;;
        INSERT INTO `config` VALUES ('WP_PATH', '');;;
        INSERT INTO `config` VALUES ('WP_USERS_ENABLED', 'NO');;;
        INSERT INTO `config` VALUES ('WP_ADMIN_ENABLED', 'NO');;;
        INSERT INTO `config` VALUES ('WP_USE_MAIL', 'NO');;;

        CREATE TABLE `currencies` (
          `code` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `rate` decimal(10,4) NOT NULL default '1.0000',
          `is_default` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `sign` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `decimal_places` smallint(6) NOT NULL default '0',
          `decimal_point` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `thousands_sep` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`code`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `currencies` VALUES ('AUD', 'Australian Dollar', 1.5193, 'N', '$', 2, '.', ',');;;
        INSERT INTO `currencies` VALUES ('CAD', 'Canadian Dollar', 1.3378, 'N', '$', 2, '.', ',');;;
        INSERT INTO `currencies` VALUES ('EUR', 'Euro', 0.9095, 'N', '€', 2, '.', ',');;;
        INSERT INTO `currencies` VALUES ('GBP', 'British Pound', 0.7756, 'N', '£', 2, '.', ',');;;
        INSERT INTO `currencies` VALUES ('JPY', 'Japanese Yen', 109.6790, 'N', '¥', 0, '.', ',');;;
        INSERT INTO `currencies` VALUES ('USD', 'U.S. Dollar', 1.0000, 'Y', '$', 2, '.', ',');;;

        CREATE TABLE `lang` (
          `lang_code` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `lang_filename` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `lang_image` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_active` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `charset` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `image_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_default` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          PRIMARY KEY  (`lang_code`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `lang` VALUES ('EN', 'english.php', 'english.gif', 'Y', 'English', 'en_US.utf8', 'R0lGODlhGQARAMQAAAURdBYscgNNfrUOEMkMBdAqE9UTMtItONNUO9w4SdxmaNuObhYuh0Y5lCxVlFJcpqN2ouhfjLCrrOeRmeHKr/Wy3Lje4dPW3PDTz9/q0vXm1ffP7MLt5/f0+AAAAAAAACwAAAAAGQARAAAF02AAMIDDkOgwEF3gukCZIICI1jhFDRmOS4dF50aMVSqEjehFIWQ2kJLUMRoxCCsNzDFBZDCuh1RMpQY6HZYIiOlIYqKy9JZIqHeZTqMWnvoZCgosCkIXDoeIAGJkfmgEB3UHkgp1dYuKVWJXWCsEnp4qAwUcpBwWphapFhoanJ+vKxOysxMRgbcDHRlfeboZF2mvwp+5Eh07YC9naMzNzLmKuggTDy8G19jZ2NAiFB0LBxYuC+TlC7Syai8QGU0TAs7xaNxLDLoDdsPDuS98ABXfQgAAOw==', 'image/gif', 'Y');;;

        CREATE TABLE `orders` (
          `user_id` int(11) NOT NULL default '0',
          `order_id` int(11) NOT NULL auto_increment,
          `blocks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `status` set('pending','completed','cancelled','confirmed','new','expired','deleted','renew_wait','renew_paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `order_date` datetime default CURRENT_TIMESTAMP,
          `price` float NOT NULL default '0',
          `quantity` int(11) NOT NULL default '0',
          `banner_id` int(11) NOT NULL default '1',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'USD',
          `days_expire` int(11) NOT NULL default '0',
          `date_published` datetime default CURRENT_TIMESTAMP,
          `date_stamp` datetime default CURRENT_TIMESTAMP,
          `expiry_notice_sent` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `package_id` int(11) NOT NULL default '0',
          `ad_id` int(11) default NULL,
          `approved` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `published` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `subscr_status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `original_order_id` int(11) default NULL,
          `previous_order_id` int(11) NOT NULL default '0',
          PRIMARY KEY  (`order_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `packages` (
          `banner_id` int(11) NOT NULL default '0',
          `days_expire` int(11) NOT NULL default '0',
          `price` float NOT NULL default '0',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `package_id` int(11) NOT NULL auto_increment,
          `is_default` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default NULL,
          `max_orders` mediumint(9) NOT NULL default '0',
          `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`package_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `prices` (
          `price_id` int(11) NOT NULL auto_increment,
          `banner_id` int(11) NOT NULL default '0',
          `row_from` int(11) NOT NULL default '0',
          `row_to` int(11) NOT NULL default '0',
          `block_id_from` int(11) NOT NULL default '0',
          `block_id_to` int(11) NOT NULL default '0',
          `price` float NOT NULL default '0',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            col_from int(11) default NULL,
            col_to int(11) default NULL,
          PRIMARY KEY  (`price_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `transactions` (
          `transaction_id` int(11) NOT NULL auto_increment,
          `date` datetime default CURRENT_TIMESTAMP,
          `order_id` int(11) NOT NULL default '0',
          `type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `amount` float NOT NULL default '0',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `txn_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `origin` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`transaction_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `users` (
          `ID` int(11) NOT NULL auto_increment,
          `IP` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `SignupDate` datetime default CURRENT_TIMESTAMP,
          `FirstName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `LastName` varchar(50) NOT NULL default '',
          `Rank` int(11) NOT NULL default '1',
          `Username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `Password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `Newsletter` int(11) NOT NULL default '1',
          `Notification1` int(11) NOT NULL default '0',
          `Notification2` int(11) NOT NULL default '0',
          `Aboutme` longtext NOT NULL,
          `Validated` int(11) NOT NULL default '0',
          `CompName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `login_date` datetime default CURRENT_TIMESTAMP,
          `logout_date` datetime default '1000-01-01 00:00:00',
          `login_count` int(11) NOT NULL default '0',
          `last_request_time` datetime default CURRENT_TIMESTAMP,
          `click_count` int(11) NOT NULL default '0',
          PRIMARY KEY  (`ID`),
          UNIQUE KEY `Username` (`Username`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `mail_queue` (
            `mail_id` int(11) NOT NULL auto_increment,
            `mail_date` datetime default CURRENT_TIMESTAMP,
            `to_address` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `to_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `from_address` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `from_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `html_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `attachments` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `status` set('queued','sent','error') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `error_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `retry_count` smallint(6) NOT NULL default '0',
            `template_id` int(11) NOT NULL default '0',
            `att1_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `att2_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `att3_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `date_stamp` datetime default CURRENT_TIMESTAMP,
            PRIMARY KEY  (`mail_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `cat_name_translations` (
          `category_id` int(11) NOT NULL default '0',
          `lang` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `category_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          PRIMARY KEY  (`category_id`,`lang`),
          KEY `category_id` (`category_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `codes` (
          `field_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `code` varchar(5) NOT NULL default '',
          `description` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`field_id`,`code`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `codes_translations` (
          `field_id` int(11) NOT NULL default '0',
          `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `lang` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`field_id`,`code`,`lang`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ";
	} else {

		$sql = "

        CREATE TABLE `ads` (
          `ad_id` int(11) NOT NULL auto_increment,
          `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '0',
          `ad_date` datetime NOT NULL default '0000-00-00 00:00:00',
          `order_id` int(11) default '0',
          `banner_id` int(11) NOT NULL default '0',
          `1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`ad_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `banners` (
          `banner_id` int(11) NOT NULL auto_increment,
          `grid_width` int(11) NOT NULL default '0',
          `grid_height` int(11) NOT NULL default '0',
          `days_expire` mediumint(9) default '0',
          `price_per_block` float NOT NULL default '0',
          `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'USD',
          `publish_date` datetime default NULL,
          `max_orders` int(11) NOT NULL default '0',
          `block_width` int(11) NOT NULL default '10',
          `block_height` int(11) NOT NULL default '10',
          `grid_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `nfs_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `tile` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_grid_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_nfs_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_ord_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_res_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_sel_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `usr_sol_block` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `max_blocks` int(11) NOT NULL default '0',
          `min_blocks` int(11) NOT NULL default '0',
          `date_updated` datetime NOT NULL default '0000-00-00 00:00:00',
          `bgcolor` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '#FFFFFF',
          `auto_publish` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `auto_approve` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `time_stamp` int(11) default NULL,
          PRIMARY KEY  (`banner_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `banners` VALUES (1, 100, 100, 1, 100, 'Million Pixels. (1000x1000)', 'USD', NULL, 1, 10, 10, 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAHklEQVR4nGO8cuUKA27AwsDAoK2tjUuaCY/W4SwNAJbvAxP1WmxKAAAAAElFTkSuQmCC', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAFUlEQVR4nGP8//8/A27AhEduBEsDAKXjAxF9kqZqAAAAAElFTkSuQmCC', 'iVBORw0KGgoAAAANSUhEUgAAAHgAAAB4AQMAAAADqqSRAAAABlBMVEXW19b///9ZVCXjAAAAJklEQVR4nGNgQAP197///Y8gBpw/6r5R9426b9R9o+4bdd8wdB8AiRh20BqKw9IAAAAASUVORK5CYII=', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAHklEQVR4nGO8cuUKA27AwsDAoK2tjUuaCY/W4SwNAJbvAxP1WmxKAAAAAElFTkSuQmCC', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAFUlEQVR4nGP8//8/A27AhEduBEsDAKXjAxF9kqZqAAAAAElFTkSuQmCC', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAFElEQVR4nGP83+DAgBsw4ZEbwdIAJ/sB02xWjpQAAAAASUVORK5CYII=', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAE0lEQVR4nGP8/58BD2DCJzlypQF0BwISHGyJPgAAAABJRU5ErkJggg==', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAE0lEQVR4nGNk+M+ABzDhkxy50gBALQETmXEDiQAAAABJRU5ErkJggg==', 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAIAAAACUFjqAAAAEklEQVR4nGP8z4APMOGVHbHSAEEsAROxCnMTAAAAAElFTkSuQmCC', 500, 0, '2007-02-17 10:48:32', '#FFffFF', 'Y', 'Y', 1171775611);;;

        CREATE TABLE `categories` (
          `category_id` int(11) NOT NULL default '0',
          `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `parent_category_id` int(11) NOT NULL default '0',
          `obj_count` int(11) NOT NULL default '0',
          `form_id` int(11) NOT NULL default '0',
          `allow_records` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'Y',
          `list_order` smallint(6) NOT NULL default '1',
          `search_set` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `seo_fname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default NULL,
          `seo_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default NULL,
          `seo_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default NULL,
          `seo_keys` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default NULL,
          PRIMARY KEY  (`category_id`),
          KEY `composite_index` (`parent_category_id`,`category_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `form_fields` (
          `form_id` int(11) NOT NULL default '0',
          `field_id` int(11) NOT NULL auto_increment,
          `section` tinyint(4) NOT NULL default '1',
          `reg_expr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '-noname-',
          `field_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'TEXT',
          `field_sort` tinyint(4) NOT NULL default '0',
          `is_required` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `display_in_list` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `is_in_search` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `error_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_init` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_width` tinyint(4) NOT NULL default '20',
          `field_height` tinyint(4) NOT NULL default '0',
          `list_sort_order` tinyint(4) NOT NULL default '0',
          `search_sort_order` tinyint(4) NOT NULL default '0',
          `template_tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_hidden` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_anon` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `category_init_id` int(11) NOT NULL default '0',
          `is_cat_multiple` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `cat_multiple_rows` tinyint(4) NOT NULL default '1',
          `is_blocked` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `multiple_sel_all` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `is_prefill` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          PRIMARY KEY  (`field_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `form_fields` VALUES (1, 1, 1, 'not_empty', 'Ad Text', 'TEXT', 1, 'Y', '', '', 'was not filled in', '', 80, 0, 0, 0, 'ALT_TEXT', '', '', '', 0, '', 0, '', '', '');;;
        INSERT INTO `form_fields` VALUES (1, 2, 1, 'url', 'URL', 'TEXT', 2, 'Y', '', '', 'is not valid.', '', 80, 0, 0, 0, 'URL', '', '', '', 0, '', 0, '', '', '');;;
        INSERT INTO `form_fields` VALUES (1, 3, 1, '', 'Additional Image', 'IMAGE', 3, '', '', '', '', '', 0, 0, 0, 0, 'IMAGE', '', '', '(This image will be displayed in a tooltip popup when your blocks are clicked)', 0, '', 0, '', '', '');;;

        CREATE TABLE `form_field_translations` (
          `field_id` int(11) NOT NULL default '0',
          `lang` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_label` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `error_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `field_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          PRIMARY KEY  (`field_id`,`lang`),
          KEY `field_id` (`field_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `form_field_translations` VALUES (1, 'EN', 'Ad Text', 'was not filled in', '');;;
        INSERT INTO `form_field_translations` VALUES (2, 'EN', 'URL', 'is not valid.', '');;;
        INSERT INTO `form_field_translations` VALUES (3, 'EN', 'Additional Image', '', '(This image will be displayed in a tooltip popup when your blocks are clicked)');;;

        CREATE TABLE `form_lists` (
          `form_id` int(11) NOT NULL default '0',
          `field_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `sort_order` int(11) NOT NULL default '0',
          `field_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '0',
          `template_tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `column_id` int(11) NOT NULL auto_increment,
          `admin` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `truncate_length` smallint(4) NOT NULL default '0',
          `linked` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `clean_format` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_bold` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_sortable` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `no_wrap` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`column_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `form_lists` VALUES (1, 'TIME', 1, 'ad_date', 'DATE', 1, 'N', 0, 'N', 'N', 'N', 'Y', 'N');;;
        INSERT INTO `form_lists` VALUES (1, 'EDITOR', 2, '1', 'ALT_TEXT', 2, 'N', 0, 'Y', 'N', 'N', 'Y', 'N');;;
        INSERT INTO `form_lists` VALUES (1, 'TEXT', 3, '2', 'URL', 3, 'N', 0, 'N', 'N', 'N', 'N', 'N');;;

        CREATE TABLE `temp_orders` (
          `session_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `blocks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `order_date` datetime NOT NULL default '0000-00-00 00:00:00',
          `price` float NOT NULL default '0',
          `quantity` int(11) NOT NULL default '0',
          `banner_id` int(11) NOT NULL default '1',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'USD',
          `days_expire` int(11) NOT NULL default '0',
          `date_stamp` datetime default NULL,
          `package_id` int(11) NOT NULL default '0',
          `ad_id` int(11) default '0',
          `block_info` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          PRIMARY KEY  (`session_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `blocks` (
          `block_id` int(11) NOT NULL default '0',
          `user_id` int(11) default NULL,
          `status` set('reserved','sold','free','ordered','nfs') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `x` int(11) NOT NULL default '0',
          `y` int(11) NOT NULL default '0',
          `image_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `alt_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `approved` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `published` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'USD',
          `order_id` int(11) NOT NULL default '0',
          `price` float default NULL,
          `banner_id` int(11) NOT NULL default '1',
          `ad_id` INT(11)  NOT NULL default '0',
          `click_count` INT NOT NULL,
          `view_count` INT NOT NULL,
          PRIMARY KEY  (`block_id`,`banner_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `clicks` (
            `banner_id` INT NOT NULL ,
            `block_id` INT NOT NULL ,
            `user_id` INT NOT NULL ,
            `date` date NOT NULL default '0000-00-00',
            `clicks` INT NOT NULL ,
            PRIMARY KEY ( `banner_id` , `block_id` ,  `date` ) 
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `views` (
            `banner_id` INT NOT NULL ,
            `block_id` INT NOT NULL ,
            `user_id` INT NOT NULL ,
            `date` date default '1970-01-01',
            `views` INT NOT NULL ,
            PRIMARY KEY ( `banner_id` , `block_id` ,  `date` ) 
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `config` (
          `key` varchar(100) NOT NULL default '',
          `val` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`key`)
        );;;

        INSERT INTO `config` VALUES ('EXPIRE_RUNNING', 'NO');;;
        INSERT INTO `config` VALUES ('LAST_EXPIRE_RUN', '1138243912');;;
        INSERT INTO `config` VALUES ('SELECT_RUNNING', 'NO');;;
        INSERT INTO `config` VALUES ('dbver', 3);;;

        CREATE TABLE `currencies` (
          `code` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `rate` decimal(10,4) NOT NULL default '1.0000',
          `is_default` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `sign` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `decimal_places` smallint(6) NOT NULL default '0',
          `decimal_point` char(3) NOT NULL default '',
          `thousands_sep` char(3) NOT NULL default '',
          PRIMARY KEY  (`code`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `currencies` VALUES ('AUD', 'Australian Dollar', 1.52003, 'N', '$', 2, '.', ',');;;
        INSERT INTO `currencies` VALUES ('CAD', 'Canadian Dollar', 1.33634, 'N', '$', 2, '.', ',');;;
        INSERT INTO `currencies` VALUES ('EUR', 'Euro', 0.911083, 'N', '€', 2, '.', ',');;;
        INSERT INTO `currencies` VALUES ('GBP', 'British Pound', 0.776339, 'N', '£', 2, '.', ',');;;
        INSERT INTO `currencies` VALUES ('JPY', 'Japanese Yen', 109.951, 'N', '¥', 0, '.', ',');;;
        INSERT INTO `currencies` VALUES ('USD', 'U.S. Dollar', 1.0000, 'Y', '$', 2, '.', ',');;;

        CREATE TABLE `lang` (
          `lang_code` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `lang_filename` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `lang_image` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_active` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `charset` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `image_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `is_default` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          PRIMARY KEY  (`lang_code`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        INSERT INTO `lang` VALUES ('EN', 'english.php', 'english.gif', 'Y', 'English', 'en_US.utf8', 'R0lGODlhGQARAMQAAAURdBYscgNNfrUOEMkMBdAqE9UTMtItONNUO9w4SdxmaNuObhYuh0Y5lCxVlFJcpqN2ouhfjLCrrOeRmeHKr/Wy3Lje4dPW3PDTz9/q0vXm1ffP7MLt5/f0+AAAAAAAACwAAAAAGQARAAAF02AAMIDDkOgwEF3gukCZIICI1jhFDRmOS4dF50aMVSqEjehFIWQ2kJLUMRoxCCsNzDFBZDCuh1RMpQY6HZYIiOlIYqKy9JZIqHeZTqMWnvoZCgosCkIXDoeIAGJkfmgEB3UHkgp1dYuKVWJXWCsEnp4qAwUcpBwWphapFhoanJ+vKxOysxMRgbcDHRlfeboZF2mvwp+5Eh07YC9naMzNzLmKuggTDy8G19jZ2NAiFB0LBxYuC+TlC7Syai8QGU0TAs7xaNxLDLoDdsPDuS98ABXfQgAAOw==', 'image/gif', 'Y');;;

        CREATE TABLE `orders` (
          `user_id` int(11) NOT NULL default '0',
          `order_id` int(11) NOT NULL auto_increment,
          `blocks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          `status` set('pending','completed','cancelled','confirmed','new','expired','deleted','renew_wait','renew_paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `order_date` datetime NOT NULL default '0000-00-00 00:00:00',
          `price` float NOT NULL default '0',
          `quantity` int(11) NOT NULL default '0',
          `banner_id` int(11) NOT NULL default '1',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'USD',
          `days_expire` int(11) NOT NULL default '0',
          `date_published` datetime default NULL,
          `date_stamp` datetime default NULL,
          `expiry_notice_sent` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `package_id` int(11) NOT NULL default '0',
          `ad_id` int(11) default NULL,
          `approved` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default 'N',
          `published` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `subscr_status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `original_order_id` int(11) default NULL,
          `previous_order_id` int(11) NOT NULL default '0',
          PRIMARY KEY  (`order_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `packages` (
          `banner_id` int(11) NOT NULL default '0',
          `days_expire` int(11) NOT NULL default '0',
          `price` float NOT NULL default '0',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `package_id` int(11) NOT NULL auto_increment,
          `is_default` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default NULL,
          `max_orders` mediumint(9) NOT NULL default '0',
          `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`package_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `prices` (
          `price_id` int(11) NOT NULL auto_increment,
          `banner_id` int(11) NOT NULL default '0',
          `row_from` int(11) NOT NULL default '0',
          `row_to` int(11) NOT NULL default '0',
          `block_id_from` int(11) NOT NULL default '0',
          `block_id_to` int(11) NOT NULL default '0',
          `price` float NOT NULL default '0',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            col_from int(11) default NULL,
            col_to int(11) default NULL,
          PRIMARY KEY  (`price_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `transactions` (
          `transaction_id` int(11) NOT NULL auto_increment,
          `date` datetime NOT NULL default '0000-00-00 00:00:00',
          `order_id` int(11) NOT NULL default '0',
          `type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `amount` float NOT NULL default '0',
          `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `txn_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `origin` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`transaction_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `users` (
          `ID` int(11) NOT NULL auto_increment,
          `IP` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `SignupDate` datetime NOT NULL default '0000-00-00 00:00:00',
          `FirstName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `LastName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `Rank` int(11) NOT NULL default '1',
          `Username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `Password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `Newsletter` int(11) NOT NULL default '1',
          `Notification1` int(11) NOT NULL default '0',
          `Notification2` int(11) NOT NULL default '0',
          `Aboutme` longtext NOT NULL,
          `Validated` int(11) NOT NULL default '0',
          `CompName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `login_date` datetime NOT NULL default '0000-00-00 00:00:00',
          `logout_date` datetime NOT NULL default '0000-00-00 00:00:00',
          `login_count` int(11) NOT NULL default '0',
          `last_request_time` datetime NOT NULL default '0000-00-00 00:00:00',
          `click_count` int(11) NOT NULL default '0',
          PRIMARY KEY  (`ID`),
          UNIQUE KEY `Username` (`Username`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `mail_queue` (
            `mail_id` int(11) NOT NULL auto_increment,
            `mail_date` datetime NOT NULL default '0000-00-00 00:00:00',
            `to_address` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `to_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `from_address` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `from_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `html_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `attachments` set('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `status` set('queued','sent','error') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `error_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `retry_count` smallint(6) NOT NULL default '0',
            `template_id` int(11) NOT NULL default '0',
            `att1_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `att2_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `att3_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
            `date_stamp` datetime NOT NULL default '0000-00-00 00:00:00',
            PRIMARY KEY  (`mail_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `cat_name_translations` (
          `category_id` int(11) NOT NULL default '0',
          `lang` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `category_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
          PRIMARY KEY  (`category_id`,`lang`),
          KEY `category_id` (`category_id`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `codes` (
          `field_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `description` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`field_id`,`code`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;;

        CREATE TABLE `codes_translations` (
          `field_id` int(11) NOT NULL default '0',
          `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          `lang` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL default '',
          PRIMARY KEY  (`field_id`,`code`,`lang`)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ";
	}

	/* You can use it like this */

	$queries = multiple_query( $sql );

	for ( $i = 0; $i < count( $queries ); $i ++ ) {
		if ( $queries[ $i ][1] == 0 ) {
			/* some code.... with the result in $queries[$i][0] */
		} else {
			echo "<pre>Error: " . $queries[ $i ][2] . "(" . $queries[ $i ][3] . ")<br>\n</pre>";
		}
	}

	//$result = mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']));
	//$rows = mysqli_affected_rows ($result);;;
	echo count( $queries ) . " Operations Completed.";
}