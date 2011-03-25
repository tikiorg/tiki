<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Image
 * 
 * Letter key: ~i~
 *
 */
class Tracker_field_Image extends Tracker_Field_File
{
	function getFieldData(array $requestData = array())
	{
		global $prefs, $smarty;
		
		$ins_id = $this->getInsertId();

		if (!empty($prefs['gal_match_regex'])) {
			if (!preg_match('/' . $prefs['gal_match_regex'] . '/', $_FILES[$ins_id]['name'], $reqs)) {
				$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
				$smarty->display("error.tpl");
				die;
			}
		}
		if (!empty($prefs['gal_nmatch_regex'])) {
			if (preg_match('/' . $prefs['gal_nmatch_regex'] . '/', $_FILES[$ins_id]['name'], $reqs)) {
				$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
				$smarty->display("error.tpl");
				die;
			}
		}
		if (!empty($requestData)) {
			return parent::getFieldData($requestData);
		} else {
			return array( 'value' => $this->getValue() );
		}
	}

	function renderInnerOutput( $context )
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');

		$val = $this->getValue();
		$list_mode = $context['list_mode'];
		if ($list_mode == 'csv') {
			return $val; // return the filename
		}
		$pre = '';
		if ( !empty($val) && file_exists($val) ) {
			$params['file'] = $val;
			$shadowtype = $this->getOption(5);
			if ($prefs['feature_shadowbox'] == 'y' && !empty($shadowtype)) {
				switch ($shadowtype) {
				case 'item':
					$rel = '['.$this->getItemId().']';
					break;
				case 'individual':
					$rel = '';
					break;
				default:
					$rel = '['.$this->getConfiguration('fieldId').']';
					break;
				}
				$pre = "<a href=\"$val\" rel=\"shadowbox$rel;type=img\">";
			}
			if ($list_mode != 'n') {
				if ($this->getOption(0))
					$params['width'] = $this->getOption(0);
				if ($this->getOption(1))
					$params['height'] = $this->getOption(1);
			} else {
				if ($this->getOption(2))
					$params['width'] = $this->getOption(2);
				if ($this->getOption(3))
					$params['height'] = $this->getOption(3);
			}
		} else {
			$params['file'] = 'img/icons/na_pict.gif';
			$params['alt'] = 'n/a';
		}
		require_once $smarty->_get_plugin_filepath('function', 'html_image');
		$ret = smarty_function_html_image($params, $smarty);
		if (!empty($pre))
			$ret = $pre.$ret.'</a>';
		return $ret;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/image.tpl', $context);
	}
}

