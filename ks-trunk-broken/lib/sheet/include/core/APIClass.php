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

define("APIC_CORE_DIR",  APATH_ROOT."include/core/");
define("APIC_CACHE_DIR", CACHE_PATH);

require (APIC_CORE_DIR .  'Object.php');
require (APIC_CORE_DIR .  'ErrorManager.php');
require (APIC_CORE_DIR .  'APIClassRegistry.php');
require (APIC_CORE_DIR .  'APIC.php');

APIClassRegistry::register('core.*');
APIClassRegistry::register('core.ErrorManager');
APIClassRegistry::register('core.Object');
APIClassRegistry::register('core.APIClassRegistry');
APIClassRegistry::register('core.APIClass');
APIClassRegistry::register('core.APIC');

/**
 * APIClass
 * 
 * @package 
 * @author Diogene
 * @copyright Copyright (c) 2003
 * @version $Id: APIClass.php,v 1.3 2005-05-18 11:01:22 mose Exp $
 * @access public
 **/
class APIClass {
	
	/**
	 *	importing a unique class or an entire package
	 *	@param	string  a class name or a package name
	 *  @param	string $module nom du module ou trouver la class a charger
	 *	@return void
	 *	@access public
	 */
	function import($package, $module=NULL){
		// already registered ?
		if (APIClassRegistry::isRegistered($package)){
			return;
		}
		// extracting package name
		$packagename = APIClassRegistry::extractPackageName($package);
		$path        = APIClassRegistry::convertToPath($package, $module);
		if (APIClassRegistry::isPackage($package)){ // filtering php class and running parser
			if (isset($path) && is_dir($path)){
				APIClassRegistry::register($package);
				$handle=opendir($path);
				while ($file = readdir($handle)) {
					if ($file!=='.' && $file!=='..' && preg_match('/(\.php)$/i', $file)>0){
						require_once($path.$file);
						APIClassRegistry::register($packagename, substr($file,0,strlen($file)-4));
					}
				}
				closedir($handle);
			}
		} else if (APIClassRegistry::isClass($package)){// extracting class name
			$classname = APIClassRegistry::extractClassName($package);
			// loadclass
			$path .= '.php';
			if (isset($path) && is_file($path)){// register
				APIClassRegistry::register($packagename, $classname);
				require_once($path);
			}
		}
	} 
}
