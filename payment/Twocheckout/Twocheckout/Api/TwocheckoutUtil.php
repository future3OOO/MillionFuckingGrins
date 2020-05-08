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

class Twocheckout_Util extends Twocheckout {

	static function returnResponse( $contents, $format = null ) {
		$format = $format == null ? Twocheckout::$format : $format;
		switch ( $format ) {
			case "array":
				$response = self::objectToArray( $contents );
				self::checkError( $response );
				break;
			case "force_json":
				$response = self::objectToJson( $contents );
				break;
			default:
				$response = self::objectToArray( $contents );
				self::checkError( $response );
				$response = json_encode( $contents );
				$response = json_decode( $response );
		}

		return $response;
	}

	public static function objectToArray( $object ) {
		$object = json_decode( $object, true );
		$array  = array();
		foreach ( $object as $member => $data ) {
			$array[ $member ] = $data;
		}

		return $array;
	}

	public static function objectToJson( $object ) {
		return json_encode( $object );
	}

	public static function getRecurringLineitems( $saleDetail ) {
		$i           = 0;
		$invoiceData = array();

		while ( isset( $saleDetail['sale']['invoices'][ $i ] ) ) {
			$invoiceData[ $i ] = $saleDetail['sale']['invoices'][ $i ];
			$i ++;
		}

		$invoice      = max( $invoiceData );
		$i            = 0;
		$lineitemData = array();

		while ( isset( $invoice['lineitems'][ $i ] ) ) {
			if ( $invoice['lineitems'][ $i ]['billing']['recurring_status'] == "active" ) {
				$lineitemData[ $i ] = $invoice['lineitems'][ $i ]['billing']['lineitem_id'];
			}
			$i ++;
		};

		return $lineitemData;
	}

	public static function checkError( $contents ) {
		if ( isset( $contents['errors'] ) ) {
			throw new Twocheckout_Error( $contents['errors'][0]['message'] );
		} else if ( isset( $contents['exception'] ) ) {
			throw new Twocheckout_Error( $contents['exception']['errorMsg'], $contents['exception']['errorCode'] );
		}
	}

}