<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for WebService
 * 
 * Letter key: ~W~
 *
 */
class Tracker_Field_WebService extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'W' => array(
				'name' => tr('Webservice'),
				'description' => tr('Displays the result of a registered webservice call.'),
				'readonly' => true,
				'help' => 'Webservice+tracker+field',				
				'prefs' => array('trackerfield_webservice', 'feature_webservices'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'service' => array(
						'name' => tr('Service Name'),
						'description' => tr('Webservice name as registered in Tiki.'),
						'filter' => 'word',
						'legacy_index' => 0,
					),
					'template' => array(
						'name' => tr('Template Name'),
						'description' => tr('Template name to use for rendering as registered with the webservice.'),
						'filter' => 'word',
						'legacy_index' => 1,
					),
					'params' => array(
						'name' => tr('Parameters'),
						'description' => tr('URL-encoded list of parameters to send to the webservice. %field_name% can be used in the string to be replaced with the values in the tracker item by field permName, Id or Name.'),
						'filter' => 'url',
						'legacy_index' => 2,
					),
					'cacheSeconds' => array(
						'name' => tr('Cache time'),
						'description' => tr('Time in seconds to cache the result for before trying again.'),
						'filter' => 'digits',
					),
				),
			),
		);
	}

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

		if (!$this->getOption('service') || !$this->getOption('template')) {
			return false;
		}

		require_once 'lib/webservicelib.php';

		if (!($webservice = Tiki_Webservice::getService($this->getOption('service')))  ||
			!($template = $webservice->getTemplate($this->getOption('template'))) ) {
				return false;
		}

		$oldValue = $this->getValue();
		if (is_string($oldValue)) {
			$oldData = json_decode($oldValue, true);
		} else {
			$oldData = [];
		}
		$cacheSeconds = $this->getOption('cacheSeconds');
		$lastRefreshed = empty($oldData) ? 0 : strtotime($oldData['tiki_updated']);
		$itemId = 0;	// itemId once saved after updating data

		if (! $cacheSeconds || TikiLib::lib('tiki')->now > $lastRefreshed + $cacheSeconds) {
			$ws_params = array();
			$definition = $this->getTrackerDefinition();

			if ($this->getOption('params')) {
				parse_str($this->getOption('params'), $ws_params);
				foreach ($ws_params as $ws_param_name => &$ws_param_value) {
					if (preg_match('/(.*)%(.*)%(.*)/', $ws_param_value, $matches)) {
						$ws_param_field_name = $matches[2];

						$field = $definition->getField($ws_param_field_name);
						if (!$field) {
							$field = $definition->getFieldFromName($ws_param_field_name);
						}
						if ($field) {
							$itemData = $this->getItemData();

							if (isset($itemData[$field['fieldId']])) {
								$value = TikiLib::lib('trk')->get_field_value($field, $itemData);
							} else {
								$itemUser = '';

								if (empty($itemData['itemId'])) {
									$itemData['itemId'] = $_REQUEST['itemId'];	// when editing an item the itemId doesn't seem to be available?
								}

								$value = TikiLib::lib('trk')->get_item_fields(
									$definition->getConfiguration('trackerId'),
									$itemData['itemId'],
									[$field],
									$itemUser
								);
								$value = isset($value[0]['value']) ? $value[0]['value'] : '';
							}
							$ws_params[$ws_param_name] = preg_replace('/%' . $ws_param_field_name . '%/', $value, $ws_param_value);
						}
					}
				}
			}

			$response = $webservice->performRequest($ws_params);

			$response->data['tiki_updated'] = gmdate('c');

			if ((empty($context['search_render']) || $context['search_render'] !== 'y') && ($response->data['status'] === 'OK' || $response->data['hasErrors'] === false)) {
				$thisField = $definition->getField($this->getConfiguration('fieldId'));
				$thisField['value'] = json_encode($response->data);

				if ($thisField['value'] != $oldValue) {
					$itemId = TikiLib::lib('trk')->replace_item(
						$definition->getConfiguration('trackerId'),
						empty($this->getItemId()) ? $_REQUEST['itemId'] : $this->getItemId(),
						['data' => [$thisField]]
					);
					if (!$itemId) {
						TikiLib::lib('errorreport')->report(tr('Error updating Webservice field %0', $this->getConfiguration('permName')));
						// try and restore previous data
						$response->data = json_decode($this->getValue());
					}
				}
			}
		}
		if (! $itemId) {
			$response = OIntegrate_Response::create($oldData, false);
			unlink($template->getTemplateFile());
			$template = $webservice->getTemplate($this->getOption('template'));
		}


		$output = $template->render($response, 'html');

		return $output;
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();
		$value = json_decode($this->getValue(), true);

		if (isset($value['result'])) {
			$value = $value['result'];
		} else if (isset($value['data'])) {
			$value = $value['data'];
		} else {
			$value = [];
		}

		return array(
			$baseKey => $typeFactory->multivalue(array_filter($value, 'is_string')),
			"{$baseKey}_text" => $typeFactory->plaintext(		// ignore nested arrays and remove html for plain text
					strip_tags(
							implode(' ', array_filter($value, 'is_string'))
					)
			),
			"{$baseKey}_json" => $typeFactory->plaintext(
					json_encode($value)
			),
		);
	}

}
