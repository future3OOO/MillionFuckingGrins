<?php
/*
 * @package       mds
 * @copyright     (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2020.11.13 08:57:00 EST
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

#########################################################################
# CONFIGURATION
# Note: Please do not edit this file. Edit the config from the admin section.
#########################################################################

error_reporting( 0 );
define( 'DEBUG', false );
define( 'MDS_LOG', false );
define( 'MDS_LOG_FILE', __DIR__ . '/.mds.log' );
define( 'VERSION_INFO', '2.1' );
define( 'BASE_HTTP_PATH', '/' );
define( 'BASE_PATH', __DIR__ );
define( 'SERVER_PATH_TO_ADMIN', __DIR__ . '/admin/' );
define( 'UPLOAD_PATH', __DIR__ . '/upload_files/' );
define( 'UPLOAD_HTTP_PATH', '/upload_files/' );
define( 'SITE_CONTACT_EMAIL', 'test@example.com' );
define( 'SITE_LOGO_URL', 'https://milliondollarscript.com/logo.gif' );
define( 'SITE_NAME', 'Million Dollar Script' );
define( 'SITE_SLOGAN', 'This is the Million Dollar Script Example. 1 pixel = 1 cent' );
define( 'MDS_RESIZE', 'YES' );
define( 'MYSQL_HOST', '' );
define( 'MYSQL_USER', '' );
define( 'MYSQL_PASS', '' );
define( 'MYSQL_DB', '' );
define( 'MYSQL_PORT', 3306 );
define( 'MYSQL_SOCKET', '' );
define( 'ADMIN_PASSWORD', 'ok' );
define( 'DATE_FORMAT', 'Y-M-d' );
define( 'GMT_DIF', date_default_timezone_get() );
define( 'DATE_INPUT_SEQ', 'YMD' );
define( 'OUTPUT_JPEG', 'N' );
define( 'JPEG_QUALITY', '75' );
define( 'INTERLACE_SWITCH', 'YES' );
define( 'USE_LOCK_TABLES', 'Y' );
define( 'BANNER_DIR', 'pixels/' );
define( 'DISPLAY_PIXEL_BACKGROUND', 'NO' );
define( 'EMAIL_USER_ORDER_CONFIRMED', 'YES' );
define( 'EMAIL_ADMIN_ORDER_CONFIRMED', 'YES' );
define( 'EMAIL_USER_ORDER_COMPLETED', 'YES' );
define( 'EMAIL_ADMIN_ORDER_COMPLETED', 'YES' );
define( 'EMAIL_USER_ORDER_PENDED', 'YES' );
define( 'EMAIL_ADMIN_ORDER_PENDED', 'YES' );
define( 'EMAIL_USER_ORDER_EXPIRED', 'YES' );
define( 'EMAIL_ADMIN_ORDER_EXPIRED', 'YES' );
define( 'EM_NEEDS_ACTIVATION', 'YES' );
define( 'EMAIL_ADMIN_ACTIVATION', 'YES' );
define( 'EMAIL_ADMIN_PUBLISH_NOTIFY', 'YES' );
define( 'USE_PAYPAL_SUBSCR', 'NO' );
define( 'EMAIL_USER_EXPIRE_WARNING', '' );
define( 'EMAILS_DAYS_KEEP', '30' );
define( 'DAYS_RENEW', '7' );
define( 'DAYS_CONFIRMED', '7' );
define( 'HOURS_UNCONFIRMED', '1' );
define( 'DAYS_CANCEL', '3' );
define( 'ENABLE_MOUSEOVER', 'POPUP' );
define( 'ENABLE_CLOAKING', 'YES' );
define( 'VALIDATE_LINK', 'NO' );
define( 'ADVANCED_CLICK_COUNT', 'YES' );
define( 'USE_SMTP', '' );
define( 'EMAIL_SMTP_SERVER', '' );
define( 'EMAIL_SMTP_USER', '' );
define( 'EMAIL_SMTP_PASS', '' );
define( 'EMAIL_SMTP_AUTH_HOST', '' );
define( 'SMTP_PORT', '465' );
define( 'POP3_PORT', '995' );
define( 'EMAIL_TLS', '1' );
define( 'EMAIL_POP_SERVER', '' );
define( 'EMAIL_POP_BEFORE_SMTP', 'NO' );
define( 'EMAIL_DEBUG', 'NO' );
define( 'EMAILS_PER_BATCH', '12' );
define( 'EMAILS_MAX_RETRY', '15' );
define( 'EMAILS_ERROR_WAIT', '20' );
define( 'USE_AJAX', 'SIMPLE' );
define( 'ANIMATION_SPEED', '50' );
define( 'MAX_BLOCKS', '' );
define( 'MEMORY_LIMIT', '128M' );
define( 'REDIRECT_SWITCH', 'NO' );
define( 'REDIRECT_URL', 'http://www.example.com' );
define( 'HIDE_TIMEOUT', '500' );
define( 'MDS_AGRESSIVE_CACHE', 'NO' );
define( 'ERROR_REPORTING', 0 );
define( 'WP_ENABLED', 'NO' );
define( 'WP_URL', '' );
