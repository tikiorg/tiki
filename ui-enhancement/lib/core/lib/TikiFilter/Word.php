<?php

require_once 'Zend/Filter/PregReplace.php';

class TikiFilter_Word extends Zend_Filter_PregReplace
{
	function __construct()
	{
		parent::__construct( '/\W+/', '' );
	}
}
