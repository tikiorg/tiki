<?php
	/**
	  * encode a string
	  * @param string $string : the string in utf-8
	  * @param $charset: iso8859-1 or utf-8
	  */
function encodeString($string, $charset="utf-8") {
	if ($charset == "iso-8859-1")
		return utf8_decode($string);
	/* add other charsets */
	else
		return $string;
}
?>