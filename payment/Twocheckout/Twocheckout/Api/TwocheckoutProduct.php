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

class Twocheckout_Product extends Twocheckout {

	public static function create( $params = array() ) {
		$request   = new Twocheckout_Api_Requester();
		$urlSuffix = '/api/products/create_product';
		$result    = $request->doCall( $urlSuffix, $params );

		return Twocheckout_Util::returnResponse( $result );
	}

	public static function retrieve( $params = array() ) {
		$request = new Twocheckout_Api_Requester();
		if ( array_key_exists( "product_id", $params ) ) {
			$urlSuffix = '/api/products/detail_product';
		} else {
			$urlSuffix = '/api/products/list_products';
		}
		$result = $request->doCall( $urlSuffix, $params );

		return Twocheckout_Util::returnResponse( $result );
	}

	public static function update( $params = array() ) {
		$request   = new Twocheckout_Api_Requester();
		$urlSuffix = '/api/products/update_product';
		$result    = $request->doCall( $urlSuffix, $params );

		return Twocheckout_Util::returnResponse( $result );
	}

	public static function delete( $params = array() ) {
		$request   = new Twocheckout_Api_Requester();
		$urlSuffix = '/api/products/delete_product';
		$result    = $request->doCall( $urlSuffix, $params );

		return Twocheckout_Util::returnResponse( $result );
	}

}