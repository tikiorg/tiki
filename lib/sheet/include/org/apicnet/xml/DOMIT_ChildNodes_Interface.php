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
APIC::import("org.apicnet.xml.DOMIT_Node");

class DOMIT_ChildNodes_Interface extends DOMIT_Node {
	//interface for appendChild, insertBefore, replaceChild, and removeChild
	function DOMIT_ChildNodes_Interface() {		
		DOMIT_DOMException::raiseException(DOMIT_ABSTRACT_CLASS_INSTANTIATION_ERR, 
			 "Cannot instantiate abstract class DOMIT_ChildNodes_Interface"); 
	} //DOMIT_ChildNodes_Interface
	
	function &appendChild(&$child) {
		if ($child->nodeType == DOMIT_DOCUMENT_FRAGMENT_NODE) {
			if ($child->hasChildNodes()) {
				$total = count($child->childNodes);
				
				for ($i = 0; $i < $total; $i++) {
					$currChild =& $child->childNodes[$i];
					$this->appendChild($currChild);
				}
			}
		}
		else {
			if (!($this->hasChildNodes())) {
				$this->childNodes[0] =& $child;
				$this->firstChild =& $child;
			}
			else {
				//remove $child if it already exists
				$index = $this->getChildNodeIndex($this->childNodes, $child);
				
				if ($index != -1) {
					$this->removeChild($child);
				}
				
				//append child
				$numNodes = count($this->childNodes);
				$prevSibling =& $this->childNodes[($numNodes - 1)];
				
				$this->childNodes[$numNodes] =& $child; 
				
				//set next and previous relationships
				$child->previousSibling =& $prevSibling;
				$prevSibling->nextSibling =& $child;
			}
	
			$this->lastChild =& $child;
			$child->parentNode =& $this;
			
			unset($child->nextSibling);
			$child->nextSibling = null;
		
			$child->setOwnerDocument($this);
		}
		
		return $child;
	} //appendChild
	
	function &insertBefore(&$newChild, &$refChild) {
		if (($refChild->nodeType == DOMIT_DOCUMENT_NODE) ||
			($refChild->parentNode->nodeType == DOMIT_DOCUMENT_NODE) || 
			($refChild->parentNode == null)) {
			
			DOMIT_DOMException::raiseException(DOMIT_NOT_FOUND_ERR, 
				 "Reference child not present in the child nodes list."); 
		}
		
		//if reference child is also the node to be inserted
		//leave the document as is and don't raise an exception
		if ($refChild->uid == $newChild->uid) {
			return $newChild;
		}
		
		//if $newChild is a DocumentFragment, 
		//loop through and insert each node separately
		if ($newChild->nodeType == DOMIT_DOCUMENT_FRAGMENT_NODE) {
			if ($newChild->hasChildNodes()) {
				$total = count($newChild->childNodes);
				
				for ($i = 0; $i < $total; $i++) {
					$currChild =& $newChild->childNodes[$i];
					$this->insertBefore($currChild, $refChild);
				}
			}
			
			return $newChild;
		}
		
		//remove $newChild if it already exists
		$index = $this->getChildNodeIndex($this->childNodes, $newChild);
		if ($index != -1) {
			$this->removeChild($newChild);
		}
	
		//find index of $refChild in childNodes
		$index = $this->getChildNodeIndex($this->childNodes, $refChild);
				
		if ($index != -1) {
			//reset sibling chain
			if ($refChild->previousSibling != null) {			
				$refChild->previousSibling->nextSibling =& $newChild;
				$newChild->previousSibling =& $refChild->previousSibling;
			}
			else {
				$this->firstChild =& $newChild;
			}
			
			$newChild->parentNode =& $refChild->parentNode;
			$newChild->nextSibling =& $refChild;
			$refChild->previousSibling =& $newChild;
			
			//add node to childNodes
			$i = count($this->childNodes);
	
			while ($i >= 0) {		
				if ($i > $index) {
					$this->childNodes[$i] =& $this->childNodes[($i - 1)];
				}
				else if ($i == $index) {
					$this->childNodes[$i] =& $newChild;
				}
				$i--;
			}
		}
		else {
			$this->appendChild($newChild);
		}
		
		return $newChild;
	} //insertBefore
	
	function &replaceChild(&$newChild, &$oldChild) {
		 if ($newChild->nodeType == DOMIT_DOCUMENT_FRAGMENT_NODE) { //if $newChild is a DocumentFragment
			//if $this is a DOMIT_Document, and the DocumentFragment
			//only has a single root node, replace documentElement with the first node 
			if (is_a($this, "DOMIT_Document")) {			
				if ($newChild->hasChildNodes() && (count($newChild->childNodes) == 1) && (is_a($newChild->firstChild, "DOMIT_Element"))) {
					$this->replaceChild($newChild->firstChild, $oldChild);
				}
				else {
				
					DOMIT_DOMException::raiseException(DOMIT_DOCUMENT_FRAGMENT_ERR, 
						("DOMIT_DocumentFragment can only be appended to DOMIT_Document" . 
						" if a single DOMIT_Element is present at its root level."));

				}			
			}
			else {
				//replace the first node then loop through and insert each node separately
				if ($newChild->hasChildNodes()) {
					$total = count($newChild->childNodes);
					
					if ($total > 0) {
						$newRef =& $newChild->lastChild;
						$this->replaceChild($newRef, $oldChild);
					
						for ($i = 0; $i < ($total - 1); $i++) {
							$currChild =& $newChild->childNodes[$i];
							$this->insertBefore($currChild, $newRef);
						}
					}
					else {
						$this->replaceChild($newChild->firstChild);
					}
				}
			}
			
			return $newChild;
		}
		else {
			if ($this->hasChildNodes()) { 
				//remove $newChild if it already exists
				$index = $this->getChildNodeIndex($this->childNodes, $newChild);
				if ($index != -1) {
					$this->removeChild($newChild);
				}
			
				//find index of $oldChild in childNodes
				$index = $this->getChildNodeIndex($this->childNodes, $oldChild);
				
				if ($index != -1) {
					$newChild->ownerDocument =& $oldChild->ownerDocument;
					$newChild->parentNode =& $oldChild->parentNode;
					
					//reset sibling chain
					if ($oldChild->previousSibling == null) {
						unset($newChild->previousSibling);
						$newChild->previousSibling = null;
					}
					else {
						$oldChild->previousSibling->nextSibling =& $newChild;
						$newChild->previousSibling =& $oldChild->previousSibling;
					}
					
					if ($oldChild->nextSibling == null) {
						unset($newChild->nextSibling);
						$newChild->nextSibling = null;
					}
					else {
						$oldChild->nextSibling->previousSibling =& $newChild;
						$newChild->nextSibling =& $oldChild->nextSibling;
					}
		
					$this->childNodes[$index] =& $newChild;
					
					if ($index == 0) $this->firstChild =& $newChild;
					if ($index == (count($this->childNodes) - 1)) $this->lastChild =& $newChild;
					
					return $newChild;
				}
			}
			
			DOMIT_DOMException::raiseException(DOMIT_NOT_FOUND_ERR, 
				("Reference node for replaceChild not found."));
		}
	} //replaceChild
	
	function &removeChild(&$oldChild) {
		if ($this->hasChildNodes()) { 
			//find index of $oldChild in childNodes
			$index = $this->getChildNodeIndex($this->childNodes, $oldChild);
				
			if ($index != -1) {
				//reset sibling chain
				if (($oldChild->previousSibling != null) && ($oldChild->nextSibling != null)) {
					$oldChild->previousSibling->nextSibling =& $oldChild->nextSibling;
					$oldChild->nextSibling->previousSibling =& $oldChild->previousSibling;			
				}
				else if (($oldChild->previousSibling != null) && ($oldChild->nextSibling == null)) {
					$this->lastChild =& $oldChild->previousSibling;
					unset($oldChild->previousSibling->nextSibling);
					$oldChild->previousSibling->nextSibling = null;
				}
				else if (($oldChild->previousSibling == null) && ($oldChild->nextSibling != null)) {
					unset($oldChild->nextSibling->previousSibling);
					$oldChild->nextSibling->previousSibling = null;			
					$this->firstChild =& $oldChild->nextSibling;
				}
				else if (($oldChild->previousSibling == null) && ($oldChild->nextSibling == null)) {
					unset($this->firstChild);
					$this->firstChild = null;					
					unset($this->lastChild);
					$this->lastChild = null;
				}
				
				$total = count($this->childNodes);

				//remove node from childNodes
				for ($i = 0; $i < $total; $i++) {
					if ($i == ($total - 1)) {
						array_splice($this->childNodes, $i, 1);
					}
					else if ($i >= $index) {
						$this->childNodes[$i] =& $this->childNodes[($i + 1)];
					}
				}

				return $oldChild;
			}
		}

		DOMIT_DOMException::raiseException(DOMIT_NOT_FOUND_ERR, 
				("Target node for removeChild not found."));
	} //removeChild
} //DOMIT_ChildNodes_Interface


?>