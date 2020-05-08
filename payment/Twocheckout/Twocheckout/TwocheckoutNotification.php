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

class Twocheckout_Notification extends Twocheckout {

	public static function check( $insMessage = array(), $secretWord ) {
		$hashSid      = $insMessage['vendor_id'];
		$hashOrder    = $insMessage['sale_id'];
		$hashInvoice  = $insMessage['invoice_id'];
		$StringToHash = strtoupper( md5( $hashOrder . $hashSid . $hashInvoice . $secretWord ) );
		if ( $StringToHash != $insMessage['md5_hash'] ) {
			$result = Twocheckout_Message::message( 'Fail', 'Hash Mismatch' );
		} else {
			$result = Twocheckout_Message::message( 'Success', 'Hash Matched' );
		}

		return Twocheckout_Util::returnResponse( $result );
	}

}
