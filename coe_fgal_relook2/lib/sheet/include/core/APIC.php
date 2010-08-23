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

APIClass::import("org.apicnet.xml.Xml");

/**
* Classe principal d'APICFrameWorks ; Elle premet d'importer n'import quelle autre class
* Ce code est tirer du projet jphp lib qui est malheureusement clos.
*
* Cette class ne s'instancie pas elle s'utilise de maniere static de cette facon :
* 
*	1.	require_once("conf/config.inc.php");
*	2.	$secure = APIC::loadClass(chemin de la class);
*	3.	$secure->methode(); 
* 	ou 
* 	1.	require_once("conf/config.inc.php");
*	2.	APIC::import(chemin de la class);
* 
*
* @update $Date: 2005-05-18 11:01:22 $
* @version 1.0
* @author diogène MOULRON <logiciel@apicnet.net>
* @package core
*/ 
class APIC extends Object {
	
	/**
	* Méthode static d'importation de class;
	*
	* @param string $class nom de la class a importer ; espace de nommage identique a celle utilisée en java (sous la forme org.apicnet.maclass).
	* @param string $module nom du module ou trouver la class a charger
 	* @access public
	* @since 1.1
	* @update 06/06/2002
	*/
	function import($class, $module=NULL){
		$class = preg_split('/,/', preg_replace('/\s{1,}/', '', $class));
		$len = count($class);
		for ($i =0; $i<$len; $i++){
			APIClass::import($class[$i], $module);
		}
	}
	
	
	/**
	 * APIC::importData()
	 * 
	 * @param $class
	 * @param unknown $module
	 * @return 
	 **/
	function importData($class, $module=NULL){
		$packagename = APIClassRegistry::extractPackageName($class);
		$path        = APIClassRegistry::convertToPath($class, $module);
		if (APIClassRegistry::isClass($class)){// extracting class name
			$classname = APIClassRegistry::extractClassName($class);
			// loadclass
			$path .= '.php';
			if (isset($path) && is_file($path)){// register
				APIClassRegistry::register($packagename, $classname);
				include($path);
			}
		}
	}
	
	/**
	*  
	*
	* @access private
	* @since 1.1
	* @update 06/06/2002
	*/
	function _debug(){
		APIClassRegistry::_debug();
	} // end func
	
	/**
	*  
	*
	* @access private
	* @since 1.1
	* @update 06/06/2002
	*/
	function isRegistered($class){
		return APIClassRegistry::isRegistered($class);
	} // end func
	
	/**
	* 
	* Méthode static d'instanciation de class / object;
	* 
	* 
	*
	* @param string  $class nom de class à charger et à instancier ; espace de nommage identique a celle utilisée en java.
	* @param array   $parameters parametres de la class s'il y en a.
	* @param boolean $new_class indique si, lorsque la class est déjà dans le registre, on doit en instancier une nouvelle.
	* @return object un pointeur sur la class instancié
	* @access public
	* @since 1.1
	* @update 06/06/2002
	*/
	function &loadClass($class, $parameters=NULL, $new_class=FALSE, $module=NULL){
		
		APIC::import("org.apicnet.io.File"); 
		
		if ($new_class){
		//	echo(" OK <br>");
			$classname = APIClassRegistry::extractClassName($class);
			if (!APIClassRegistry::isClass($class) || !APIClassRegistry::isRegistered($class)){
				$path = new File(APIClassRegistry::convertToPath($class, $module).'.php');
		//		echo($path->getFilePath()."<br>");
				if ($path->exists()){
					APIC::import($class, $module);
				} else{
					die("Class ".$class." cannot be found");
				}
			}
			
			$params = '';
			if ($parameters!=NULL && is_array($parameters)){
				$plen = count($parameters);
				if ($plen>0){
					for ($i=0; $i < $plen; $i++){
						$params .= '$parameters['.$i.']';
						if ($i<($plen-1)){
							$params .= ', ';
						}
					}
				}
			}
			$obj = NULL;
			eval('$obj = new '.$classname.'('.$params.');');
			APIClassRegistry::registerClass($class, $obj);
			return $obj;
		
		} else { // fin du if New_class
		//	APIC::_debug();
			if (APIClassRegistry::isRegistered($class)) {
		//		echo($class." loaded ...");
				return APIClassRegistry::loadClass($class, $parameters);
			} else {
				if (!APIClassRegistry::isClass($class) || !APIClassRegistry::isRegistered($class)){
					$path = new File(APIClassRegistry::convertToPath($class, $module).'.php');
		//			echo($path->getFilePath()."<br>");
					if ($path->exists()){
		//				echo($class." instanciate ...");
						APIC::import($class, $module);
						return APIC::loadClass($class, $parameters, TRUE, $module);
					} else {
						die("Class ".$class." cannot be found");
					}
				}
			}
		}
	} // end func
	
	/**
	*
	* 
	*
	* @access private
	* @since 1.1
	* @update 06/06/2002
	*/
	function _display_core_package(){
		print("<font style='font-family: Courier New; font-size: 9pt; color:#ff3300'>");
		print("<ol><li><strong>core</strong>");
		print("<ol>");
		APIC::listDir(JPHP_CORE_DIR);
		print("</ol>");
		print("</li></ol>");
		print("</font>");
	} // end func
	
	/**
	*
	* 
	*
	* @access private
	* @since 1.1
	* @update 06/06/2002
	*/
	function _display_packages(){
		print("<font style='font-family: Courier New; font-size: 9pt; color:#006600'>");
		APIC::listDir(JPHP_LIBRARY_PATH);
		print("</font>");
	} // end func
	
	/**
	* 
	*
	*
	* @param string $path chemin du répertoire à lister.
	* @access public
	* @since 1.1
	* @update 06/06/2002
	*/
	function listDir($path){
		if (file_exists($path) && is_dir($path)){
			print("<ol>");
			$handle=opendir($path);
			while ($file = readdir($handle)) {
				if ($file!='.' && $file!='..'){
					$fullpath = $path . '/' . $file;
					if (is_dir($fullpath)){
						print("<li><strong>".$file."</strong>");
						print("<ol>");
						APIC::listDir($fullpath);
						print("</ol>");
					} else if (preg_match('/(\.php)$/i', $file)>0){
						print("<li>".str_replace('.php','',$file)."</li>");
					}
				}
			}
			print("</ol>");
		} else {
			print("<li>classes library directory cannot be found</li>");
		}
	} // end func
}// end class
