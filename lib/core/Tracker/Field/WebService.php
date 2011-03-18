<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * Handler class for WebService
 * 
 * Letter key: ~W~
 *
 */
class Tracker_Field_WebService extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{	
		return array();
	}

	function renderInput($context = array())
	{
		$this->renderOutput($context);	// read only
	}

	function renderOutput($context = array())
	{
			
		if (!$this->getOption(0) || !$this->getOption(1)) {
			return false;
		}
	
		require_once 'lib/webservicelib.php';

		if (!($webservice = Tiki_Webservice::getService($this->getOption(0)))  ||
			!($template = $webservice->getTemplate($this->getOption(1))) ) {
				return false;
		}

		$ws_params = array();
		
		if ( $this->getOption(2) ) {
			parse_str($this->getOption(2), $ws_params);
			foreach ($ws_params as $ws_param_name => &$ws_param_value) {
				if (preg_match('/(.*)%(.*)%(.*)/', $ws_param_value, $matches)) {
					$ws_param_field_name = $matches[2]; 
				}
				$field = $this->getTrackerDefinition()->getFieldFromName($ws_param_field_name);
				if ($field) {
					$value = TikiLib::lib('trk')->get_field_value($field, $this->getItemData());
					$ws_param_value = preg_replace('/%'. $ws_param_field_name .'%/', $value, $ws_param_value);
				}
			}
		}

		$response = $webservice->performRequest( $ws_params );
		$output = $template->render( $response, 'html' );
					
		return $output;
	}
}
