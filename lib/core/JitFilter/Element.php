<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class JitFilter_Element
 * @method Zend\I18n\Filter\Alpha|TikiFilter_Alpha alpha
 * @method Zend\I18n\Filter\Alnum|TikiFilter_Alnum alnum
 * @method Zend\Filter\Digits digits
 * @method Zend\Filter\ToInt int
 * @method Zend\Filter\StripTags username
 * @method Zend\Filter\StripTags groupname
 * @method Zend\Filter\StripTags pagename
 * @method Zend\Filter\StripTags topicname
 * @method Zend\Filter\StripTags themename
 * @method Zend\Filter\StripTags email
 * @method Zend\Filter\StripTags url
 * @method Zend\Filter\StripTags text
 * @method Zend\Filter\StripTags date
 * @method Zend\Filter\StripTags time
 * @method Zend\Filter\StripTags datetime
 * @method Zend\Filter\StripTags striptags
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
