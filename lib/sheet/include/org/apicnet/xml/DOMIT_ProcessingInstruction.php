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

class DOMIT_ProcessingInstruction extends DOMIT_Node {
	function DOMIT_ProcessingInstruction($target, $data) {
		$this->_constructor();	
		$this->nodeType = DOMIT_PROCESSING_INSTRUCTION_NODE;
		$this->nodeName = $target;
		$this->nodeValue = $data;
	} //DOMIT_ProcessingInstruction	
	
	function getTarget() {
		return $this->nodeName;
	} //getTarget
	
	function getData() {
		return $this->nodeValue;
	} //getData
	
	function &createClone() {
		$className = get_class($this);
		$clone =& new $className($this->nodeValue, $this->nodeValue);
		
		return $clone;
	} //createClone
	
	function toString() {
		return "<?" . $this.nodeName . " " . $this->nodeValue . "?>";
	} //toString
} //DOMIT_ProcessingInstruction

?>