<?php
//
// +---------------------------------------------------------------------+
// | phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
// +---------------------------------------------------------------------+
// | Copyright (c) 2000-2003 Sebastian Bergmann. All rights reserved.    |
// +---------------------------------------------------------------------+
// | This source file is subject to the phpOpenTracker Software License, |
// | Version 1.0, that is bundled with this package in the file LICENSE. |
// | If you did not receive a copy of this file, you may either read the |
// | license online at http://phpOpenTracker.de/license/1_0.txt, or send |
// | a note to license@phpOpenTracker.de, so we can mail you a copy.     |
// +---------------------------------------------------------------------+
// | Author: Sebastian Bergmann <sebastian@phpOpenTracker.de>            |
// +---------------------------------------------------------------------+
//
// $Id: Config.php,v 1.1 2003-04-25 18:43:51 lrargerich Exp $
//

/**
* phpOpenTracker Configuration Container
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_Config {
  /**
  * Loads the configuration.
  *
  * @return array
  * @access public
  * @static
  */
  function &singleton() {
    static $config;

    if (!isset($config)) {
      if (!$config = @parse_ini_file(POT_CONFIG_PATH . 'phpOpenTracker.ini')) {
        die('phpOpenTracker Error: Could not open ' . POT_CONFIG_PATH . 'phpOpenTracker.ini');
      }

      $config = array_change_key_case($config, CASE_LOWER);

      if ($config['debug_level'] > 1) {
        error_reporting(E_ALL);
      }

      if ($config['get_parameter_filter']) {
        $config['get_parameter_filter'] = explode(
          ',',
          str_replace(
            ' ',
            '',
            $config['get_parameter_filter']
          )
        );
      } else {
        $config['get_parameter_filter'] = array();
      }

      if ($config['logging_engine_plugins']) {
        $config['logging_engine_plugins'] = explode(
          ',',
          str_replace(
            ' ',
            '',
            $config['logging_engine_plugins']
          )
        );
      } else {
        $config['logging_engine_plugins'] = array();
      }
    }

    return $config;
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
