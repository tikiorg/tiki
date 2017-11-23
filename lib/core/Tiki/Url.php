<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki;

/**
 * Class Url
 */
class Url
{
	/**
	 * Handle the fallback url pref, in case the info couldn't be guessed from the request (eg. when called from cli)
	 *
	 * @return bool|array false if didn't process, array with the values written otherwise
	 */
	public static function handleFallbackUrl()
	{
		global $prefs, $https_mode, $url_scheme, $url_host, $url_port, $url_path, $base_host, $base_url, $base_url_http, $base_url_https, $tikiroot;

		if (! empty($url_host) || empty($prefs['fallbackBaseUrl'])) {
			return false;
		}

		$values = static::parseFallbackUrl($prefs['fallbackBaseUrl']);

		if ($values == false) {
			return false;
		}

		$url_scheme = $values['url_scheme'];
		$url_port = $values['url_port'];
		$https_mode = $values['https_mode'];
		$url_host = $values['url_host'];
		$url_path = $values['url_path'];
		$tikiroot = $values['tikiroot'];
		$base_host = $values['base_host'];
		$base_url = $values['base_url'];
		$base_url_http = $values['base_url_http'];
		$base_url_https = $values['base_url_https'];

		return $values;
	}

	/**
	 * Parses the fallback url in the different values of the tiki url used when building urls
	 *
	 * @param string $url the URL to process
	 * @return bool|array false if url couldn't be parsed, array of values if successful
	 */
	public static function parseFallbackUrl($url)
	{
		global $prefs;

		if (empty($url)) {
			return false;
		}

		$parts = parse_url($url);

		if ($parts === false) {
			return false;
		}

		$options['url_scheme'] = 'http';
		$options['url_port'] = $prefs['http_port'];
		$options['https_mode'] = false;
		if (isset($parts['scheme']) && $parts['scheme'] == 'https') {
			$options['url_scheme'] = 'https';
			$options['url_port'] = $prefs['https_port'];
			$options['https_mode'] = true;
		}

		$options['url_host'] = '';
		if (isset($parts['host'])) {
			$options['url_host'] = $parts['host'];
		}

		if (isset($parts['port'])) {
			$options['url_port'] = $parts['port'];
			if ($options['url_scheme'] == 'http') {
				$prefs['http_port'] = $parts['port'];
			} else {
				$prefs['https_port'] = $parts['port'];
			}
		}

		$options['tikiroot'] = empty($parts['path']) ? '/' : $parts['path'];
		$options['url_path'] = $options['tikiroot'];


		$options['base_host'] = $options['url_scheme'] . '://' . $options['url_host'] . ((isset($parts['port'])) ? ':' . $parts['port'] : '');
		$options['base_url'] = $options['url_scheme'] . '://' . $options['url_host'] . ((isset($parts['port'])) ? ':' . $parts['port'] : '') . $options['url_path'];
		$options['base_url_http'] = 'http://' . $options['url_host'] . (($prefs['http_port'] != '') ? ':' . $prefs['http_port'] : '') . $options['url_path'];
		$options['base_url_https'] = 'https://' . $options['url_host'] . (($prefs['https_port'] != '') ? ':' . $prefs['https_port'] : '') . $options['url_path'];

		return $options;
	}
}
