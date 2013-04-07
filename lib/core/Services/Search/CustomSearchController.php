<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Controller to process requests from the custom search plugin using the list plugin to display results
// Refactored from customsearch_ajax.php for Tiki


class Services_Search_CustomSearchController
{
	function setUp()
	{
		Services_Exception_Disabled::check('wikiplugin_list');
		Services_Exception_Disabled::check('wikiplugin_customsearch');
		Services_Exception_Disabled::check('feature_search');
	}

	function action_customsearch($input)
	{
		global $prefs;

		$cachelib = TikiLib::lib('cache');
		$definition = $input->definition->word();
		if (empty($definition) || ! $definition = $cachelib->getSerialized($definition, 'customsearch')) {
			throw new Services_Exception(tra('Search expired.'));
		}

		$query = $definition['query'];
		$formatter = $definition['formatter'];

		$adddata = json_decode($input->adddata->text(), true);

		$dataappend = array();

		$recalllastsearch = $input->recalllastsearch->int() ? true : false;

		$id = $input->searchid->text();
		if (empty($id)) {
			$id = '0';
		}
		// setup AJAX pagination
		$offset_jsvar = "customsearch_$id.offset";
		$onclick = "$('#customsearch_$id').submit();return false;";
		$dataappend['pagination'] = "{pagination offset_jsvar=\"$offset_jsvar\" onclick=\"$onclick\"}";

		if ($input->groups->text()) {
			$groups = json_decode($input->groups->text(), true);
		} else {
			$groups = array();
		}
		if ($input->textrangegroups->text()) {
			$textrangegroups = json_decode($input->textrangegroups->text(), true);
		} else {
			$textrangegroups = array();
		}
		if ($input->daterangegroups->text()) {
			$daterangegroups = json_decode($input->daterangegroups->text(), true);
		} else {
			$datarangegroups = array();
		}
		if ($recalllastsearch && isset($_SESSION["customsearch_$id"])) {
			unset($_SESSION["customsearch_$id"]);
		}
		if ($input->sort_mode->text()) {
			if ($recalllastsearch) {
				$_SESSION["customsearch_$id"]["sort_mode"] = $input->sort_mode->text();
			}
			$query->setOrder($input->sort_mode->text());
		}
		if ($input->maxRecords->int()) {
			if ($recalllastsearch) {
				$_SESSION["customsearch_$id"]["maxRecords"] = $input->maxRecords->int();
			}
			$maxRecords = $input->maxRecords->int();	// pass request data required by list
		} else {
			$maxRecords = $prefs['maxRecords'];
		}
		if ($input->offset->int()) {
			if ($recalllastsearch) {
				$_SESSION["customsearch_$id"]["offset"] = $input->offset->int();
			}
			$offset = $input->offset->int();
		} else {
			$offset = 0;
		}
		$query->setRange($offset, $maxRecords);

		if ($adddata) {
			foreach ($adddata as $fieldid => $d) {
				$config = $d['config'];
				$name = $d['name'];
				$value = $d['value'];

				// save values entered as defaults while session lasts
				if (empty($value) && $value != 0) {
					$value = '';		// remove false or null
				}
				if ($recalllastsearch) {
					$_SESSION["customsearch_$id"][$fieldid] = $value;
				}

				if (empty($config['type'])) {
					$config['type'] = $name;
				}

				if ($config['_filter'] == 'language') {
					$filter = 'language';
				} elseif ($config['_filter'] == 'type') {
					$filter = 'type';
				} elseif ($config['_filter'] == 'categories' || $name == 'categories') {
					$filter = 'categories';
				} elseif ($name == 'daterange') {
					$filter = 'daterange';
				} else {
					$filter = 'content'; //default
				}

				if (is_array($value) && count($value > 1)) {
					$value = implode(' ', $value);
				} elseif (is_array($value)) {
					$value = current($value);
				}

				$function = "cs_dataappend_{$filter}";
				if (method_exists($this, $function)) {
					$this->$function($query, $config, $value);
				}
			}
		}

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$query->setWeightCalculator($unifiedsearchlib->getWeightCalculator());
		$index = $unifiedsearchlib->getIndex();
		$resultSet = $query->search($index);

		$formatter->setDataSource($unifiedsearchlib->getDataSource());
		$results = $formatter->format($resultSet);

		$results = TikiLib::lib('tiki')->parse_data($results, array('is_html' => true, 'skipvalidation' => true));

		return array('html' => $results);
	}

	private function cs_dataappend_language(Search_Query $query, $config, $value)
	{
		if ($config['type'] != 'text') {
			if (!empty($config['_value'])) {
				$value = $config['_value'];
				$query->filterLanguage($value);
			} elseif ($value) {
				$query->filterLanguage($value);
			}
		}
	}

	private function cs_dataappend_type(Search_Query $query, $config, $value)
	{
		if ($config['type'] != 'text') {
			if (!empty($config['_value'])) {
				$value = $config['_value'];
				$query->filterType($value);
			} elseif ($value) {
				$query->filterType($value);
			}
		}
	}

	private function cs_dataappend_content(Search_Query $query, $config, $value)
	{
		if ($value) {
			if ($config['type'] == 'checkbox') {
				if (empty($config['_field'])) {
					return;
				}
				if (!empty($config['_value'])) {
					if ($config['_value'] == 'n') {
						$config['_value'] = 'NOT y';
					}
					$query->filterContent($config['_value'], $config['_field']);
				} else {
					$query->filterContent('y', $config['_field']);
				}
			} elseif ($config['type'] == 'radio' && !empty($config['_value'])) {
				if (empty($config['_field'])) {
					$query->filterContent($config['_value']);
				} else {
					$query->filterContent($config['_value'], $config['_field']);
				}
			} else {
				// covers everything else including radio that have no _value set (use sent value)
				if (empty($config['_field'])) {
					$query->filterContent($value);
				} else {
					$query->filterContent($value, $config['_field']);
				}
			}
		}
		return false;
	}

	private function cs_dataappend_categories(Search_Query $query, $config, $value)
	{
		if (isset($config['_filter']) && $config['_filter'] == 'categories' && $config['type'] != 'text') {
			if (!empty($config['_value'])) {
				$value = $config['_value'];
			}
		} elseif (!isset($config['_style'])) {
			return;
		} elseif ($value) {
			$deep = isset($config['_showdeep']) && $config['_showdeep'] != 'n';
			$query->filterCategory($value, $deep);
		}
	}

	private function cs_dataappend_daterange(Search_Query $query, $config, $value)
	{
		if ($vals = preg_split('/,/', $value)) {
			if (count($vals) == 2) {
				$from = $vals[0];
				$to = $vals[1];
				if (!empty($config['_field'])) {
					$field = $config['_field'];
				} else {
					$field = 'modification_date';
				}
				$query->filterRange($from, $to, $field);
			}
		}
	}

}
