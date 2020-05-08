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

class Twocheckout_Charge extends Twocheckout {

	public static function form( $params, $type = 'Checkout' ) {
		echo '<form id="2checkout" style="text-align:center;" action="' . Twocheckout::$baseUrl . '/checkout/purchase" method="post">';

		foreach ( $params as $key => $value ) {
			echo '<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
		}
		if ( $type == 'auto' ) {
			echo '<input type="submit" value="Click here if you are not redirected automatically" /></form>';
			echo '<script type="text/javascript">document.getElementById("2checkout").submit();</script>';
		} else {
			echo '<input type="image" src="https://www.2checkout.com/upload/images/paymentlogoshorizontal.png" value="' . $type . '" />';
			echo '</form>';
		}
	}

	public static function direct( $params, $type = 'Checkout' ) {
		echo '<form id="2checkout" action="' . Twocheckout::$baseUrl . '/checkout/purchase" method="post">';

		foreach ( $params as $key => $value ) {
			echo '<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
		}

		if ( $type == 'auto' ) {
			echo '<input type="submit" value="Click here if the payment form does not open automatically." /></form>';
			echo '<script type="text/javascript">
                    function submitForm() {
                        document.getElementById("tco_lightbox").style.display = "block";
                        document.getElementById("2checkout").submit();
                    }
                    setTimeout("submitForm()", 2000);
                  </script>';
		} else {
			echo '<input type="submit" value="' . $type . '" />';
			echo '</form>';
		}

		echo '<script src="' . Twocheckout::$baseUrl . '/static/checkout/javascript/direct.min.js"></script>';
	}

	public static function link( $params ) {
		$url = Twocheckout::$baseUrl . '/checkout/purchase?' . http_build_query( $params, '', '&amp;' );

		return $url;
	}

	public static function redirect( $params ) {
		$url = Twocheckout::$baseUrl . '/checkout/purchase?' . http_build_query( $params, '', '&amp;' );
		header( "Location: $url" );
	}

	public static function auth( $params = array() ) {
		$params['api'] = 'checkout';
		$request       = new Twocheckout_Api_Requester();
		$result        = $request->doCall( '/checkout/api/1/' . self::$sid . '/rs/authService', $params );

		return Twocheckout_Util::returnResponse( $result );
	}

}
