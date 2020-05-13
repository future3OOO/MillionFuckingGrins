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

class Twocheckout_Api_Requester {
	public $baseUrl;
	public $environment;
	private $user;
	private $pass;
	private $sid;
	private $privateKey;

	function __construct() {
		$this->user       = Twocheckout::$username;
		$this->pass       = Twocheckout::$password;
		$this->sid        = Twocheckout::$sid;
		$this->baseUrl    = Twocheckout::$baseUrl;
		$this->verifySSL  = Twocheckout::$verifySSL;
		$this->privateKey = Twocheckout::$privateKey;
	}

	function doCall( $urlSuffix, $data = array() ) {
		$url = $this->baseUrl . $urlSuffix;
		$ch  = curl_init( $url );
		if ( isset( $data['api'] ) ) {
			unset( $data['api'] );
			$data['privateKey'] = $this->privateKey;
			$data['sellerId']   = $this->sid;
			$data               = json_encode( $data );
			$header             = array( "content-type:application/json", "content-length:" . strlen( $data ) );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		} else {
			$header = array( "Accept: application/json" );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_POST, 0 );
			curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
			curl_setopt( $ch, CURLOPT_USERPWD, "{$this->user}:{$this->pass}" );
		}
		if ( $this->verifySSL == false ) {
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		}
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $ch, CURLOPT_USERAGENT, "2Checkout PHP/0.1.0%s" );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		$resp = curl_exec( $ch );
		curl_close( $ch );
		if ( $resp === false ) {
			throw new Twocheckout_Error( "cURL call failed", "403" );
		} else {
			return utf8_encode( $resp );
		}
	}

}
