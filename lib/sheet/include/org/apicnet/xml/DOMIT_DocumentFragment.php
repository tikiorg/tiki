<?php
//*******************************************************************
//DOMIT! is a non-validating, but lightweight and fast DOM parser for PHP
//*******************************************************************
//by John Heinstein
//jheinstein@engageinteractive.com
//johnkarl@nbnet.nb.ca
//*******************************************************************
//Version 0.7
//copyright 2004 Engage Interactive
//http://www.engageinteractive.com/domit/
//All rights reserved
//*******************************************************************
//Licensed under the GNU General Public License (GPL)
//
//This program is free software; you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation; either version 2 of the License, or
//(at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//*******************************************************************
//see GPL details at http://www.gnu.org/copyleft/gpl.html
//and also in file license.txt included with DOMIT! 
//*******************************************************************
APIC::import("org.apicnet.xml.DOMIT_ChildNodes_Interface");

class DOMIT_DocumentFragment extends DOMIT_ChildNodes_Interface {

	function DOMIT_DocumentFragment() {
		$this->_constructor();	
		$this->nodeType = DOMIT_DOCUMENT_FRAGMENT_NODE;
		$this->nodeName ="#document-fragment";
		$this->nodeValue = null;
		$this->childNodes = array();
	} //DOMIT_DocumentFragment	
	
	function &createClone() {
		$className = get_class($this);
		$clone =& new $className($this->nodeName, $this->nodeValue);
		
		return $clone;
	} //createClone
	
	function toString() {
		//get children
		$returnString = "";
		$myNodes =& $this->childNodes;
		$total = count($myNodes);
		
		if ($total != 0) {
			for ($i = 0; $i < $total; $i++) {
				$child =& $myNodes[$i];
				$returnString .= $child->toString();
			}
		}
		
		return $returnString;
	} //toString
} //DOMIT_DocumentFragment

?>