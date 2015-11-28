<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
	 * @return \Zend\Filter\FilterInterface
	 */
	public static function get( $filter )
	{
		if ( $filter instanceof \Zend\Filter\FilterInterface ) {
			return $filter;
		}

		switch( $filter )
		{
		case 'alpha':
			return new Zend\I18n\Filter\Alpha;
		case 'alnum':
			return new Zend\I18n\Filter\Alnum;
		case 'digits':
			return new Zend\Filter\Digits;
		case 'int':
			return new Zend\Filter\ToInt;
		case 'isodate':
			return new TikiFilter_IsoDate;
		case 'isodatetime':
			return new TikiFilter_IsoDate('Y-m-d H:i:s');
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
			return new Zend\Filter\StripTags;
		case 'word':
			return new TikiFilter_Word;
		case 'xss':
			return new TikiFilter_PreventXss;
		case 'purifier':
			return new TikiFilter_HtmlPurifier('temp/cache');
		case 'wikicontent':
			return new TikiFilter_WikiContent;
		case 'rawhtml_unsafe':
		case 'none':
			return new TikiFilter_RawUnsafe;
		case 'lang':
			return new Zend\Filter\PregReplace('/^.*([a-z]{2})(\-[a-z]{2}).*$/', '$1$2');
		case 'imgsize':
			return new Zend\Filter\PregReplace('/^.*(\d+)\s*(%?).*$/', '$1$2');
		case 'attribute_type':
			return new TikiFilter_AttributeType;
		default:
			trigger_error('Filter not found: ' . $filter, E_USER_WARNING);
			return new TikiFilter_PreventXss;
		}
	}
}
