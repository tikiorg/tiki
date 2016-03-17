<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Categorylist.php 37848 2011-10-01 18:18:38Z changi67 $

class Search_Formatter_ValueFormatter_Imagegrabber extends Search_Formatter_ValueFormatter_Abstract
{
	
	private $max;
	private $height;
	private $width;
	private $smartcrop;	
	
	function __construct($arguments)
	{
		if (isset($arguments['max'])) {
			$this->max = $arguments['max'];
		}
		
		if (isset($arguments['height'])) {
			$this->height = $arguments['height'];
		}		

		if (isset($arguments['width'])) {
			$this->width = $arguments['width'];
		}

		if (isset($arguments['smartcrop'])) {
			$this->smartcrop = $arguments['smartcrop'];
		}
	}

    function render($name, $value, array $entry)
	{
	$pattern = '/\{img [^}]*(fileId="|dl)([0-9]+)"?[^}]*\}/';
	preg_match_all($pattern, $value, $entry);
	$extract = $entry[2]; 

	$output = '';
	foreach($extract as $key => $val)
	{
		if($key < $this->max)
		{
		$fileId = $val; 
		$query = 'fileId='.$fileId.'&display='.$name;
		if ($this->height) {
			$query .= '&y='.$this->height;
		}
		if ($this->width) {
			$query .= '&x='.$this->width;
		}
		if ($this->height && $this->width && $this->smartcrop == 'y') {
			$query .= '&smartcrop=y';
		}
		$output .= '<img src=tiki-download_file.php?' . $query . '></img>';
		}
	}

	return	'{HTML()}' . $output . '{HTML}';
	
	}
}
