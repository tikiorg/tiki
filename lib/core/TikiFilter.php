<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'Zend/Filter/Interface.php';

class TikiFilter
{
	/**
	 * Provides a filter instance based on the input. Either a filter
	 * can be passed or a name.
	 * 
	 * @param mixed
	 * @return Zend_Filter_Interface
	 */
	public static function get( $filter )
	{
		if( $filter instanceof Zend_Filter_Interface ) {
			return $filter;
		}

		switch( $filter )
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
		case 'int':
			require_once 'Zend/Filter/Int.php';
			return new Zend_Filter_Int;
		case 'username':
		case 'groupname':
		case 'pagename':
		case 'topicname':
		case 'themename':
		case 'email':
		case 'url':
		case 'text':
		case 'date':
		case 'time':
		case 'datetime':
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
		case 'purifier':
			require_once 'TikiFilter/HtmlPurifier.php';
			return new TikiFilter_HtmlPurifier( 'temp/cache' );
		case 'wikicontent':
		case 'rawhtml_unsafe':
		case 'none':
			require_once 'TikiFilter/RawUnsafe.php';
			return new TikiFilter_RawUnsafe;
		case 'lang':
			require_once 'Zend/Filter/PregReplace.php';
			return new Zend_Filter_PregReplace( '/^.*([a-z]{2})(\-[a-z]{2}).*$/', '$1$2' );
		case 'imgsize':
			require_once 'Zend/Filter/PregReplace.php';
			return new Zend_Filter_PregReplace( '/^.*(\d+)\s*(%?).*$/', '$1$2' );
		case 'attribute_type':
			require_once 'TikiFilter/AttributeType.php';
			return new TikiFilter_AttributeType;
		default:
			trigger_error( 'Filter not found: ' . $filter, E_USER_WARNING );
			require_once 'TikiFilter/PreventXss.php';
			return new TikiFilter_PreventXss;
		}
	}
}
