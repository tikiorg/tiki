<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
This file is part of J4PHP - Ensembles de propriétés et méthodes permettant le developpment rapide d'application web modulaire
Copyright (c) 2002-2004 @PICNet

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE
as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU LESSER GENERAL PUBLIC LICENSE for more details.

You should have received a copy of the GNU LESSER GENERAL PUBLIC LICENSE
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

/*
* SuperClass Object offrant toutes les fonctionnalités de gestion des objets tel que le nom de la class courante, le parent de la class courant s'il y a.
* Cette class permet aussi l'instanciation de la superClass général de gestion d'erreur ErrorManager
* 
* @module APIC
* @package core
* @update $Date: 2006-07-14 11:00:50 $
* @version 1.0
* @author diogène MOULRON <logiciel@apicnet.net>
* @see ErrorManager.php
*/ 
class Object{

	/**
	 * Constructeur : Object::Object()
	 * 
	 * @return 
	 **/
	function Object(){
	}
	
	function getNewId(){
		$char_list = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		for($i = 0; $i < 20; $i++){
			$sid .= substr($char_list, rand(0, strlen($char_list)), 1);
		}
		return md5($sid);
	}
	
	/**
	 * Object::className()
	 * 
	 * @return le nom de la class courante ayant fait appel a cette fonction
	 **/
	function className(){return get_class($this);}
	
	/**
	 * Object::getParentClass()
	 * 
	 * @param $object
	 * @return 
	 **/
	function getParentClass($object){return get_parent_class($object);}
	
	/**
	 * Object::equals()
	 * 
	 * @param $object
	 * @return 
	 **/
	function equals(&$object){
		if (Object::validClass($object)){
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Object::toString()
	 * 
	 * @return 
	 **/
	function toString(){return 'Object';}
	
	/**
	 * Object::serialize()
	 * 
	 * @return 
	 **/
	function serialize(){
		return serialize($this);
	}
	
	/**
	 * Object::serialize()
	 * 
	 * @return 
	 **/
	function load(){
	}
	
	/**
	 * Returns a copy of this object instance.
	 *
	 * @access	public
	 * @return	mixed
	 */
	function cloneNode(){	return $this;}
	
	/**
	 * Object::hashValue()
	 * 
	 * @return 
	 **/
	function hashValue(){return md5(serialize($this));}
	
	/**
	 * Object::validClass()
	 * 
	 * @param $object
	 * @param string $classname
	 * @return 
	 **/
	function validClass($object, $classname = 'object'){
		return (is_object($object) && ($object->className()==$classname || is_subclass_of($object, $classname)));
	}
}
?>
