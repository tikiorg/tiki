<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
			return new Zend_Filter_Alpha;
		case 'alnum':
			return new Zend_Filter_Alnum;
		case 'digits':
			return new Zend_Filter_Digits;
		case 'int':
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
			return new Zend_Filter_StripTags;
		case 'word':
			return new TikiFilter_Word;
		case 'xss':
			return new TikiFilter_PreventXss;
		case 'purifier':
			return new TikiFilter_HtmlPurifier( 'temp/cache' );
		case 'wikicontent':
		case 'rawhtml_unsafe':
		case 'none':
			return new TikiFilter_RawUnsafe;
		case 'lang':
			return new Zend_Filter_PregReplace( '/^.*([a-z]{2})(\-[a-z]{2}).*$/', '$1$2' );
		case 'imgsize':
			return new Zend_Filter_PregReplace( '/^.*(\d+)\s*(%?).*$/', '$1$2' );
		case 'attribute_type':
			return new TikiFilter_AttributeType;
		default:
			trigger_error( 'Filter not found: ' . $filter, E_USER_WARNING );
			return new TikiFilter_PreventXss;
		}
	}
}
