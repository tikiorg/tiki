<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
	Begining with TikiWiki 3.0, PHP >=5.1 is required. This file includes soft
	implementations of functions introduced in newer versions of PHP.
*/

if( ! function_exists( 'json_encode' ) )
{
	function json_encode( $nodes )
	{
		require_once 'lib/pear/Services/JSON.php';

		$json = new Services_JSON();
		return $json->encode($nodes);
	}

	function json_decode( $string )
	{
		require_once 'lib/pear/Services/JSON.php';

		$json = new Services_JSON();
		return $json->decode($string);
	}
}

if( ! function_exists( 'array_fill_keys' ) ) {
	function array_fill_keys( $keys, $value = null )
	{
		return array_combine( $keys, array_fill( 0, count($keys), $value) );
	}
}

if ( ! function_exists('sys_getloadavg') ) {
	function sys_getloadavg()
	{
		$loadavg_file = '/proc/loadavg';
		if (file_exists($loadavg_file)) {
			return explode(chr(32),file_get_contents($loadavg_file));
		}
		return array(0,0,0);
	}
}

/* \brief  substr with a utf8 string - works only with $start and $length positive or nuls
 * This function is the same as substr but works with multibyte
 * In a multybyte sequence, the first byte of a multibyte sequence that represents a non-ASCII character is always in the range 0xC0 to 0xFD
 * and it indicates how many bytes follow for this character.
 * All further bytes in a multibyte sequence are in the range 0x80 to 0xBF.
 */
/*
if ( ! function_exists('mb_substr') ) {
    function mb_substr($str, $start, $len = '', $encoding="UTF-8"){
        $limit = strlen($str);
        for ($s = 0; $start > 0;--$start) {// found the real start
            if ($s >= $limit)
                break;
            if ($str[$s] <= "\x7F")
                ++$s;
            else {
                ++$s; // skip length
                while ($str[$s] >= "\x80" && $str[$s] <= "\xBF")
                    ++$s;
            }
        }
        if ($len == '')
            return substr($str, $s);
        else {
            for ($e = $s; $len > 0; --$len) {//found the real end
                if ($e >= $limit)
                    break;
                if ($str[$e] <= "\x7F")
                    ++$e;
                else {
                    ++$e;//skip length
                    while ($str[$e] >= "\x80" && $str[$e] <= "\xBF" && $e < $limit)
                        ++$e;
                       }
            }
        return substr($str, $s, $e - $s);
		}
    }
}
*/
