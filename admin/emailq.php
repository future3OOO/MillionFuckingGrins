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

ini_set( 'max_execution_time', 100200 );
require_once __DIR__ . "/../include/init.php";

$VERBOSE = "YES";

if ( $_REQUEST['action'] == "send" ) {
	$DO_SEND = "YES";
} else if ( $_SERVER['PHP_SELF'] == "" ) {
	$DO_SEND = "YES";
} else {
	?>
    <h3>This is a backend script which will process your outgoing email queue</h3><br>
    Set this file up in your Cron jobs to run <i>every few minutes</i><br>
    This scripts's location is: <b><?php echo $_SERVER['SCRIPT_FILENAME']; ?></b><br>
    Crontab command to run will look something like:<b> /usr/bin/php -f <?php echo $_SERVER['SCRIPT_FILENAME']; ?></b><br>(Depending on the location of php)<p>

        <br>Run Manually form Web:<input type="button" value="Process outgoing email queue" onclick="mds_load_page('email_q.php?action=send', true)">

	<?php
	die();
}

if ( $DO_SEND == 'YES' ) {

	process_mail_queue( EMAILS_PER_BATCH );
}
?>