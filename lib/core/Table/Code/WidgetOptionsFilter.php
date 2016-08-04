<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Table_Code_WidgetOptionsFilter
 *
 * Creates the code for the filter widget options portion of the Tablesorter jQuery code
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_WidgetOptions
 */
class Table_Code_WidgetOptionsFilter extends Table_Code_WidgetOptions
{

	protected function getOptionArray()
	{
		if (parent::$filters) {
			$wof[] = 'filter_cssFilter : \'form-control\'';
			//allows for different label versus value in dropdowns
			$wof[] = 'filter_selectSourceSeparator : \'|\'';
			//server side filtering
			if (parent::$ajax) {
				$wof[] = 'filter_serversideFiltering : true';
			}
			//hide filters
			if (isset(parent::$s['filters']['hide']) && parent::$s['filters']['hide'] === true) {
				$wof[] = 'filter_hideFilters : true';
			}
			//filter reset
			if (isset(parent::$s['filters']['type']) && parent::$s['filters']['type'] === 'reset') {
				$wof[] = 'filter_reset : \'button#' . parent::$s['filters']['reset']['id'] . '\'';
			}
			//filter_functions and filter_formatter
			if (parent::$filtercol) {
				$ffunc = '';
				$fform = '';
				$custom_filter_columns = array();
				foreach (parent::$s['columns'] as $col => $info) {
					$info = !empty($info['filter']) ? $info['filter'] : [];
					$colpointer =  parent::$usecolselector ? (string) '\'' . $col . '\'' : (int) $col;
					if (!empty($info['type'])) {
						switch($info['type']) {
							case 'dropdown' :
								$o = [];
								if (array_key_exists('options', $info)) {
									foreach ($info['options'] as $key => $val) {
										$label =  addcslashes(is_numeric($key) ? $val : $key,"'/");
										if (parent::$ajax) {
											$o[] = '\'' . $label . '\' : function() {}';
										} else {
											$o[] = '\'' . $label . '\' : function(e, n, f, i) { return /' . $val
													. '/.test(e);}';
										}
									}
									if (count($o) > 0) {
										$options = $this->iterate($o, '{', $this->nt4 . '}', $this->nt5, '', ',');
										$ffunc[] = $colpointer . ' : ' . $options;
									}
								} elseif (!parent::$ajax) {
									if( array_key_exists('empty', $info) ) {
										$ffunc[] = $colpointer . ' : {
					\'(empty)\': function( e, n, f, i, $r, c, data ) {
						if( typeof e === "Object" && typeof n === "Object" && typeof f === "undefined" ) {
							// v2.22.0 compatibility
							c = e;
							data = n;
						}
						if( data.filter === "(empty)" )
							return ( "" + data.exact ) === "";
						else
							return data.isMatch ?
								( "" + data.iExact ).search( data.iFilter ) >= 0 :
								data.filter === data.exact;
					}
				}';
										$custom_filter_columns[$col] = $info['empty'];
									} else {
										$ffunc[] = $colpointer . ' : true';
										$custom_filter_columns[$col] = false;
									}
								}
								break;
							case 'range' :
								$min = isset($info['from']) ? $info['from'] : 0;
								$max = isset($info['to']) ? $info['to'] : 100;
								$valtohead = isset($info['style']) && $info['style'] == 'popup' ? 'false' : 'true';
								$fform[] = $colpointer . ' : function($cell, indx){return $.tablesorter.filterFormatter.uiRange('
										. '$cell, indx, {values: [' . $min . ', ' . $max . '], min: ' . $min . ', max: ' . $max
										. ', delayed: false, valueToHeader: ' . $valtohead . ', exactMatch: true});}';
								break;
							case 'date' :
								$fm = isset($info['from']) ? $info['from'] : '';
								$to = isset($info['to']) ? $info['to'] : '';
								$format = isset($info['format']) ? $info['format'] : 'yy-mm-dd';
								$fform[] = $colpointer . ' : function($cell, indx){return $.tablesorter.filterFormatter.uiDatepicker('
										. '$cell, indx, {from: \'' . $fm . '\', to: \'' . $to . '\', dateFormat: \'' . $format
										. '\', changeMonth: true, changeYear: true});}';
								break;
						}
					}
				}
				unset($col, $info);
				if (is_array($ffunc)) {
					$wof[] = $this->iterate($ffunc, 'filter_functions : {', $this->nt3 . '}', $this->nt4, '');
				}
				if (is_array($fform)) {
					$wof[] = $this->iterate($fform, 'filter_formatter : {', $this->nt3 . '}', $this->nt4, '');
				}
				if( $custom_filter_columns ) {
					$wof[] = 'filter_matchType : { \'input\': \'exact\', \'select\': \'match\' }';
					$wof[] = 'filter_selectSource : function( table, column, onlyAvail ) {
				table = $( table )[0];
				var rowIndex, tbodyIndex, len, row, cell, cache, indx, child, childLen,
					c = table.config,
					wo = c.widgetOptions,
					arry = [];
				var emptyFilters = ' . json_encode($custom_filter_columns) . ';
				if( emptyFilters[column] )
					arry[ arry.length ] = {value: "(empty)", text: emptyFilters[column]};
				for ( tbodyIndex = 0; tbodyIndex < c.$tbodies.length; tbodyIndex++ ) {
					cache = c.cache[tbodyIndex];
					len = c.cache[tbodyIndex].normalized.length;
					// loop through the rows
					for ( rowIndex = 0; rowIndex < len; rowIndex++ ) {
						// get cached row from cache.row ( old ) or row data object
						// ( new; last item in normalized array )
						row = cache.row ?
							cache.row[ rowIndex ] :
							cache.normalized[ rowIndex ][ c.columns ].$row[0];
						// check if has class filtered
						if ( onlyAvail && row.className.match( wo.filter_filteredRow ) ) {
							continue;
						}
						// get non-normalized cell content
						if ( wo.filter_useParsedData ||
							c.parsers[column].parsed ||
							c.$headerIndexed[column].hasClass( \'filter-parsed\' ) ) {
							arry[ arry.length ] = \'\' + cache.normalized[ rowIndex ][ column ];
							// child row parsed data
							if ( wo.filter_childRows && wo.filter_childByColumn ) {
								childLen = cache.normalized[ rowIndex ][ c.columns ].$row.length - 1;
								for ( indx = 0; indx < childLen; indx++ ) {
									arry[ arry.length ] = \'\' + cache.normalized[ rowIndex ][ c.columns ].child[ indx ][ column ];
								}
							}
						} else {
							// get raw cached data instead of content directly from the cells
							// and parse by new line to add rows as separate values
							cell = cache.normalized[ rowIndex ][ c.columns ].$row.children().eq( column );
							if( cell.text().match(/[\r\n]/) )
								arry = arry.concat(cell.text().split(/[\r\n]/));
							else
								arry = arry.concat(cell.html().split(/\s*<br\s*\/?>\s*/i));
							// child row unparsed data
							if ( wo.filter_childRows && wo.filter_childByColumn ) {
								childLen = cache.normalized[ rowIndex ][ c.columns ].$row.length;
								for ( indx = 1; indx < childLen; indx++ ) {
									cell = cache.normalized[ rowIndex ][ c.columns ].$row.eq( indx ).children().eq( column );
									if( cell.text().match(/[\r\n]/) )
										arry = arry.concat(cell.text().split(/[\r\n]/));
									else
										arry = arry.concat(cell.html().split(/\s*<br\s*\/?>\s*/i));
								}
							}
						}
					}
				}
				return arry;
			}';
									}
			}
			if (count($wof) > 0) {
				return $wof;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}