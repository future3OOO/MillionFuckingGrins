<?php
/**
 * @package       mds
 * @copyright     (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2020.05.08 17:42:17 EDT
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

class Twocheckout_Sale extends Twocheckout {

	public static function retrieve( $params = array() ) {
		$request = new Twocheckout_Api_Requester();
		if ( array_key_exists( "sale_id", $params ) || array_key_exists( "invoice_id", $params ) ) {
			$urlSuffix = '/api/sales/detail_sale';
		} else {
			$urlSuffix = '/api/sales/list_sales';
		}
		$result = $request->doCall( $urlSuffix, $params );

		return Twocheckout_Util::returnResponse( $result );
	}

	public static function refund( $params = array() ) {
		$request = new Twocheckout_Api_Requester();
		if ( array_key_exists( "lineitem_id", $params ) ) {
			$urlSuffix = '/api/sales/refund_lineitem';
			$result    = $request->doCall( $urlSuffix, $params );
		} else if ( array_key_exists( "invoice_id", $params ) || array_key_exists( "sale_id", $params ) ) {
			$urlSuffix = '/api/sales/refund_invoice';
			$result    = $request->doCall( $urlSuffix, $params );
		} else {
			$result = Twocheckout_Message::message( 'Error', 'You must pass a sale_id, invoice_id or lineitem_id to use this method.' );
		}

		return Twocheckout_Util::returnResponse( $result );
	}

	public static function stop( $params = array() ) {
		$request   = new Twocheckout_Api_Requester();
		$urlSuffix = '/api/sales/stop_lineitem_recurring';
		if ( array_key_exists( "lineitem_id", $params ) ) {
			$result = $request->doCall( $urlSuffix, $params );
		} else if ( array_key_exists( "sale_id", $params ) ) {
			$result = Twocheckout_Sale::retrieve( $params );
			if ( ! is_array( $result ) ) {
				$result = Twocheckout_Util::returnResponse( $result, 'array' );
			}
			$lineitemData = Twocheckout_Util::getRecurringLineitems( $result );
			if ( isset( $lineitemData[0] ) ) {
				$i                = 0;
				$stoppedLineitems = array();
				foreach ( $lineitemData as $value ) {
					$params = array( 'lineitem_id' => $value );
					$result = $request->doCall( $urlSuffix, $params );
					$result = json_decode( $result, true );
					if ( $result['response_code'] == "OK" ) {
						$stoppedLineitems[ $i ] = $value;
					}
					$i ++;
				}
				$result = Twocheckout_Message::message( 'OK', $stoppedLineitems );
			} else {
				throw new Twocheckout_Error( "No recurring lineitems to stop." );
			}
		} else {
			throw new Twocheckout_Error( 'You must pass a sale_id or lineitem_id to use this method.' );
		}

		return Twocheckout_Util::returnResponse( $result );
	}

	public static function active( $params = array() ) {
		if ( array_key_exists( "sale_id", $params ) ) {
			$result = Twocheckout_Sale::retrieve( $params );
			if ( ! is_array( $result ) ) {
				$result = Twocheckout_Util::returnResponse( $result, 'array' );
			}
			$lineitemData = Twocheckout_Util::getRecurringLineitems( $result );
			if ( isset( $lineitemData[0] ) ) {
				$result = Twocheckout_Message::message( 'OK', $lineitemData );

				return Twocheckout_Util::returnResponse( $result );
			} else {
				throw new Twocheckout_Error( "No active recurring lineitems." );
			}
		} else {
			throw new Twocheckout_Error( "You must pass a sale_id to use this method." );
		}
	}

	public static function comment( $params = array() ) {
		$request   = new Twocheckout_Api_Requester();
		$urlSuffix = '/api/sales/create_comment';
		$result    = $request->doCall( $urlSuffix, $params );

		return Twocheckout_Util::returnResponse( $result );
	}

	public static function ship( $params = array() ) {
		$request   = new Twocheckout_Api_Requester();
		$urlSuffix = '/api/sales/mark_shipped';
		$result    = $request->doCall( $urlSuffix, $params );

		return Twocheckout_Util::returnResponse( $result );
	}

	public static function reauth( $params = array() ) {
		$request   = new Twocheckout_Api_Requester();
		$urlSuffix = '/api/sales/reauth';
		$result    = $request->doCall( $urlSuffix, $params );

		return Twocheckout_Util::returnResponse( $result );
	}

}