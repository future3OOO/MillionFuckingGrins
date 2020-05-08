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

/**
 * Payment method liqpay process
 *
 * @author      Liqpay <support@liqpay.ua>
 */
class LiqPayAPI {
	const CURRENCY_EUR = 'EUR';
	const CURRENCY_USD = 'USD';
	const CURRENCY_UAH = 'UAH';
	const CURRENCY_RUB = 'RUB';
	const CURRENCY_RUR = 'RUR';

	private $_api_url = 'https://www.liqpay.ua/api/';
	private $_checkout_url = 'https://www.liqpay.ua/api/3/checkout';
	protected $_supportedCurrencies = array(
		self::CURRENCY_EUR,
		self::CURRENCY_USD,
		self::CURRENCY_UAH,
		self::CURRENCY_RUB,
		self::CURRENCY_RUR,
	);
	private $_public_key;
	private $_private_key;
	private $_server_response_code = null;

	/**
	 * Constructor.
	 *
	 * @param string $public_key
	 * @param string $private_key
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $public_key, $private_key ) {
		if ( empty( $public_key ) ) {
			throw new InvalidArgumentException( 'public_key is empty' );
		}

		if ( empty( $private_key ) ) {
			throw new InvalidArgumentException( 'private_key is empty' );
		}

		$this->_public_key  = $public_key;
		$this->_private_key = $private_key;
	}

	/**
	 * Call API
	 *
	 * @param string $path
	 * @param array $params
	 *
	 * @return string
	 */
	public function api( $path, $params = array() ) {
		if ( ! isset( $params['version'] ) ) {
			throw new InvalidArgumentException( 'version is null' );
		}
		$url         = $this->_api_url . $path;
		$public_key  = $this->_public_key;
		$private_key = $this->_private_key;
		$data        = base64_encode( json_encode( array_merge( compact( 'public_key' ), $params ) ) );
		$signature   = base64_encode( sha1( $private_key . $data . $private_key, 1 ) );
		$postfields  = http_build_query( array(
			'data'      => $data,
			'signature' => $signature
		) );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $postfields );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$server_output               = curl_exec( $ch );
		$this->_server_response_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		return json_decode( $server_output );
	}

	/**
	 * Return last api response http code
	 * @return string|null
	 */
	public function get_response_code() {
		return $this->_server_response_code;
	}

	/**
	 * cnb_form
	 *
	 * @param array $params
	 *
	 * @return string
	 *
	 * @throws InvalidArgumentException
	 */
	public function cnb_form( $params ) {
		$language = 'ru';
		if ( isset( $params['language'] ) && $params['language'] == 'en' ) {
			$language = 'en';
		}

		$params    = $this->cnb_params( $params );
		$data      = base64_encode( json_encode( $params ) );
		$signature = $this->cnb_signature( $params );

		return sprintf( '
            <form method="POST" action="%s" accept-charset="utf-8">
                %s
                %s
                <input id="btn_liqpay" type="image" src="//static.liqpay.ua/buttons/p1%s.radius.png" name="btn_text" />
            </form>
            ', $this->_checkout_url, sprintf( '<input type="hidden" name="%s" value="%s" />', 'data', $data ), sprintf( '<input type="hidden" name="%s" value="%s" />', 'signature', $signature ), $language );
	}

	/**
	 * cnb_signature
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public function cnb_signature( $params ) {
		$params      = $this->cnb_params( $params );
		$private_key = $this->_private_key;

		$json      = base64_encode( json_encode( $params ) );
		$signature = $this->str_to_sign( $private_key . $json . $private_key );

		return $signature;
	}

	/**
	 * cnb_params
	 *
	 * @param array $params
	 *
	 * @return array $params
	 */
	public function cnb_params( $params ) {
		$params['public_key'] = $this->_public_key;

		if ( ! isset( $params['version'] ) ) {
			throw new InvalidArgumentException( 'version is null' );
		}
		if ( ! isset( $params['amount'] ) ) {
			throw new InvalidArgumentException( 'amount is null' );
		}
		if ( ! isset( $params['currency'] ) ) {
			throw new InvalidArgumentException( 'currency is null' );
		}
		if ( ! in_array( $params['currency'], $this->_supportedCurrencies ) ) {
			throw new InvalidArgumentException( 'currency is not supported' );
		}
		if ( $params['currency'] == self::CURRENCY_RUR ) {
			$params['currency'] = self::CURRENCY_RUB;
		}
		if ( ! isset( $params['description'] ) ) {
			throw new InvalidArgumentException( 'description is null' );
		}

		return $params;
	}

	/**
	 * str_to_sign
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public function str_to_sign( $str ) {
		$signature = base64_encode( sha1( $str, 1 ) );

		return $signature;
	}
}
