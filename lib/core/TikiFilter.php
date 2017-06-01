<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiFilter
{
	/**
	 * Provides a filter instance based on the input. Either a filter
	 * can be passed or a shortcut name.
	 * 
	 * @param \Zend\Filter\FilterInterface|string $filter		A filter shortcut name, or the filter name its self.
	 * @return \Zend\Filter\FilterInterface 					The filter to apply.
	 *
	 * @link https://dev.tiki.org/Filtering+Best+Practices
	 */
	public static function get( $filter )
	{
		if ( $filter instanceof \Zend\Filter\FilterInterface ) {
			return $filter;
		}

		switch( $filter )
		{
			case 'alpha':
				// Removes all but alphabetic characters. Unicode support.
				return new TikiFilter_Alpha;
			case 'alphaspace':
				// Removes all but alphabetic characters and spaces
				return new TikiFilter_Alpha(true);
			case 'word':
				// Strips everything but digit and alpha and underscore characters. Unicode support. eg. "g.4h&#Δ δ🍗_🍘コン" evaluates to "ghΔδ_コン"
				return new Zend\Filter\PregReplace('/\W+/', '');
			case 'wordspace':
				// Words and spaces only (no trimming)
				return new Zend\Filter\PregReplace('/[^\p{L}\p{M}\p{N}_\p{Zs}]*/u', '');
			case 'alnum':
				// Only alphabetic characters and digits. All other characters are suppressed. Unicode support.
				return new TikiFilter_Alnum;
			case 'alnumspace':
				// Only alphabetic characters, digits and spaces. All other characters are suppressed. Unicode support
				return new TikiFilter_Alnum(true);
			case 'alnumdash':
				// Removes everything except alphabetic characters, digits, dashes and underscores. Could be used for
				// class names, sortmode values, etc.
				return new Zend\Filter\PregReplace('/[^\p{L}\p{N}\p{Pc}\p{Pd}]*/', '');
			case 'digits':
				// Removes everything except digits eg. ' 12345 to 67890' returns '1234567890', while '-5' returns '5'
				// return type: (string)
			return new Zend\Filter\Digits;
			case 'digitscolons':
				// Removes everything except digits and colons, e.g., for colon-separated ID numbers.
				// Only characters matched, not patterns - eg 'x75::xx44:' will return '75::44:'
				return new Zend\Filter\PregReplace('/[^\p{N}:]*/', '');
			case 'digitscommas':
				// Removes everything except digits and commas, e.g., for comma-separated ID numbers.
				// Only characters matched, not patterns - eg 'x75,,xx44,' will return 'x75,,44,'
				return new Zend\Filter\PregReplace('/[^\p{N},]*/', '');
			case 'digitspipes':
				// Removes everything except digits and pipes, e.g., for pipe-separated ID numbers.
				// Only characters matched, not patterns - eg 'x75||xx44|' will return '75||44|'
				return new Zend\Filter\PregReplace('/[^\p{N}\|]*/', '');
			case 'int':
				// Transforms a sclar phrase into an integer. eg. '-4 is less than 0' returns -4, while '' returns 0
				// return type: (int)
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
			case 'striptags':
				// Strips XML and HTML tags
				return new Zend\Filter\StripTags;
			case 'bool':
				// False upon:	false, 0, '0', 0.0, '', array(), null, 'false', 'no', 'n' and php casting equivalent to false.
				// True upon:	Everything else returns true. Case insensitive evaluation.
			return new Zend\Filter\Boolean([
					'type'			=> Zend\Filter\Boolean::TYPE_ALL,
					'translations'	=> ['n' => false, 'N' => false]
					]);
			case 'relativeurl':
				// If formatted as a absolute url, will return the relative portion, also applies striptags
				return new TikiFilter_RelativeURL;
			case 'xss':
				// Leave everything except for potentially malicious HTML
				return new TikiFilter_PreventXss;
			case 'purifier':
				// Strips non-valid HTML and potentially malicious HTML
				return new TikiFilter_HtmlPurifier('temp/cache');
			case 'wikicontent':
				return new TikiFilter_WikiContent;
			case 'rawhtml_unsafe':
			case 'none':
				return new TikiFilter_RawUnsafe;
			case 'lang':
				// Allows values for languages (such as 'en') available on the site
				return new TikiFilter_Lang;
			case 'imgsize':
				// Allows digits optionally followed by a space and/or certain size units
				return new TikiFilter_PregFilter(
					'/^(\p{N}+)\p{Zs}?(%|cm|em|ex|in|mm|pc|pt|px|vh|vw|vmin)?$/u',
					'$1$2'
				);
			case 'attribute_type':
				return new TikiFilter_AttributeType;
			default:
				trigger_error('Filter not found: ' . $filter, E_USER_WARNING);
				return new TikiFilter_PreventXss;
		}
	}
}
