<?php

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
		return array(
			'value' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: $this->getValue(),
		);
	}

	function renderInnerOutput()
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');

		$val = $this->getValue();
		$list_mode = $smarty->get_template_vars('list_mode'); // to be fixed
		if ($list_mode == 'csv') {
			return $val; // return the filename
		}
		$pre = '';
		if (!empty($val)) {
			$params['file'] = $val;
			if ($list_mode != 'n') {
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

