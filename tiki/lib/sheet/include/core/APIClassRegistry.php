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

/**
* Classe principal d'APICFrameWorks ; elle premet d'importer n'import quelle autre class
* Ce code est tirer du projet jphp lib qui est malheureusement clos.
*
* Cette class ne s'instancie pas elle s'utilise de maniere static de cette facon :
*		require_once("conf/config.inc.php");
*		$secure = APIC::loadClass('org.apicnet.security.Auth');
*		$secure->secureIt(); <i>
* 	ou 	require_once("conf/config.inc.php");
*		APIC::import('org.apicnet.security.Auth');
* 
*
* @update $Date: 2005-05-18 11:01:38 $
* @version 1.0
* @author diogène MOULRON <logiciel@apicnet.net>
* @package core
*/ 
class APIClassRegistry extends ErrorManager{
	
	var $packages  = NULL;
	var $classes   = NULL;
	var $instances = NULL;
	
	/**
	* Class registry constructor
	* @access private
	*/
	function APIClassRegistry(){
		$this->packages = array();
		$this->classes = array();
		$this->instances = array();
		parent::ErrorManager();
	}
	
	
	/**
	* create a singleton instance of APIClassRegistry
	* @return	APIClassRegistry	a unique instance of a APIClassRegistry
	* @access	private static
	*/
	function & getInstance(){
		static $instance;
		if (!isset($instance)){
			$instance = new APIClassRegistry();
		}
		return $instance;
	}
	
	/**
	* Try to register the class/package 
	* @param	string	a class name or a package name
	* @return boolean TRUE if the class has been registered
	* @access public static
	*/
	function register($class_package, $class_class=NULL){
		static $registry;
		if (!isset($registry)) {
			$registry =& APIClassRegistry::getInstance();
		}
		if (isset($class_class)){
			$class_package.= '.'.$class_class;
		}
		$class_package = preg_replace('/([À-Ý]|[A-Z])/e','chr(ord(\'\\1\')+32)', $class_package);
		// determine is a class or is a package
		if (APIClassRegistry::isClass($class_package)){
			$extractedClassName = APIClassRegistry::extractClassName($class_package);
			$extractedPackageName = APIClassRegistry::extractPackageName($class_package);
			$registry->classes[$extractedClassName] = $extractedPackageName;
			$registry->instances[$extractedClassName] = NULL;
		}
		else if (APIClassRegistry::isPackage($class_package)){
			$registry->packages[$class_package] = $class_package;
		}
		return FALSE;
	}
	
	function registerClass($class_package, &$obj){
		static $registry;
		if (!isset($registry)) {
			$registry =& APIClassRegistry::getInstance();
		}
		$extractedClassName = strtolower(APIClassRegistry::extractClassName($class_package));
		$registry->instances[$extractedClassName] = $obj;
	}
	
	function &loadClass($class_package, $parameters){
		static $registry;
		if (!isset($registry)) {
			$registry =& APIClassRegistry::getInstance();
		}
		$class_package      = preg_replace('/([À-Ý]|[A-Z])/e','chr(ord(\'\\1\')+32)', $class_package);
		$extractedClassName = strtolower(APIClassRegistry::extractClassName($class_package));
		if (isset($registry->instances[$extractedClassName])){
		//	echo("OK ");
		//	echo(" chargement effectué avec succès de la class ".$registry->instances[$extractedClassName]->className()."<br>");
			return $registry->instances[$extractedClassName]; 
		} else {
		//	echo(" ....");
			return APIC::loadClass($class_package, $parameters, TRUE);
		}
	}
	
	
	/**
	*	check if the parameter string is a package definition
	*	@param	string|core.StringBuffer	a class name or a package name
	*	@return boolean		TRUE if it's a package definition
	*	@access public static
	*/
	function isPackage($package){
		return $package[strlen($package)-1]==='*' && $package[strlen($package)-2]==='.';
	}
	
	/**
	*	check if the parameter string is a class definition
	*	@param	string|core.StringBuffer	a class name or a package name
	*	@return boolean		TRUE if it's a package definition
	*	@access public static
	*/
	function isClass($package){
		return isset($package) ? $package[strlen($package)-1]!=='*' : FALSE;
	}
	
	/**
	* try to extract a class name from a package call
	* @param string|core.StringBuffer	a class name or a package name
	* @return core.StringBuffer	the extracted class name if extraction is a success or NULL if fail to extract
	* @access public static
	*/
	function extractClassName($package){
		$array = explode('.', $package);
		return $array[count($array)-1];
	}
	
	/**
	* try to extract a package name from a package call
	* @param string|core.StringBuffer	a class name or a package name
	* @return core.StringBuffer	the extracted package name if extraction is a success or NULL if fail to extract
	* @access public static
	*/
	function extractPackageName($package){
		if (APIClassRegistry::isPackage($package)){
			return substr($package, 0, strlen($package)-2);
		}else{
			$pos = strrpos($package, '.');
			if ($pos>0){
				return substr($package, 0, $pos);
			}else{
				return $package;
			}
		}
		return NULL;
	}
	
	/**
	* check if the class/package is already registered
	* @param string|core.StringBuffer	a class name or a package name
	* @return boolean	TRUE if the class/package is already registered
	* @access public
	*/
	function isRegistered($class_package, $class_class=NULL){
		static $registry;
		// get unique instance
		if (!isset($registry)) {
		$registry =& APIClassRegistry::getInstance();
		}
		if (isset($class_class) || APIClassRegistry::isClass($class_package)){
			if (!isset($class_class)){
			$class_class = APIClassRegistry::extractClassName($class_package);
			}
			$class_class = preg_replace('/([À-Ý]|[A-Z])/e','chr(ord(\'\\1\')+32)', $class_class);
			return isset($registry->classes[$class_class]);
		}else{
			$class_package = preg_replace('/([À-Ý]|[A-Z])/e','chr(ord(\'\\1\')+32)', $class_package);
			return isset($registry->packages[$class_package]);
		}
	}
	
	/**
	* Convert to real path
	*/ 
	// ajouter une variable permettant de distinguer les imports du core et ceux des modules en utilisant APIC_MODULE_PATH
	function convertToPath($package_name, $modulePath=NULL, $class_name=NULL){
		$package_name = preg_replace('/(\*?)$/', '', $package_name);
		$package_name = strtr($package_name, '*.',' /');
		$package_name = str_replace(str_repeat(DIRECTORY_SEPARATOR,2), '.'.DIRECTORY_SEPARATOR, $package_name);
		if (isset($modulePath))	{
			$package_name = APATH_MODULE_PATH . $modulePath ."/include/" . $package_name;
		} else {
			$package_name = APIC_LIBRARY_PATH . $package_name;
		}
			
		if (isset($class_name)){
			$package_name .= '/'.$class_name.'.php';
		}
		
		return $package_name;
	}
	/**
	* display all class and import that have been register
	* @return void
	* @access public static
	*/
	function _debug(){
		static $registry;
		// get unique instance
		if (!isset($registry)) {
			$registry =& APIClassRegistry::getInstance();
		}
		print("<font style='font-family: Courier New; font-size: 9pt; color:#0000ff'><strong>Packages importation :</strong><ol>");
		$keys = array_keys($registry->packages);
		$len = count($keys);
		if ($len>0){
			for ($i=0; $i<$len; $i++){
				$pos = strpos($keys[$i], 'core.');
				if ($pos!==FALSE && $pos===0){
					print("<li><font color=#ff3300><strong>".$keys[$i]."</strong></font>");
				} else{
					print("<li>".$keys[$i]);
				}
			}
		} else {
			print("<li> no existance of package import");
		}
		print("</ol></font>");
		print("<font style='font-family: Courier New; font-size: 9pt; color:#0000ff'><strong>Classes importation :</strong><ol>");
		$keys = array_keys($registry->classes);
		$len = count($keys);
		if ($len>0){
			for ($i=0; $i<$len; $i++){
				$class = $registry->classes[$keys[$i]].".<b>".$keys[$i]."</b>";
				$pos = strpos($class, 'core.');
				if ($pos!==FALSE && $pos===0){
					print("<li><font color=#ff3300>".$class."</font>");
				} else {
					print("<li>".$class);
				}
			}
		} else {
			print("<li> no existance of class import");
		}
		print("</ol></font>");
	}
}
?>
