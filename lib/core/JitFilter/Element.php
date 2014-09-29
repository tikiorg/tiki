<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class JitFilter_Element
 * @method Zend_Filter_Alpha alpha
 * @method Zend_Filter_Alnum alnum
 * @method Zend_Filter_Digits digits
 * @method Zend_Filter_Int int
 * @method Zend_Filter_StripTags username
 * @method Zend_Filter_StripTags groupname
 * @method Zend_Filter_StripTags pagename
 * @method Zend_Filter_StripTags topicname
 * @method Zend_Filter_StripTags themename
 * @method Zend_Filter_StripTags email
 * @method Zend_Filter_StripTags url
 * @method Zend_Filter_StripTags text
 * @method Zend_Filter_StripTags date
 * @method Zend_Filter_StripTags time
 * @method Zend_Filter_StripTags datetime
 * @method Zend_Filter_StripTags striptags
 * @method TikiFilter_Word word
 * @method TikiFilter_PreventXss xss
 * @method TikiFilter_HtmlPurifier purifier
 * @method TikiFilter_WikiContent wikicontent
 * @method TikiFilter_RawUnsafe rawhtml_unsafe
 * @method TikiFilter_RawUnsafe none
 * @method string lang
 * @method string imgsize
 * @method TikiFilter_AttributeType attribute_type
 * @method bool bool
 */
class JitFilter_Element
{
	private $value;

	function __construct( $value )
	{
		$this->value = $value;
	}

	function filter( $filter )
	{
		$filter = TikiFilter::get($filter);

		return $filter->filter($this->value);
	}

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    function __call( $name, $arguments )
	{
		return $this->filter($name);
	}
}
