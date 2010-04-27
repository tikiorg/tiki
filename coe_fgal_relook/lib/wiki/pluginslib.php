<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
* Plugin Lib
*
* A port of PhpWiki WikiPlugin class
* Principal use is port PhpWiki plugins, but can be used to make new ones.
* Use:
* - Extends PluginsLib with your class
* - add the lines
* <code>
* include "pluginslib.php";
* 
* function wikiplugin_backlinks($data, $params) {
*    $plugin = new BackLinks();
*    return $plugin->run($data, $params);
* }
* function wikiplugin_backlinks_help() {
*    $plugin = new BackLinks();
*    return $plugin->getDescription();
* }    * </code>
* @package Tikiwiki
* @subpackage Plugins
* @author Claudio Bustos
* @version $Revision: 1.12 $
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class PluginsLib extends TikiLib
{
	var $_errors;
	var $_data;
	var $_params;
	/**
	* Array of params to be expanded as arrays. Explode the string with {@link $separator}
	* @var array
	*/
	var $expanded_params = array();
	/**
	* Separator used to explote params listed on {@link $expanded_params}
	* @var string
	*/
	var $separator = "|";
	/**
	* List of fields retrieved from {@link TikiLib::list_pages()}
	* Keys are the name of the fields and values the names for tra();
	* @var array
	*/
	var $aInfoPresetNames = array(
	"hits" => "Hits", "lastModif" => "Last mod", "user" => "Last author", "len" => "Size", "comment" => "Com", "creator" => "Creator", "version" => "Last ver", "flag" => "Status", "versions" => "Vers", "links" => "Links", "backlinks" => "Backlinks");
	/**
	* Process the params, in this order:
	* - default values, asigned on {@link PluginsLib::getDefaultArguments()}
	* - request values, sended by GET or POST method, if $request is put to true
	* - explicit values, asigned on the Wiki
	* @param array sended to wikiplugin_name($data, $params)
	* @param bool if set to true, accept values from $_REQUEST
	* @param bool if set to true, assign default values from {@link PluginsLib::getDefaultArguments()}
	* @return array list of params
	*/
	function getParams($params, $request = false, $defaults = false) {
	    if ($defaults === false) {
	        $defaults = $this->getDefaultArguments();
	    }
	    $args = array();
	    foreach ($defaults as $arg => $default_val) {
	        if (isset($params[$arg])) {
	            $args[$arg] = $params[$arg];
	        } elseif(isset($_REQUEST[$arg])) {
	            $args[$arg] = $_REQUEST[$arg];
	        } else {
	            // maybe this kind of transformation can be grouped on a external function
	            if ($default_val==="[pagename]") {
	                $default_val=$_REQUEST["page"];
	            }
	            $args[$arg] = $default_val;
	        }
	        if (in_array($arg, $this->expanded_params)) {
	            if ($args[$arg]) {
	            $args[$arg] = explode($this->separator, $args[$arg]);
	            foreach($args[$arg] as $id=>$value) {
	                $args[$arg][$id]=trim($value);
	            }
	            } else {
	            $args[$arg]=array();
	            }
	        } 
	    }
	    return $args;
	}
	/**
	* Returns the name of the Plugin
	* By default, erase the first 'WikiPlugin'
	* Made for overload it.
	* @return string
	*/
	function getName() {
	    return preg_replace('/^WikiPlugin/', '', get_class($this));
	}
	/**
	* Returns a description of the Plugin
	* Made for overload it.
	* @return string
	*/
	function getDescription() {
	    return $this->getName();
	}
	/**
	* Returns the version of the version
	* Made for overload it.
	* @return string
	*/
	function getVersion() {
	    return tra("No version indicated");
	    //return preg_replace("/[Revision: $]/", '',
	    //                    "\$Revision: 1.12 $");
	}
	/**
	* Returns the default arguments for the plugin
	* Use keys as the arguments and values as ... the default values
	* @return array
	*/
	function getDefaultArguments() {
	    return array('description' => $this->getDescription());
	}
	/**
	* Run the plugin
	* For sake of God, overload it!
	* @param string
	* @param array
	*/
	function run ($data, $params) {
	    /**
	    * UGLY ERROR!.
	    */
	    return $this->error("PluginsLib::run: pure virtual function. Don't be so lazy!");
	}
	function error ($message) {
	    return "~np~<span class='warn'>".tra("Plugin ").$this->getName()." ".tra("failed")." : ".tra($message)."</span>~/np~";
	}
	function getErrorDetail() {
	    return $this->_errors;
	}
	function _error($message) {
	    $this->_errors = $message;
	    return false;
	}
}
/**
* Class with utilities for Plugins
*/
class PluginsLibUtil
{
	/**
	* Create a table with information from pages
	* @param array key ["data"] from one of the functions that retrieve informaci�n about pages
	* @param array list of keys to show.
	* @param array definition of the principal field. By default: 
	*              array("field"=>"pageName","name"=>"Page")
	* @return string
	*/
	function createTable($aData,$aInfo=false,$aPrincipalField=false) {
	    // contract
	    if (!$aPrincipalField or !is_array($aPrincipalField)) {
	        $aPrincipalField=array("field"=>"pageName","name"=>"Page");
	    }
	    if (!is_array($aInfo)) {
	        $aInfo=false;
	    }
	    // ~contract
	    $sOutput = '';
	    if ($aInfo) {
	        $iNumCol=count($aInfo)+1;
	        $sStyle = '';

	        if (in_array('parameters',$aInfo)) {
	        	$sOutput .= '<em>Required parameters are in</em> <b>bold</b><br />';
	        }
	        // Header for info
	        $sOutput  .= '<table class="normal">' . "\n\t" . '<tr>' . "\n\t\t" . '<td class="heading"' . $sStyle. '>' 
	        	. tra($aPrincipalField['name']) . '</td>';
	        foreach($aInfo as $iInfo => $sHeader) {
	        	if ($sHeader == 'paraminfo') {
	        		$sHeader = 'Parameter Info';
	        	}
	            $sOutput  .= "\n\t\t" . '<td class="heading"' . $sStyle . '>' . ucfirst(tra($sHeader)) . '</td>';
	        }
	        $sOutput  .= "\n\t" . '</tr>';
	    }
	    $iCounter=1;
	    //Primary row
	    foreach($aData as $aPage) {
	    	$rowspan = '';
	    	if ($aPrincipalField['field'] == 'plugin') {
	    		$openlink = '';
	    		$closelink = '';
	    	} else {
	    		$openlink = '((';
	    		$closelink = '))';
	    	}
	        if (!$aInfo) {
	            $sOutput  .= '*' . $openlink . $aPage[$aPrincipalField['field']] . $closelink . "\n";
	        //First column
	        } elseif (isset($aPage[$aPrincipalField['field']])) {
		        if (is_array($aPage[$aPrincipalField['field']])) {
		        	$fieldval = $aPage[$aPrincipalField['field']][$aPrincipalField['field']];
	        		if (isset($aPage[$aPrincipalField['field']]['rowspan']) && $aPage[$aPrincipalField['field']]['rowspan'] != 0) {
	        			$rowspan = ' rowspan="' . $aPage[$aPrincipalField['field']]['rowspan'] . '" ';
	        		} else {
	        			$rowspan = '';
	        		}
		        } else {
		        	$fieldval = $aPage[$aPrincipalField['field']];
		        	$rowspan = '';
		        }
				$sClass = ($iCounter%2) ? 'odd' : 'even';
		        $sOutput .= "\n\t" . '<tr>' . "\n\t\t" . '<td class="' . $sClass . '"' . $rowspan . '>' 
	            			. $openlink . $fieldval . $closelink . '</td>';
	            $colcounter = 2;
	            //Subsequent columns
	            foreach($aInfo as $sInfo) {
	                if (isset($aPage[$sInfo])) {
	                	if (is_array($aPage[$sInfo])) {
	                		$rowspan2 = '';
	                		if (isset($aPage[$sInfo]['rowspan']) && $aPage[$sInfo]['rowspan'] > 0) {
	                			$rowspan2 = ' rowspan="' . $aPage[$sInfo]['rowspan'] . '" ';
	                			$pcount = count($aPage[$sInfo]) - 1;
	                		} else {
	                			$pcount = count($aPage[$sInfo]);
	                		}
	                		$i = $pcount;
	                		foreach ($aPage[$sInfo] as $sInfokey => $sInfoitem) {
	                			//Potential sub-rows
				        		if ($i < $pcount && strpos($sInfokey, 'rowspan') === false) {
				        			$begrow = "\n\t" . '<tr>';
				        			$endrow = "\n\t" . '</tr>';
				        		} else {
				        			$begrow = '';
				        			if ($colcounter == $iNumCol && strpos($sInfokey, 'rowspan') === false) {
				        				$endrow = "\n\t" . '</tr>';
				        			} else {
				        				$endrow = '';
				        			}
				        		}
	                		//Ignore field added to hold rowspan
				        		if (strpos($sInfokey, 'rowspan') !== false) {
				        			$sOutput .= '';
				        		} else {
				        			$sOutput .= $begrow . "\n\t\t" . '<td class="' . $sClass . '"' . $rowspan2 . '>';
				        			if (strpos($sInfokey, 'onekey') !== false) {
				        				$sOutput .= $sInfoitem;
				        			} else {
				        				$sOutput .= $sInfokey;
				        			}
				        			$sOutput .= '</td>';
				        			if (in_array('paraminfo',$aInfo) && $sInfo == 'parameters') {
				        				$sOutput .= "\n\t\t" . '<td class="' . $sClass . '">';
				        				if (count($aPage['parameters']) > 0) {
				        					$sOutput .= $sInfoitem;
					        			} 
					        			$sOutput .= '</td>';
					        		}
					        	}
				        		$sOutput .= $endrow;
			        			$i--;
	                		}
	                		$colcounter++;
	                	} else {
		                    $sOutput  .= "\n\t\t" . '<td class="' . $sClass . '">' . $aPage[$sInfo] . '</td>';
		                    if ($colcounter == $iNumCol) {
		                    	$sOutput  .= "\n\t" . '</tr>';
		                    }
		                    $colcounter++;
	                	}
	                }
	            }
	        }
	    	$iCounter++;
	    }
        if ($aInfo) {
            $sOutput  .= '</table>';
		}
	return $sOutput;
	}
	
	function createList($aData) {
	    $aPrincipalField=array("field"=>"pageName","name"=>"Pages");
	
	    // Header for info
	    $sOutput = "<table class='normal'><tr><td class='heading'>".tra($aPrincipalField["name"])."</td></tr><tr><td class='even'>";
	    $iCounter=0;		
	    // create a comma separated list of entries
	    foreach($aData as $aPage) {
	      if ($iCounter>0) $sOutput .= ", ";
	      $sOutput  .= "((".$aPage[$aPrincipalField["field"]]."))";
	      $iCounter++;
	    }
	      $sOutput .= "</td></tr></table>";
	    return $sOutput;
	}
}
