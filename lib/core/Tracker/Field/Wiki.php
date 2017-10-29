<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Wiki extends Tracker_Field_Text implements Tracker_Field_Exportable
{
	public static function getTypes()
	{
		global $prefs;
		if (isset($prefs['tracker_wikirelation_synctitle'])) {
			$tracker_wikirelation_synctitle = $prefs['tracker_wikirelation_synctitle'];
		} else {
			$tracker_wikirelation_synctitle = 'n';
		}
		return array(
			'wiki' => array(
				'name' => tr('Wiki Page'),
				'description' => tr('Embeds an associated wiki page'),
				'help' => 'Wiki page Tracker Field',
				'prefs' => array('trackerfield_wiki'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'fieldIdForPagename' => array(
						'name' => tr('Field that is used for Wiki Page Name'),
						'description' => tr('Field to get page name to create page name with.'),
						'filter' => 'int',
						'profile_reference' => 'tracker_field',
					),
					'namespace' => array(
						'name' => tr('Namespace for Wiki Page'),
						'description' => tr('The namespace to use for the wiki page to prevent page name clashes. See namespace feature for more information.'),
						'filter' => 'alpha',
						'options' => array(
							'default' => tr('Default (trackerfield<fieldId>)'),
							'none' => tr('No namespace'),
							'custom' => tr('Custom namespace'),
						),
						'default' => 'default',
					),
					'customnamespace' => array(
						'name' => tr('Custom Namespace'),
						'description' => tr('The custom namespace to use if the custom option is selected.'),
						'filter' => 'alpha',
					),
					'syncwikipagename' => array(
						'name' => tr('Rename Wiki Page when changed in tracker'),
						'description' => tr('Rename associated wiki page when the field that is used for Wiki Page Name is changed.'),
						'default' => $tracker_wikirelation_synctitle,
						'filter' => 'alpha',
                                                'options' => array(
                                                        'n' => tr('No'),
                                                        'y' => tr('Yes'),
                                                ),
                                        ),
					'syncwikipagedelete' => array(
                                                'name' => tr('Delete Wiki Page when tracker item is deleted'),
                                                'description' => tr('Delete associated wiki page when the tracker item is deleted.'),
                                                'default' => 'n',
                                                'filter' => 'alpha',
                                                'options' => array(
                                                        'n' => tr('No'),
                                                        'y' => tr('Yes'),
                                                ),
                                        ),
					'toolbars' => array(
						'name' => tr('Toolbars'),
						'description' => tr('Enable the toolbars as syntax helpers.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('Disable'),
							1 => tr('Enable'),
						),
						'default' => 1,
					),
					'width' => array(
						'name' => tr('Width'),
						'description' => tr('Size of the text area, in characters.'),
						'filter' => 'int',
					),
					'height' => array(
						'name' => tr('Height'),
						'description' => tr('Size of the text area, in lines.'),
						'filter' => 'int',
					),
					'max' => array(
						'name' => tr('Character Limit'),
						'description' => tr('Maximum number of characters to be stored.'),
						'filter' => 'int',
					),
					'wordmax' => array(
						'name' => tr('Word Count'),
						'description' => tr('Limit the length of the text, in number of words.'),
						'filter' => 'int',
					),
					'wysiwyg' => array(
						'name' => tr('Use WYSIWYG'),
						'description' => tr('Use a rich text editor instead of inputting plain text.'),
						'default' => 'n',
						'filter' => 'alpha',
						'options' => array(
							'n' => tr('No'),
							'y' => tr('Yes'),
						),
					),
					'samerow' => array(
						'name' => tr('Same Row'),
						'description' => tr('Display the field name and input on the same row.'),
						'deprecated' => false,
						'filter' => 'int',
						'default' => 1,
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
					'removeBadChars' => array(
						'name' => tr('Remove Bad Chars'),
						'description' => tr('Remove bad characters from the Wiki Page name.'),
						'default' => 'n',
						'filter' => 'alpha',
						'options' => array(
							'n' => tr('No'),
							'y' => tr('Yes'),
						),
					),
				),
			),
		);
	}

	/**
	 * @param $ins_fields_data
	 * @param int $itemId           set to itemId when importing
	 * @return bool|mixed|string
	 */
	function isValid($ins_fields_data, $itemId = 0)
	{
		global $prefs;

		$pagenameField = $this->getOption('fieldIdForPagename');
		$pagename = $this->cleanPageName($ins_fields_data[$pagenameField]['value']);
		if (! $itemId) {
			$itemId = $this->getItemId();
		}

		if (TikiLib::lib('trk')->check_field_value_exists($pagename, $pagenameField, $itemId)) {
			return tr('The page name provided already exists. Please choose another.');
		}

		if ($prefs['wiki_badchar_prevent'] == 'y' && TikiLib::lib('wiki')->contains_badchars($pagename)) {
			$bad_chars = TikiLib::lib('wiki')->get_badchars();
			return tr(
				'The page name specified "%0" contains unallowed characters. It will not be possible to save the page until those are removed: %1',
				$pagename,
				$bad_chars
			);
		}

		return true;
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		global $user, $prefs;

		$to_create_page = false;
		$page_data = '';
		$fieldId = $this->getConfiguration('fieldId');

		if ($this->getOption('wysiwyg') === 'y' && $prefs['wysiwyg_htmltowiki'] != 'y') {
			$is_html = true;
		} else {
			$is_html = false;
		}

		$page_name = $this->getValue();
		$insForPagenameField = 'ins_' . $this->getOption('fieldIdForPagename');

		if (! $page_name && ! empty($requestData['itemId']) && ! empty($requestData[$insForPagenameField])) {	// from tabular import replace
			$page_name = $this->getFullPageName($requestData[$insForPagenameField]);
			$itemId = $requestData['itemId'];
		} else {
			$itemId = $this->getItemId();
		}

		if ($page_name) {
			// There is already a wiki pagename set (the value of the field is the wiki page name)
			if (TikiLib::lib('tiki')->page_exists($page_name)) {
				// Get wiki page content
				$page_info = TikiLib::lib('tiki')->get_page_info($page_name);
				$page_data = $page_info['data'];
				if (!empty($requestData[$ins_id])) {
					// There is new page data provided
					if ($page_data != $requestData[$ins_id]) {
						// Update page data
						$edit_comment = 'Updated by Tracker Field ' . $fieldId;
						$short_name = $requestData[$insForPagenameField];
						$ins_fields_data[$this->getOption('fieldIdForPagename')]['value'] = $short_name;
						if ($this->isValid($ins_fields_data, $itemId) === true) {
							TikiLib::lib('tiki')->update_page($page_name, $requestData[$ins_id], $edit_comment, $user, TikiLib::lib('tiki')->get_ip_address(), '', 0, '', $is_html, null, null, $this->getOption('wysiwyg'));
						}
					}
				}
			} else {
				$to_create_page = true;
			}
		} elseif (!empty($requestData[$ins_id])) {
			// the field value is currently null and there is input, so would need to create page.
			if ($short_name = $requestData[$insForPagenameField]) {
				$page_name = $this->getFullPageName($short_name);
				if (!TikiLib::lib('tiki')->page_exists($page_name)) {
					$ins_fields_data[$this->getOption('fieldIdForPagename')]['value'] = $short_name;
					if ($this->isValid($ins_fields_data) === true) {
						$to_create_page = true;
					}
				} else {
					Feedback::error(tr('Page "%0" already exists. Not overwriting.', $page_name), 'session');
				}
			}
		}

		if ($to_create_page) {
			// Note we do not want to create blank pages, but if in the event a page that is already linked is deleted, a blank page will be created.
			if (!empty($requestData[$ins_id])) {
				$page_data = $requestData[$ins_id];
			}
			// re-clean the page name here incase it comes from legacy data, i.e. from a partial import
			$page_name = $this->cleanPageName($page_name);
			$edit_comment = 'Created by Tracker Field ' . $fieldId;
			TikiLib::lib('tiki')->create_page($page_name, 0, $page_data, TikiLib::lib('tiki')->now, $edit_comment, $user, TikiLib::lib('tiki')->get_ip_address(), '', '', $is_html, null, $this->getOption('wysiwyg'));
		}

		$data = array(
			'value' => $page_name,
			'page_data' => $page_data,
		);

		return $data;
	}

	function renderInput($context = array())
	{
		global $prefs;

		static $firstTime = true;

		$cols = $this->getOption('width');
		$rows = $this->getOption('height');

		if ($this->getOption('toolbars') === 0) {
			$toolbars = false;
		} else  {
			$toolbars = true;
		}

		$data = array(
			'toolbar' => $toolbars ? 'y' : 'n',
			'cols' => ($cols >= 1) ? $cols : 80,
			'rows' => ($rows >= 1) ? $rows : 6,
			'keyup' => '',
		);

		if ($this->getOption('wordmax')) {
            $data['keyup'] = "wordCount({$this->getOption('wordmax')}, this, 'cpt_{$this->getConfiguration('fieldId')}', '" . addcslashes(tr('Word Limit Exceeded'), "'") . "')";
		} elseif ($this->getOption('max')) {
            $data['keyup'] = "charCount({$this->getOption('max')}, this, 'cpt_{$this->getConfiguration('fieldId')}', '" . addcslashes(tr('Character Limit Exceeded'), "'") . "')";
		}
		$data['element_id'] = 'area_' . uniqid();
		if ($firstTime && $this->getOption('wysiwyg') === 'y' && $prefs['wysiwyg_htmltowiki'] != 'y') {	// html wysiwyg
			$is_html = '<input type="hidden" id="allowhtml" value="1" />';
			$firstTime = false;
		} else {
			$is_html = '';
		}
		return $this->renderTemplate('trackerinput/wiki.tpl', $context, $data) . $is_html;
	}

	function renderOutput($context = array())
	{
		return $this->attemptParse($this->getConfiguration('page_data'));
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$data = array();
		$value = $this->getValue();
		$baseKey = $this->getBaseKey();

		if (!empty($value)) {

			$info = TikiLib::lib('tiki')->get_page_info($value, true, true);
			if ($info) {
				$data = array(
					$baseKey => $typeFactory->identifier($value),
					"{$baseKey}_text" => $typeFactory->wikitext($info['data']),
					"{$baseKey}_raw" => $typeFactory->identifier($info['data']),
				);
			}

		}

		return $data;
	}

	function getProvidedFields()
	{
		$baseKey = $this->getBaseKey();

		$data = array(
			$baseKey, // the page name
			"{$baseKey}_text", // wiki text
		);

		return $data;
	}

	function getGlobalFields()
	{
		$baseKey = $this->getBaseKey();

		$data = array(
			"{$baseKey}_text" => true,
		);

		return $data;
	}

	function getTabularSchema()
	{
		$definition = $this->getTrackerDefinition();
		$schema = new Tracker\Tabular\Schema($definition);

		$permName = $this->getConfiguration('permName');
		$name = $this->getConfiguration('name');
		$insertId = $this->getInsertId();
		$baseKey = $this->getBaseKey();
		$fieldIdForPagename = $this->getOption('fieldIdForPagename');
		$fieldForPagename = $definition->getField($fieldIdForPagename);


		$plain = function () {
			return function ($value, $extra) {
				if (isset($extra['text'])) {	// indexed value from addQuerySource _raw indexed field
					$value = $extra['text'];
				} else {
					// not indexed yet, need to find page contents for $value
					if (TikiLib::lib('tiki')->page_exists($value)) {
						// Get wiki page content
						$page_info = TikiLib::lib('tiki')->get_page_info($value);
						$value = $page_info['data'];
					}
				}

				return $value;
			};
		};

		$render = function () use ($plain) {
			$f = $plain();
			return function ($value, $extra) use ($f) {
				$value = $f($value, $extra);

				return $this->attemptParse($value);
			};
		};

		$schema->addNew($permName, 'default')
			->setLabel($name)
			->setRenderTransform(function ($value) {
				return $value;
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName) {
				$info['fields'][$permName] = $value;
			});

		$schema->addNew($permName, 'content-raw')
			->setLabel($name)
			->addQuerySource('text', "{$baseKey}_raw")
			->setRenderTransform($plain())
			->setParseIntoTransform(function (& $info, $value) use ($permName, $fieldForPagename, $insertId) {
				$data = $this->getFieldData([
					$insertId => $value,
					'ins_' . $fieldForPagename['fieldId'] => $info['fields'][$fieldForPagename['permName']],
					'itemId' => empty($info['itemId']) ? 0 : $info['itemId'],
				]);
				$info['fields'][$permName] = $data['value'];
			});

		// convert incoming html to wiki syntax and the opposite on export
		$schema->addNew($permName, 'content-wiki-html')
			->setLabel($name)
			->addQuerySource('text', "{$baseKey}_raw")
			->setRenderTransform($render())
			->setParseIntoTransform(function (& $info, $value) use ($permName, $fieldForPagename, $insertId) {
				$data = $this->getFieldData([
					$this->getInsertId() => TikiLib::lib('edit')->parseToWiki($value),
					'ins_' . $fieldForPagename['fieldId'] => $info['fields'][$fieldForPagename['permName']],
					'itemId' => empty($info['itemId']) ? 0 : $info['itemId'],
				]);
				$info['fields'][$permName] = $data['value'];
			});

		return $schema;
	}

	protected function attemptParse($text)
	{
		global $prefs;

		$parseOptions = array();
		if ($this->getOption('wysiwyg') === 'y' && $prefs['wysiwyg_htmltowiki'] != 'y') {
			$parseOptions['is_html'] = true;
		}
		return TikiLib::lib('parser')->parse_data($text, $parseOptions);
	}

	/**
	 * Gets the full page name including the namespace and separator
	 *
	 * @param $short_name
	 * @return string
	 */
	private function getFullPageName($short_name)
	{
		global $prefs;

		$namespace = $this->getOption('namespace');
		if ($namespace == 'none') {
			$page_name = $short_name;
		} elseif ($namespace == 'custom' && ! empty($this->getOption('customnamespace'))) {
			$page_name = $this->getOption('customnamespace') . $prefs['namespace_separator'] . $short_name;
		} else {
			$page_name = 'trackerfield' . $this->getConfiguration('fieldId') . $prefs['namespace_separator'] . $short_name;
		}

		$page_name = $this->cleanPageName($page_name);

		return $page_name;
	}

	/**
	 * Gets and cleans the specified page name (i.e. the fieldIdForPagename field value with or without the namespace)
	 * @param $page_name
	 * @return string
	 */
	private function cleanPageName($page_name)
	{
		$wikilib = TikiLib::lib('wiki');
		if ($this->getOption('removeBadChars') === 'y' && $wikilib->contains_badchars($page_name)) {
			$bad_chars = $wikilib->get_badchars();
			$page_name = preg_replace('/[' . preg_quote($bad_chars, '/') . ']/', ' ', $page_name);
			$page_name = trim(preg_replace('/\s+/', ' ', $page_name));
		}
		if (strlen($page_name) > 160) {
			$page_name = substr($page_name, 0, 160);
		}
		return $page_name;
	}

}

