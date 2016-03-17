<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/wiki/pluginslib.php';

class WikiPluginPluginManager extends PluginsLib
{
	var $expanded_params = array('info');
	function getDefaultArguments()
	{
		return array(
					'info' => 'description|parameters|paraminfo', 
					'plugin' => '', 
					'module' => '',
					'singletitle' => 'none',
					'titletag' => 'h3',
					'start' => '',
					'limit' => '',
					'paramtype' => '',
					'showparamtype' => 'n',
					'showtopinfo' => 'y'
				);
	}
	function getName()
	{
		return 'PluginManager';
	}
	function getVersion()
	{
		return preg_replace("/[Revision: $]/", '', "\$Revision: 1.11 $");
	}
	function getDescription()
	{
		return wikiplugin_pluginmanager_help();
	}
	function run($data, $params)
	{
		global $helpurl;
		$wikilib = TikiLib::lib('wiki');
		$tikilib = TikiLib::lib('tiki');
		if (!is_dir(PLUGINS_DIR)) {
			return $this->error('No plugin directory defined');
		}
		if (empty($helpurl)) {
			$helpurl = 'http://doc.tiki.org/';
		}
		
		$params = $this->getParams($params);
		extract($params, EXTR_SKIP);

		if (!empty($module) && !empty($plugin)) {
			return $this->error(tra('Either the module or plugin parameter must be set, but not both.'));
		} elseif (!empty($module)) {
			$aPrincipalField = array('field' => 'plugin', 'name' => 'Module');
			$helppath = $helpurl . $aPrincipalField['name'] . ' ';
			$filepath = 'mod-func-';

			$modlib = TikiLib::lib('mod');
			$aPlugins = $modlib->list_module_files();
			$mod = true;
			$type = ' module';
			$plugin = $module;
		} else {
			$aPrincipalField = array('field' => 'plugin', 'name' => 'Plugin');
			$helppath = $helpurl . $aPrincipalField['name'];
			$filepath = 'wikiplugin_';
			$aPlugins = $wikilib->list_plugins();
			$mod = false;
			$type = ' plugin';
		}
		$all = $aPlugins;
		//if the user set $module, that setting has now been moved to $plugin so that one code set is used
		//$aPlugins and $all now has the complete list of plugin or module file names - the code below modifies $aPlugins
		//if necessary based on user settings
		if (!empty($plugin)) {
			if (strpos($plugin, '|') !== false) {
				$aPlugins = array();
				$userlist = explode('|', $plugin);
				foreach ($userlist as $useritem) {
					$file = $filepath . $useritem . '.php';
					$confirm = in_array($file, $all);
					if ($confirm === false) {
						return '^' . tr('Plugin Manager error: %0%1 not found', $useritem, $type) . '^';
					} else {
						$aPlugins[] = $file;
					}
				}
			} elseif (strpos($plugin, '-') !== false) {
				$userrange = explode('-', $plugin);
				$begin = array_search($filepath . $userrange[0] . '.php', $aPlugins);
				$end = array_search($filepath . $userrange[1] . '.php', $aPlugins);
				$beginerror = '';
				$enderror = '';
				$type2 = $type;
				if ($begin === false || $end === false) {
					if ($begin === false) {
						$beginerror = $userrange[0];
					} 
					if ($end === false) {
						$enderror = $userrange[1];
						if (!empty($beginerror)) {
							$and = ' and ';
						} else {
							$and = '';
							$type = '';
						}
					}
					return '^' . tr('Plugin Manager error: %0%1%2%3%4 not found', $beginerror, $type, $and, $enderror, $type2) . '^';
				} elseif ($end > $begin) {
					$aPlugins = array_slice($aPlugins, $begin, $end-$begin+1);
				} else {
					$aPlugins = array_slice($aPlugins, $end, $begin-$end+1);
				}	 		
			} elseif (!empty($limit)) { 
				$begin = array_search($filepath . $plugin . '.php', $aPlugins); 
				if ($begin === false) {
					return '^' . tr('Plugin Manager error: %0%1 not found', $begin, $type) . '^';
				} else {
					$aPlugins = array_slice($aPlugins, $begin, $limit);
				}
			} elseif ($plugin != 'all') {
				$file = $filepath . $plugin . '.php';
				$confirm = in_array($file, $aPlugins);
				if ($confirm === false) {
					return '^' . tr('Plugin Manager error:  %0%1 not found', $plugin, $type) . '^';
				} else {
					$aPlugins = array();
					$aPlugins[] = $file;
				}
			}
		} else {
			if (!empty($start) || !empty($limit)) {
				if (!empty($start) && !empty($limit)) {
					$aPlugins = array_slice($aPlugins, $start-1, $limit);
				} elseif (!empty($start)) {
					$aPlugins = array_slice($aPlugins, $start-1);
				} else {			
					$aPlugins = array_slice($aPlugins, 0, $limit);
				}
			}
		}
		//Set all data variables needed for separate code used to generate the display table
		$aData=array();
		if ($singletitle == 'table' || count($aPlugins) > 1) {
			foreach ($aPlugins as $sPluginFile) {
				global $sPlugin, $numparams;
				if ($mod) {
					$infoPlugin = get_module_params($sPluginFile);
					$namepath = $sPlugin;
				} else {
					$infoPlugin = get_plugin_info($sPluginFile);
					$namepath = ucfirst($sPlugin);
				}
				if (in_array('description', $info)) {
					if (isset($infoPlugin['description'])) {
						if ($numparams > 1) {
							$aData[$sPlugin]['description']['onekey'] = $infoPlugin['description'];
						} else {
							$aData[$sPlugin]['description'] = $infoPlugin['description'];
						}
					} else {
						$aData[$sPlugin]['description'] = ' --- ';
					}
				}
				if (in_array('parameters', $info)) {
					if ($numparams > 0) {
						if ($aPrincipalField['field'] == 'plugin' && !in_array('options', $info) && $numparams > 1) {
							$aData[$sPlugin][$aPrincipalField['field']]['rowspan'] = $numparams;
							if (in_array('description', $info)) {
								$aData[$sPlugin]['description']['rowspan'] = $numparams;
							}
						}
						foreach ($infoPlugin['params'] as $paramname => $param) {
							if (isset($infoPlugin['params'][$paramname]['description'])) {
								$paramblock = '~np~' . $infoPlugin['params'][$paramname]['description'] . '~/np~';
							}
							if (isset($param['options']) && is_array($param['options'])) {
								$paramblock .= '<br /><em>' . tra('Options:') . '</em> ';
								$i = 0;
								foreach ($param['options'] as $oplist => $opitem) {
									if (isset($opitem['value'])) {
										$paramblock .= $opitem['value'];
									} else {
										$paramblock .=  $opitem['text'];
									}
									$paramblock .= ' | ';
									$i++;
								}
								$paramblock = substr($paramblock, 0, -3);
							}
							if (isset($infoPlugin['params'][$paramname]['required']) && $infoPlugin['params'][$paramname]['required'] == true) {
								$aData[$sPlugin]['parameters']['<b><code>' . $paramname . '</code></b>'] = $paramblock;
							} else {
								$aData[$sPlugin]['parameters']['<code>' . $paramname . '</code>'] = $paramblock;
							}
						}
					} else {
						$aData[$sPlugin]['parameters']['<em>no parameters</em>'] = '<em>' . tra('n/a') . '</em>';
					}
				}
				$aData[$sPlugin]['plugin']['plugin'] = '[' . $helppath . $namepath . '|' . ucfirst($sPlugin) . ']';
			} // Plugins Loop
			return PluginsLibUtil::createTable($aData, $info, $aPrincipalField);
		} else {
			//Replicates a documentation table for parameters for a single plugin or module
			//Not using plugin lib table to avoid making custom modifications
			global $sPlugin, $numparams;
			if ($mod) {
				$infoPlugin = get_module_params($aPlugins[0]);
				$namepath = $sPlugin;
			} else {
				$infoPlugin = get_plugin_info($aPlugins[0]);
				$namepath = ucfirst($sPlugin);
			}
			if ($singletitle == 'top') {
				$title = '<' . $titletag . '>['. $helppath . $namepath 
					. '|' . ucfirst($sPlugin) . ']</' . $titletag . '>';
				$title .= $infoPlugin['description'] . '<br />';
			} else {
				$title = '';
			}
			$headbegin = "\n\t\t" . '<th class="heading">';
			$cellbegin = "\n\t\t" . '<td>';
			$header =  "\n\t" . '<tr class="heading">' . $headbegin . 'Parameters</td>';
			$rows = '';
			if (isset($numparams) && $numparams > 0) {
				$header .= $headbegin . tra('Accepted Values') . '</th>';
 			   	$header .= $headbegin . tra('Description') . '</th>';
				$rowCounter = 1;
				//sort required params first
				$reqarray = array_column($infoPlugin['params'], 'required');
				$keysarray = array_keys($infoPlugin['params']);
				$reqarray = array_combine($keysarray, $reqarray);
				if (count($reqarray) == count($infoPlugin['params'])) {
					array_multisort($reqarray, SORT_DESC, $infoPlugin['params']);
				}
				//add body instructions to the parameter array
				if (!empty($infoPlugin['body'])) {
					$body = array('(body of plugin)' => array('description' => $infoPlugin['body']));
					$infoPlugin['params'] = array_merge($body, $infoPlugin['params']);
				}
				foreach ($infoPlugin['params'] as $paramname => $paraminfo) {
					unset($sep, $septext);
					//check is paramtype filter is set
					if (empty($params['paramtype'])
						|| ((empty($paraminfo['doctype']) && !empty($params['paramtype']) && $params['paramtype'] === 'none')
						|| (!empty($paraminfo['doctype']) && $params['paramtype'] == $paraminfo['doctype']))
					) {
						$filteredparams[] = $paraminfo;
						$rows .= "\n\t" . '<tr>' . $cellbegin;
						//Parameters column
						if (isset($paraminfo['required']) && $paraminfo['required'] == true) {
							$rows .= '<strong><code>' . $paramname . '</code></strong>';
						} elseif ($paramname == '(body of plugin)') {
							$rows .= tra('(body of plugin)');
						} else {
							$rows .= '<code>' . $paramname . '</code>' ;
						}
						if (isset($params['showparamtype']) && $params['showparamtype'] === 'y'
							&& !empty($paraminfo['doctype']))
						{
							$rows .= '<br /><small>(' . $paraminfo['doctype'] . ')</small>';
						}
						$rows .= '</td>';
						//Accepted Values column
						$rows .= $cellbegin;
						if (isset($paraminfo['separator'])) {
							$sep = $paraminfo['separator'];
							$septext = tr('%0separator:%1 ', '<em>', '</em>') . '<code>' . $paraminfo['separator'] .
								'</code>';
						} else {
							$sep = '| ';
						}
						if (isset($paraminfo['accepted'])) {
							$rows .= $paraminfo['accepted'];
							if (isset($septext)) {
								$rows .= '<br />' . $septext;
							}
							$rows .= '</td>';
						} elseif (isset($paraminfo['options'])) {
							$optcounter = 1;
							$numoptions = count($paraminfo['options']);
							foreach ($paraminfo['options'] as $oplist => $opitem) {
								$rows .= strlen($opitem['value']) == 0 ? tra('(blank)') : $opitem['value'];
								if ($optcounter < $numoptions) {
									if ($numoptions > 10) {
										$rows .= $sep;
									} else {
										$rows .= '<br />';
									}
								}
								$optcounter++;
							}
							if (isset($septext)) {
								$rows .= '<br />' . $septext;
							}
							$rows .= '</td>';
						} elseif (isset($paraminfo['filter'])) {
							if ($paraminfo['filter'] == 'striptags') {
								$rows .= tra('any string except for HTML and PHP tags');
							} else {
								$rows .= $paraminfo['filter'];
							}
							if (isset($septext)) {
								$rows .= '<br />' . $septext;
							}
							$rows .= '</td>';
						} else {
							if (isset($septext)) {
								$rows .= '<br />' . $septext;
							}
							$rows .= '</td>';
						}
						//Description column
						$rows .= $cellbegin . $paraminfo['description'] . '</td>';
						//Default column
						if ($rowCounter == 1) {
							$header .= $headbegin . tra('Default') . '</th>';
						}
						if (!isset($paraminfo['default'])) {
							$paraminfo['default'] = '';
						}
						$rows .= $cellbegin . $paraminfo['default'] . '</td>';
						//Since column
						if ($rowCounter == 1) {
							$header .= $headbegin . tra('Since') . '</th>';
						}
						$since = !empty($paraminfo['since']) ? $paraminfo['since'] : '';
						$rows .= $cellbegin . $since . '</td>';
						$rows .= "\n\t" . '</tr>';
						$rowCounter++;
					}
				}
				if (!empty($infoPlugin['additional']) && (empty($params['paramtype']) || $params['paramtype'] === 'none')) {
					$rows .= '<tr><td colspan="5">' . $infoPlugin['additional'] . '</td></tr>';
				}
			} else {
				if (!empty($infoPlugin['body'])) {
					$rows .= "\n\t" . '<tr>' . $cellbegin . '<em>' . tra('(body of plugin)') . ' - </em>'
						. $infoPlugin['body'] . '</td>';
				}
				$rows .= "\n\t" . '<tr>' . $cellbegin . '<em>' . tra('no parameters') . '</em></td>';
			}
			$header .= "\n\t" . '</tr>';
			$pluginprefs = !empty($infoPlugin['prefs']) && $params['showtopinfo'] !== 'n' ? '<em>'
				. tra('Preferences required:') . '</em> ' . implode(', ', $infoPlugin['prefs']). '<br/>' : '';
			$title .= isset($infoPlugin['introduced']) && $params['showtopinfo'] !== 'n' ? '<em>' .
				tr('Introduced in %0', 'Tiki' . $infoPlugin['introduced']) . '.</em>' : '';
			$required = !empty($filteredparams) ? array_column($filteredparams, 'required') : false;
			$bold = in_array(true, $required) > 0 ? '<em> ' . tr('Required parameters are in%0 %1bold%2', '</em>',
				'<strong><code>', '</code></strong>.') : '';
			$sOutput = $title . $bold . '<br>' . $pluginprefs . '<div class="table-responsive">' .
				'<table class="table table-striped table-hover">' . $header . $rows . '</table></div>' . "\n";
			return $sOutput;
		}
	}
	function processDescription($sDescription)
	{
		$sDescription=str_replace(',', ', ', $sDescription);
		$sDescription=str_replace('|', '| ', $sDescription);
		$sDescription=strip_tags(wordwrap($sDescription, 35));
		return $sDescription;
	}
}

function wikiplugin_pluginmanager_info()
{
	return array(
		'name' => tra('Plugin Manager'),
		'documentation' => 'PluginPluginManager',
		'description' => tra('List wiki plugin or module information for the site'),
		'prefs' => array( 'wikiplugin_pluginmanager' ),
		'introduced' => 1,
		'iconname' => 'plugin',
		'params' => array(
			'info' => array(
				'required' => false,
				'name' => tra('Information'),
				'description' => tr('Determines what information is shown. Values separated with %0|%1.
					Ignored when %0singletitle%1 is set to %0top%1 or %0none%1.', '<code>', '</code>'),
   				'filter' => 'text',
				'accepted' => tra('One or more of: description | parameters | paraminfo'),
				'default' => 'description | parameters | paraminfo ',
				'since' => '1',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Description'), 'value' => 'description'), 
					array('text' => tra('Description and Parameters'), 'value' => 'description|parameters'), 
					array('text' => tra('Description & Parameter Info'), 'value' => 'description|paraminfo'), 
					array('text' => tra('Parameters & Parameter Info'), 'value' => 'parameters|paraminfo'), 
					array('text' => tra('All'), 'value' => 'description|parameters|paraminfo')
				)
			),
			'plugin' => array(
				'required' => false,
				'name' => tra('Plugin'),
				'description' => tr('Name of a plugin (e.g., backlinks), or list separated by %0|%1, or range separated
					 by %0-%1. Single plugin can be used with %0limit%1 parameter.', '<code>', '</code>'),
				'filter' => 'text',
				'default' => '',
				'since' => '5.0',					
			),
			'module' => array(
				'required' => false,
				'name' => tra('Module'),
				'description' => tr('Name of a module (e.g., calendar_new), or list separated by %0|%1, or range separated
					by %0-%1. Single module can be used with %0limit%1 parameter.', '<code>', '</code>'),
				'filter' => 'text',
				'default' => '',
				'since' => '6.1',					
			),
			'singletitle' => array(
				'required' => false,
				'name' => tra('Single Title'),
				'description' => tr('Set placement of plugin name and description when displaying information for only one plugin'),
				'filter' => 'alpha', 
				'default' => 'none',
				'since' => '5.0', 
				'options' => array(
					array('text' => tra(''), 'value' => ''), 
					array('text' => tra('Top'), 'value' => 'top'), 
					array('text' => tra('Table'), 'value' => 'table'), 
				),  				
			),
			'titletag' => array(
				'required' => false,
				'name' => tra('Title Heading'),
				'description' => tr('Sets the heading size for the title, e.g., %0h2%1.', '<code>', '</code>'),
				'filter' => 'alnum',
				'default' => 'h3',
				'since' => '5.0',
				'advanced' => true,
			),
			'start' => array(
				'required' => false,
				'name' => tra('Start'),
				'description' => tra('Start with this plugin record number (must be an integer 1 or greater).'),
				'filter' => 'digits',
				'default' => '',
				'since' => '5.0',					
			),
			'limit' => array(
				'required' => false,
				'name' => tra('Limit'),
				'description' => tra('Number of plugins to show. Can be used either with start or plugin as the starting
					point. Must be an integer 1 or greater.'),
				'filter' => 'digits',
				'default' => '',
				'since' => '5.0',					
			),
			'paramtype' => array(
				'required' => false,
				'name' => tra('Parameter Type'),
				'description' => tr('Only list parameters with this %0doctype%1 setting. Set to %0none%1 to show only
					parameters without a type setting and the body instructions.' , '<code>', '</code>'),
				'since' => '15.0',
				'filter' => 'alpha',
				'default' => '',
				'advanced' => true,
			),
			'showparamtype' => array(
				'required' => false,
				'name' => tra('Show Parameter Type'),
				'description' => tr('Show the parameter %0doctype%1 value.' , '<code>', '</code>'),
				'since' => '15.0',
				'filter' => 'alpha',
				'default' => '',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showtopinfo' => array(
				'required' => false,
				'name' => tra('Show Top Info'),
				'description' => tr('Show information above the table regarding preferences required and the first
					version when the plugin became available. Shown by default.'),
				'since' => '15.0',
				'filter' => 'alpha',
				'default' => '',
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function get_plugin_info($sPluginFile)
{
	preg_match("/wikiplugin_(.*)\.php/i", $sPluginFile, $match);
	global $sPlugin, $numparams;
	$sPlugin= $match[1];
	include_once(PLUGINS_DIR.'/'.$sPluginFile);
	global $tikilib;
	$parserlib = TikiLib::lib('parser');
	
	$infoPlugin = $parserlib->plugin_info($sPlugin);
	$numparams = isset($infoPlugin['params']) ? count($infoPlugin['params']) : 0;
	return $infoPlugin;
}

function get_module_params($sPluginFile)
{
	preg_match("/mod-func-(.*)\.php/i", $sPluginFile, $match);
	global $sPlugin, $numparams;
	$sPlugin= $match[1];
	include_once('modules/' . $sPluginFile);
	$info_func = "module_{$sPlugin}_info";
	$infoPlugin = $info_func();
	$numparams = isset($infoPlugin['params']) ? count($infoPlugin['params']) : 0;
	return $infoPlugin;
}

function wikiplugin_pluginmanager($data, $params)
{
	$plugin = new WikiPluginPluginManager();
	return $plugin->run($data, $params);
}
