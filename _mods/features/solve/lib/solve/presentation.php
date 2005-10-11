<?php
/**
 * @version $Id: presentation.php,v 1.6 2005-10-11 23:23:36 michael_davey Exp $
 * @package Solve
 * @copyright (C) 2005 the Tiki community
 * @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
 */

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

class TikiPresentation {
    var $_config = null;

    function Initialize() {
    }
    
    function setConfig( $config ) {
        $this->_config = $config;
    }

    /**
     * @param columns the columns to display
     * @param item (optional) the item (bug|case) to render
     * @param notes (optional) collection of notes objects
     * @param section bugs | cases
     * @param task edit | new | view ...
     */
    function Render($columns, $item=null, $notes=false, $section, $task) {
        $this->_renderTopNav('bug', false);
        $this->_renderAppForm($columns, $item, $notes, $task);
    }

    function RenderList($bugs, $queryfields, $columns, $task='search') {
        $this->_renderTopNav($task, false);
        $this->_renderSearchForm($bugs, $queryfields, $columns, $task);
    }

    // retreive sortcolumn/sortorder key/value pair
    function _getOrderBy() {
        $sortcolumn = '';
        $sortorder = '';

        foreach(($this->_config->getSortBy()) as $sortcolumn=>$sortorder) {
            break;
        }
        return array($sortcolumn, $sortorder);
    }
    
    // shortcut to get a url of the form "solve/whatever"
    function _getBaseUrl() {
        global $feature_server_name;

        return $feature_server_name . '/solve/' . _MYNAMEIS;
    }
    
    function _getNewOrderby($sortcolumn, $sortorder, $parameter) {
        if($sortcolumn == $parameter) {
            $newOrder_by = $parameter;
            if($sortorder == 'desc') {
                $newOrder_by .= ',asc';
            } else {
                $newOrder_by .= ',desc';
            }
        } else {
            $newOrder_by = "$parameter,desc";
        }
        
        return $newOrder_by;
    }
    
    function _getAppropriateFormfield($atype, $column, $value, $options, $ignorewidth=false) {
        global $short_date_format, $short_time_format;

        $returnWidget = '';
        
        $showMe = true;
        
        $widgetWidth = '';
        
        if( $ignorewidth ) {
            $widgetWidth = '100';
        } else {
            $widgetWidth = $column['size'];
        }
        
        if( ! (bool)$column['show'] ) {
            $returnWidget = '<input type="hidden" name="' . $column['field'] . '" value="' . $value . '" />';
            $showMe = false;
        } else {
            switch($atype) {
                case 'plaintext':
                    $returnWidget = nl2br($value);
                    break;
                case 'enum':
					if( ! (bool)$column['canedit'] ) {
                        foreach($options as $thisoption) {
                            if( $thisoption['name'] == $value ) {
                                $returnWidget = $thisoption['value'];
                                break;
                            }
                        }
					} else {
						if($column['size'] > 0) {
							$returnWidget = '<select name="' . $column['field'] . '" class="solveinputbox" style="width: ' . $widgetWidth . '%;">';
						} else {
							$returnWidget = '<select name="' . $column['field'] . '" class="solveinputbox">';
						}
						foreach($options as $thisoption) {
							if( $thisoption['name'] == $value ) {
								$selectEd = 'selected ';
							} else {
								$selectEd = '';
							}
							$returnWidget .= '<option ' . $selectEd . 'value="' . $thisoption['name'] . '">' . $thisoption['value'] . '</option>';
						}
						$returnWidget .= '</select>';
					}
                    break;
				case 'datetime':
					if( ! (bool)$column['canedit'] ) {
						if( isset($value) && $value != '')
							$returnWidget = strftime($short_date_format.' '.$short_time_format, strtotime($value));
						else
							$returnWidget = '';
					} else {
						if($column['size'] < 50 && $column['size'] > 0) {
							$returnWidget = '<input type="text" name="' . $column['field'] . '" value="' . $value . '" class="solveinputbox" style="width:' . $widgetWidth . '%;" />';
						} elseif($column['size'] >= 50) {
							$returnWidget = '<textarea class="solveinputbox" name="' . $column['field'] . '" cols="'.$column['size'] . '" rows="5">' . $value . '</textarea>';
						} else {
							$returnWidget = '<input type="text" name="' . $column['field'] . '" value="' . $value . '" class="inputbox" style="width: 100%;" />';
						}
					}
					break;
                default:
					if( ! (bool)$column['canedit'] ) {
						$returnWidget = nl2br($value);
						$returnWidget .= '<input type="hidden" name="' . $column['field'] . '" value="' . $value . '" />';
					} else {
						if($column['size'] < 50 && $column['size'] > 0) {
							$returnWidget = '<input type="text" name="' . $column['field'] . '" value="' . $value . '" class="solveinputbox" style="width:' . $widgetWidth . '%;" />';
						} elseif($column['size'] >= 50) {
							$returnWidget = '<textarea class="solveinputbox" name="' . $column['field'] . '" cols="'.$column['size'] . '" rows="5">' . $value . '</textarea>';
						} else {
							$returnWidget = '<input type="text" name="' . $column['field'] . '" value="' . $value . '" class="inputbox" style="width: 100%;" />';
						}
					}
                    break;
            }
        }
        
        return array($returnWidget,$showMe);
    }
    
    function _getAppropriateListfield($atype, $column, $value, $options, $ignorewidth=false) {
        global $short_date_format, $short_time_format;

        $returnWidget = '';
        
        $showMe = true;
        
        $widgetWidth = '';
        
        if( $ignorewidth ) {
            $widgetWidth = '100';
        } else {
            $widgetWidth = $column['size'];
        }
        
        if( ! (bool)$column['inlist'] ) {
            $showMe = false;
        } else {
            switch($atype) {
                case 'plaintext':
                    $returnWidget = nl2br($value);
                    break;
                case 'enum':
                    foreach($options as $thisoption) {
                        if( $thisoption['name'] == $value ) {
                            $returnWidget = $thisoption['value'];
                            break;
                        }
                    }
                    break;
                case 'datetime':
                    if( isset($value) && $value != '')
                        $returnWidget = strftime($short_date_format.' '.$short_time_format, strtotime($value));
                    else
                        $returnWidget = '';
                    break;
                default:
                    $returnWidget = nl2br($value);
                    break;
            }
        }
        
        return array($returnWidget,$showMe);
    }


    function _renderTopNav($task, $isHome=false) {
        global $smarty, $site_nav_seper;
        global $vtiger_p_list_bugs, $vtiger_p_list_cases, $vtiger_p_search_bugs, $vtiger_p_search_cases;
        global $vtiger_p_create_bugs, $vtiger_p_create_cases, $vtiger_p_refresh_bugs, $vtiger_p_refresh_cases;

        if( _MYNAMEIS == 'bugs' ) {
            if( $vtiger_p_list_bugs == 'y') $smarty->assign( 'listbutton', true );
            if( $vtiger_p_create_bugs == 'y') $smarty->assign( 'newbutton', true );
            if( $vtiger_p_search_bugs == 'y') $smarty->assign( 'searchbutton', true );
            if( $vtiger_p_refresh_bugs == 'y') $smarty->assign( 'refreshbutton', true );
        }
        if( _MYNAMEIS == 'cases' ) {
            if( $vtiger_p_list_cases == 'y') $smarty->assign( 'listbutton', true );
            if( $vtiger_p_create_cases == 'y') $smarty->assign( 'newbutton', true );
            if( $vtiger_p_search_cases == 'y') $smarty->assign( 'searchbutton', true );
            if( $vtiger_p_refresh_cases == 'y') $smarty->assign( 'refreshbutton', true );
        }

        $smarty->assign('is_home', $isHome);
        $smarty->assign('base_url2', $this->_getBaseUrl());
        $smarty->assign('nav_separator', $site_nav_seper);
        $smarty->assign('task', $task);
        $smarty->assign('option', _MYNAMEIS);
    }

    function _renderAppForm($columns, $item = null, $notes, $task ) {
        global $smarty, $short_date_format, $short_time_format;

        $smarty->assign('datetimeformat', $short_date_format.' '.$short_time_format);

        if ($item == null || $item == '' || $item == array() || $item == false) {
            $itemTitle = "New";
            $item = false;
            $itemID = null;
        } else {
            $itemTitle = $item['name'];
            $itemID = $item['id'];
        }
        $tmpData = array();
        $tmpCase = array();

        foreach($columns['selected'] as $column) {
            $default[$column['field']] = $column;
        }

        if( isset($item) && $item ) { 
            $tmpCase =& $item;
            $savetype = "saveedit";
        } else {
            foreach($default as $key=>$column) {
                $tmpCase[$key] = $column['default'];
            }
            $savetype = "savenew";
        }

        foreach($columns['data'] as $columnData) {
            $tmpData[$columnData['name']] = $columnData;
        }
        foreach(array_keys($columns['selected']) as $key) {
            $column = &$columns['selected'][$key];
            $column['showme'] = false;
            if ( $task == 'view' ) $column['canedit'] = false;
            if (isset($tmpData[$column['field']]['type']) && 
                    isset($tmpCase[$column['field']])) {
            list($column['inputWidget'],$column['showme']) = $this->_getAppropriateFormfield(
                                                        $tmpData[$column['field']]['type'],
                                                        $column,
                                                        $tmpCase[$column['field']],
                                                        $tmpData[$column['field']]['options']
                                                        );
            }
        }
        if($notes) {
            foreach(array_keys($notes) as $key) {
                $note = &$notes[$key];
                $note['htmlfilename'] = '(No Attachment)';
                if(!empty($note['filename'])){
                        $note['htmlfilename'] = '<a href="solve/'. _MYNAMEIS . '/download?noteid='.$note["id"].'&moduleid='.$itemID.'">'.$note["filename"] .'</a>';
                }
            }
        }
        $smarty->assign('columns', $columns);
        switch (_MYNAMEIS) {
          case "cases":
            $smarty->assign('item_title', "Case: " . $itemTitle);
            $smarty->assign('section', "cases");
            break;
          case "bugs":
            $smarty->assign('item_title', "Bug: " . $itemTitle);
            $smarty->assign('section', "bugs");
            break;
          default:
            $smarty->assign('item_title', "Unknown: " . $itemTitle);
            $smarty->assign('section', "unknown");
            break;
        }
        $smarty->assign('base_url2', $this->_getBaseUrl() );
        $smarty->assign('task', $task);
        $smarty->assign('savetype', $savetype);
        $smarty->assign_by_ref('tmpCase', $tmpCase);
        $smarty->assign('item', $item);
        $smarty->assign('itemID', $itemID);
        $smarty->assign('notes', $notes);
        $smarty->assign('mid', 'solve-'._MYNAMEIS.'.tpl');
    }


    function _renderSearchForm($items, $queryfields, $columnData, $task) {
        global $smarty;

        // if( $items ) {

        list($sortcolumn, $sortorder) = $this->_getOrderBy();

        $smarty->assign('base_url2', $this->_getBaseUrl());
        $smarty->assign('sortcolumn', $sortcolumn);
        $smarty->assign('sortorder', $sortorder);
        switch (_MYNAMEIS) {
          case "cases":
            $smarty->assign('section', "cases");
            break;
          case "bugs":
            $smarty->assign('section', "bugs");
            break;
          default:
            $smarty->assign('section', "unknown");
            break;
        }

        /* header */
        $columns = array();
        $columnInfo = array();
if ( $task == 'search' ) {
        foreach($columnData['selected'] as $column) {
            $columns[$column['field']] = $column;
        }
        foreach($columnData['data'] as $column) {
            $tmpData[$column['name']] = $column;
        }
} else {
        $columns = $columnData['selected'];
        if( $columnData['data'] ) foreach(array_keys($columnData['data']) as $key) {
            $bcolumn = &$columnData['data'][$key];
            $tmpData[$bcolumn['name']] = $bcolumn;
        }
}

        foreach(array_keys($columns) as $key) {
            $column = &$columns[$key];
            if( (bool)$column['inlist']) {
                $orderby = $this->_getNewOrderBy($sortcolumn, $sortorder, $column['field']);
                $column['orderby'] = $orderby;
                $column['onClick'] = '';
                $column['href'] = 'href=#';
                if($task == 'search') {
                    $column['onClick'] = 'onclick="javascript: set_order_by_and_submit(document.SearchForm, \'' . $orderby . '\');"';
                } else {
                    $column['href'] = 'href="'.$this->_getBaseUrl().'/' . $task . '?order_by=' . $orderby . '"';
                }
            }
        }

        /* list */
        foreach(array_keys($items) as $key1) {
            $item = &$items[$key1];
            $item['columns'] = array();
            foreach(array_keys($columns) as $key2) {
                $column = &$columns[$key2];
                if( (bool)$column['inlist']) {
                    $item['columns'][$key2]['inlist'] = true;
                    if( !isset($column['options']) ) $column['options'] = array();
                    // @bug BUG XXX next line should not be needed
                    if( !isset($columnInfo[$column['field']]['options']) ) $columnInfo[$column['field']]['options'] = array();
                    list($item['columns'][$key2]['inputWidget'],$showme) = $this->_getAppropriateListfield(
                                                                $column['type'],
                                                                $column,
                                                                $item[$column['field']],
                                                                $columnInfo[$column['field']]['options']
                                                                );
                }
            }
        }

        foreach($queryfields as $field=>$value) {
            if( count( $tmpData[$field]['options'] ) > 0 ) {
                $options = array_merge( array(
                                            array('name'=>'--None--',
                                                  'value'=>'')
                                              ), $tmpData[$field]['options']);
            } else {
                $options = $tmpData[$field]['options'];
            }

            list($tmplfields[$field]['formfield'], $tmplfields[$field]['canshow']) = $this->_getAppropriateFormField($tmpData[$field]['type'],
                                                 $columns[$field],
                                                 $value,
                                                 $options,
                                                 true );
           $tmplfields[$field]['label'] = $tmpData[$field]['label'];
        }
      // } // end if $items

        global $site_nav_seper, $vtiger_p_edit_bugs, $vtiger_p_edit_cases, $vtiger_p_view_bugs, $vtiger_p_view_cases;
        if( _MYNAMEIS == 'bugs' ) {
            if( $vtiger_p_edit_bugs == 'y') $smarty->assign( 'editbutton', true );
            if( $vtiger_p_view_bugs == 'y') $smarty->assign( 'viewbutton', true );
        }
        if( _MYNAMEIS == 'cases' ) {
            if( $vtiger_p_edit_cases == 'y') $smarty->assign( 'editbutton', true );
            if( $vtiger_p_view_cases == 'y') $smarty->assign( 'viewbutton', true );
        }
        $smarty->assign('nav_separator', $site_nav_seper);
        $smarty->assign('task', $task);
        if( isset($tmplfields) ) {
            $smarty->assign('queryfields', $tmplfields);
        }
        $smarty->assign('items', $items);
        $smarty->assign('columns', $columns);
        $smarty->assign('mid', 'solve-'._MYNAMEIS.'_list.tpl');
    }

}    
?>
