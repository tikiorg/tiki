<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


/**
 * Foundation of all trackerfields. Each trackerfield defines its own class that derives from this one and also
 * hast to implement Tracker_Field_Interface, Tracker_Field_Indexable.
 *
 */
abstract class Tracker_Field_Abstract implements Tracker_Field_Interface, Tracker_Field_Indexable
{
	/**
	 * @var string - ???
	 */
	private $baseKeyPrefix = '';

	/**
	 * @var array - the field definition
	 */
	private $definition;

	/**
	 * @var handle ??? -
	 */
	private $options;

	/**
	 * @var array - complex data about an item. including itemId, trackerId and values of fields by fieldId=>value pairs
	 *
	 */
	private $itemData;

	/**
	 * @var array - trackerdefinition
	 *
	 */
	private $trackerDefinition;


	/**
	 * Initialize the instance with field- and trackerdefinition and item value(s)
	 * @param array $fieldInfo - the field definition
	 * @param array $itemData - itemId/value pair(s)
	 * @param array $trackerDefinition - the tracker definition.
	 *
	 */
	function __construct($fieldInfo, $itemData, $trackerDefinition)
	{
		$this->options = Tracker_Options::fromSerialized($fieldInfo['options'], $fieldInfo);

		if (! isset($fieldInfo['options_array'])) {
			$fieldInfo['options_array'] = $this->options->buildOptionsArray();
		}

		$this->definition = $fieldInfo;
		$this->itemData = $itemData;
		$this->trackerDefinition = $trackerDefinition;
	}


	/**
	 * Not implemented here. Its upto to the extending class.
	 * @param array $context - ???
	 * @return string $renderedContent depending on the $context
	 */
	public function renderInput($context = array())
	{
		return 'Not implemented';
	}


	/**
	 * Render output for this field.
	 * IMPORTANT: This method uses the following $_GET args directly: 'page'
	 * @TODO fixit so it does not directly access the $_GET array. Better pass it as a param.
	 * @param array $context -keys:
	 * <pre>
	 * $context = array(
	 * 		// required
	 * 		// optional
	 * 		'url' => 'sefurl', // other values 'offset', 'tr_offset'
	 *  	'reloff' => true, // checked only if set
	 *  	'showpopup' => 'y', // wether to show that value in a mouseover popup
	 *  	'showlinks' => 'n' // NO check for 'y' but 'n'
	 *  	'list_mode' => 'csv' //
	 * );
	 * </pre>
	 *
	 * @return string $renderedContent depending on the $context
	 */
	public function renderOutput($context = array())
	{
		// only if this field is marked as link and the is no request for a csv export
		// create the link and if required the mouseover popup
		if ($this->isLink($context)) {
			$itemId = $this->getItemId();
			$query = $_GET;
			unset($query['trackerId']);
			if (isset($query['page'])) {
				$query['from'] = $query['page'];
				unset($query['page']);
			}


			$classList = array('tablename');
			$metadata = TikiLib::lib('object')->get_metadata('trackeritem', $itemId, $classList);

			require_once ('lib/smarty_tiki/modifier.sefurl.php');
			$href = smarty_modifier_sefurl($itemId, 'trackeritem');
			$href .= (strpos($href, '?') === false) ? '?' : '&';
			$href .= http_build_query($query, '', '&');
			$href = rtrim($href, '?&');

			$arguments = array(
				'class' => implode(' ', $classList),
				'href' => $href,
			);
			if (!empty($context['url'])) {
				if ($context['url'] == 'sefurl') {
					$context['url'] = 'item' . $itemId;
				} elseif (strpos($context['url'], 'itemId') !== false) {
					$context['url'] = preg_replace('/([&|\?])itemId=?[^&]*/', '\\1itemId=' . $itemId, $context['url']);
				} elseif (isset($context['reloff']) && strpos($context['url'], 'offset') !== false) {
					$smarty = TikiLib::lib('smarty');
					$context['url'] = preg_replace('/([&|\?])tr_offset=?[^&]*/', '\\1tr_offset' . $smarty->tpl_vars['iTRACKERLIST']
						. '=' . $context['reloff'], $context['url']);
				}
				$arguments['href'] = $context['url'];
			}

			$pre = '<a';
			foreach ($arguments as $key => $value) {
				$pre .= ' ' . $key . '="' . htmlentities($value, ENT_QUOTES, 'UTF-8') . '"';
			}

			// add the html / js for the mouseover popup
			if (isset($context['showpopup']) && $context['showpopup'] == 'y') {
				// check if trackerplugin has set popup fields using the popup parameter
				$pluginPopupFields = isset($context['popupfields']) ? $context['popupfields'] : null;
				$popup = $this->renderPopup($pluginPopupFields);

				if ($popup) {
					$popup = preg_replace('/<\!--.*?-->/', '', $popup);	// remove comments added by log_tpl
					$popup = preg_replace('/\s+/', ' ', $popup);
					$pre .= " $popup";
				}
			}

			$pre .= $metadata;
			$pre .= '>';
			$post = '</a>';

			return $pre . $this->renderInnerOutput($context) . $post;
		} else {
			// no link, no mouseover popup. Note: can also be a part of a csv request
			return $this->renderInnerOutput($context);
		}
	}

	function watchCompare($old, $new)
	{
		$name = $this->getConfiguration('name');
		$is_visible = $this->getConfiguration('isHidden', 'n') == 'n';

		if (! $is_visible) {
			return;
		}

		if ($old) {
			// split old value by lines
			$lines = explode("\n", $old);
			// mark every old value line with standard email reply character
			$old_value_lines = '';
			foreach ($lines as $line) {
				$old_value_lines .= '> '.$line;
			}
			return "[-[$name]-]:\n--[Old]--:\n$old_value_lines\n\n*-[New]-*:\n$new";
		} else {
			return "[-[$name]-]:\n$new";
		}
	}


	private function isLink($context = array())
	{
		$type = $this->getConfiguration('type');
		if ($type == 'x') {
			return false;
		}

		if ($this->getConfiguration('showlinks', 'y') == 'n') {
			return false;
		}

		if (isset($context['showlinks']) && $context['showlinks'] == 'n') {
			return false;
		}

		if (isset($context['list_mode']) && $context['list_mode'] == 'csv') {
			return false;
		}

		$itemId = $this->getItemId();
		if (empty($itemId)) {
			return false;
		}
		$itemObject = Tracker_Item::fromInfo($this->itemData);

		$status = $this->getData('status');

		if ($this->getConfiguration('isMain', 'n') == 'y'
			&& ($itemObject->canView()	|| $itemObject->getPerm('comment_tracker_items'))
			) {
			return (bool) $this->getItemId();
		}

		return false;
	}


	/**
	 * Create the html/js to show a popupwindow on mouseover when the trackeritem has a field with link enabled.
	 * The formatting is done via smarty based on 'trackeroutput/popup.tpl'
	 * @param array $pluginPopupFields - array with fieldids set by trackerlist plugin. if not set the tracker defaults will be used.
	 * @return NULL|string $popupHtml
	 */
	private function renderPopup($pluginPopupFields = null)
	{
		// support of trackerlist plugin popup field - if popup is set and has fields - show the fields as defined and in their order
		// if parameter popup is set but without fields show no popup
		// note: the popup template code in wikiplugin_trackerlist.tpl does not seem to be used at all - only the flag $showpopup
		if ($pluginPopupFields && is_array($pluginPopupFields)) {
			$fields = $pluginPopupFields;
		} else {
			// plugin trackerlist not involved
			$fields = $this->trackerDefinition->getPopupFields();
		}

		if (empty($fields)) {
			return null;
		}
				
		$factory = $this->trackerDefinition->getFieldFactory();

		$popupFields = array();

		foreach ($fields as $id) {
			$field = $this->trackerDefinition->getField($id);

			if (!isset($this->itemData[$field['fieldId']])) {
				foreach ($this->itemData['field_values'] as $fieldVal) {
					if ($fieldVal['fieldId'] == $id) {
						if (isset($fieldVal['value'])) {
							$this->itemData[$field['fieldId']] = $fieldVal['value'];
						}
					}
				}
			}
			$handler = $factory->getHandler($field, $this->itemData);

			if ($handler) {
				$field = array_merge($field, $handler->getFieldData());
				$popupFields[] = $field;
			}
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->assign('popupFields', $popupFields);
		$smarty->assign('popupItem', $this->itemData);
		return trim($smarty->fetch('trackeroutput/popup.tpl'));
	}

	/**
	 * return the html for the output of a field without link, prepend...
	 * @param array $context - key 'list_mode' defines wether to output for a list or a simple value
	 * @return string $html
	 */
	protected function renderInnerOutput($context = array())
	{
		$value = $this->getConfiguration('value');
		$pvalue = $this->getConfiguration('pvalue', $value);

		if (isset($context['list_mode']) && $context['list_mode'] === 'csv') {
			$default = array('CR'=>'%%%', 'delimitorL'=>'"', 'delimitorR'=>'"');
			$context = array_merge($default, $context);
			$value = str_replace(array("\r\n", "\n", '<br />', $context['delimitorL'], $context['delimitorR']), array($context['CR'], $context['CR'], $context['CR'], $context['delimitorL'].$context['delimitorL'], $context['delimitorR'].$context['delimitorR']), $value);
			return $value;
		} else {
			return $pvalue;
		}
	}

	/**
	 * Return the HTML id/name of input tag for this
	 * field in the item form
	 *
	 * @return string
	 */
	protected function getInsertId()
	{
		return 'ins_' . $this->definition['fieldId'];
	}

	protected function getFilterId()
	{
		return 'filter_' . $this->definition['fieldId'];
	}

	/**
	 * Gets data from the field's configuration
	 *
	 * i.e. from the field definition in the database plus what is returned by the field's getFieldData() function
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	protected function getConfiguration($key, $default = false)
	{
		return isset($this->definition[$key]) ? $this->definition[$key] : $default;
	}

	/**
	 * Return the value for this item field. Depending on fieldtype that could be the itemId of a linked field.
	 * Value is looked for in:
	 * $this->itemData[fieldNumber]
	 * $this->definition['value']
	 * $this->itemData['fields'][permName]
	 * @param mixed $default the field value used if none is set
	 * @return mixed field value
	 */
	protected function getValue($default = '')
	{
		$key = $this->getConfiguration('fieldId');

		if (isset($this->itemData[$key])) {
			$value = $this->itemData[$key];
		} else if (isset($this->definition['value'])) {
			$value = $this->definition['value'];
		} else if (isset($this->itemData['fields'][$this->getConfiguration('permName')])) {
			$value = $this->itemData['fields'][$this->getConfiguration('permName')];
		} else {
			$value = null;
		}

		return $value === null ? $default : $value;
	}

	protected function getItemId()
	{
		return $this->getData('itemId');
	}

	protected function getData($key, $default = false)
	{
		return isset($this->itemData[$key]) ? $this->itemData[$key] : $default;
	}

	protected function getItemField($permName)
	{
		$field = $this->trackerDefinition->getFieldFromPermName($permName);

		if ($field) {
			$id = $field['fieldId'];

			return $this->getData($id);
		}
	}

	/**
	 * Return option from the options array.
	 * For the list of options for a particular field check its getTypes() method.
	 * Note: This function should be public, as long as certain low-level trackerlib functions need to be accessed directly.
	 * Otherwise one would be forced to get the options from fields like this: $myField['options_array'][0] ...  
	 * @param int $number | string $key.  depending on type: based on the numeric array position, or by name.
	 * @param mixed $default - defaultValue to return if nothing found
	 * @return mixed
	 */
	public function getOption($key, $default = false)
	{
		if (is_numeric($key)) {
			return $this->options->getParamFromIndex($key, $default);
		} else {
			return $this->options->getParam($key, $default);
		}
	}

	/**
	 * Get the tracker definition object
	 *
	 * @return \Tracker_Definition
	 */
	protected function getTrackerDefinition()
	{
		return $this->trackerDefinition;
	}

	/**
	 * Get the item's data
	 *
	 * @return array
	 */
	protected function getItemData()
	{
		return $this->itemData;
	}

	protected function renderTemplate($file, $context = array(), $data = array())
	{
		$smarty = TikiLib::lib('smarty');

		//ensure value is set, because it may not always come from definition
		if (!isset($this->definition['value'])) {
			$this->definition['value'] = $this->getValue();
		}

		$smarty->assign('field', $this->definition);
		$smarty->assign('context', $context);
		$smarty->assign('item', $this->getItemData());
		$smarty->assign('data', $data);

		return $smarty->fetch($file, $file);
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();
		return array(
			$baseKey => $typeFactory->sortable($this->getValue()),
		);
	}

	function getProvidedFields()
	{
		$baseKey = $this->getBaseKey();
		return array($baseKey);
	}

	function getGlobalFields()
	{
		$baseKey = $this->getBaseKey();
		return array($baseKey => true);
	}

	function getBaseKey()
	{
		global $prefs;
		$indexKey = $prefs['unified_trackerfield_keys'];
		return 'tracker_field_' . $this->baseKeyPrefix . $this->getConfiguration($indexKey);
	}

	function setBaseKeyPrefix($prefix)
	{
		$this->baseKeyPrefix = $prefix;
	}
}


