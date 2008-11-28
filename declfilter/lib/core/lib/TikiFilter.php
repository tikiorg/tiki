<?php

class TikiFilter
{
	public static function get( $name )
	{
		switch( $name )
		{
		case 'alpha':
			require_once 'Zend/Filter/Alpha.php';
			return new Zend_Filter_Alpha;
		case 'alnum':
			require_once 'Zend/Filter/Alnum.php';
			return new Zend_Filter_Alnum;
		case 'digits':
			require_once 'Zend/Filter/Digits.php';
			return new Zend_Filter_Digits;
		case 'username':
		case 'groupname':
			// Use striptags
		case 'striptags':
			require_once 'Zend/Filter/StripTags.php';
			return new Zend_Filter_StripTags;
		case 'word':
			require_once 'TikiFilter/Word.php';
			return new TikiFilter_Word;
		case 'xss':
			require_once 'TikiFilter/PreventXss.php';
			return new TikiFilter_PreventXss;
		default:
			trigger_error( 'Filter not found: ' . $name, E_USER_WARNING );
			require_once 'TikiFilter/PreventXss.php';
			return new TikiFilter_PreventXss;
		}
	}
}

?>
