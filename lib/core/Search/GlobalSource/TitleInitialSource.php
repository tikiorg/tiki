<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_TitleInitialSource implements Search_GlobalSource_Interface, Search_FacetProvider_Interface
{
	private $substr;
	private $strtoupper;

	function __construct()
	{
		if (function_exists('mb_substr')) {
			$this->substr = 'mb_substr';
			$this->strtoupper = 'mb_strtoupper';
		} else {
			$this->substr = 'substr';
			$this->strtoupper = 'mb_strtoupper';
		}
	}

	function getFacets()
	{
		return array(
			Search_Query_Facet_Term::fromField('title_initial')
				->setLabel(tr('Letter')),
			Search_Query_Facet_Term::fromField('title_firstword')
				->setLabel(tr('First word')),
		);
	}

	function getProvidedFields()
	{
		return array('title_initial', 'title_firstword');
	}

	function getGlobalFields()
	{
		return array();
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$title = empty($data['title']) ? null : $data['title']->getValue();
		$title = empty($title) ? '0' : $title;

		$substr = $this->substr;
		$strtoupper = $this->strtoupper;

		$first = $substr($title, 0, 1);
		$first = TikiLib::take_away_accent($first);
		$letter = $strtoupper($first);
		
		$firstword = preg_replace('/^([^:\s]+).*$/u', '$1', $title);
		return array(
			'title_initial' => $typeFactory->identifier($letter),
			'title_firstword' => $typeFactory->identifier($firstword),
		);
	}


}

