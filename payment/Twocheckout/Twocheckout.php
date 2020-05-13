<?php
/**
 * @package       mds
 * @copyright     (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2020.05.13 12:41:16 EDT
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

abstract class Twocheckout {
	public static $sid;
	public static $privateKey;
	public static $username;
	public static $password;
	public static $sandbox;
	public static $verifySSL = true;
	public static $baseUrl = 'https://www.2checkout.com';
	public static $error;
	public static $format = 'array';
	const VERSION = '0.3.0';

	public static function sellerId( $value = null ) {
		self::$sid = $value;
	}

	public static function privateKey( $value = null ) {
		self::$privateKey = $value;
	}

	public static function username( $value = null ) {
		self::$username = $value;
	}

	public static function password( $value = null ) {
		self::$password = $value;
	}

	public static function sandbox( $value = null ) {
		if ( $value == 1 || $value == true ) {
			self::$sandbox = true;
			self::$baseUrl = 'https://sandbox.2checkout.com';
		} else {
			self::$sandbox = false;
			self::$baseUrl = 'https://www.2checkout.com';
		}
	}

	public static function verifySSL( $value = null ) {
		if ( $value == 0 || $value == false ) {
			self::$verifySSL = false;
		} else {
			self::$verifySSL = true;
		}
	}

	public static function format( $value = null ) {
		self::$format = $value;
	}
}

require( dirname( __FILE__ ) . '/Twocheckout/Api/TwocheckoutAccount.php' );
require( dirname( __FILE__ ) . '/Twocheckout/Api/TwocheckoutPayment.php' );
require( dirname( __FILE__ ) . '/Twocheckout/Api/TwocheckoutApi.php' );
require( dirname( __FILE__ ) . '/Twocheckout/Api/TwocheckoutSale.php' );
require( dirname( __FILE__ ) . '/Twocheckout/Api/TwocheckoutProduct.php' );
require( dirname( __FILE__ ) . '/Twocheckout/Api/TwocheckoutCoupon.php' );
require( dirname( __FILE__ ) . '/Twocheckout/Api/TwocheckoutOption.php' );
require( dirname( __FILE__ ) . '/Twocheckout/Api/TwocheckoutUtil.php' );
require( dirname( __FILE__ ) . '/Twocheckout/Api/TwocheckoutError.php' );
require( dirname( __FILE__ ) . '/Twocheckout/TwocheckoutReturn.php' );
require( dirname( __FILE__ ) . '/Twocheckout/TwocheckoutNotification.php' );
require( dirname( __FILE__ ) . '/Twocheckout/TwocheckoutCharge.php' );
require( dirname( __FILE__ ) . '/Twocheckout/TwocheckoutMessage.php' );
