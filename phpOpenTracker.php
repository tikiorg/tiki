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
// $Id: phpOpenTracker.php,v 1.1 2003-04-25 18:38:06 lrargerich Exp $
//

if (!defined('POT_INCLUDE_PATH')) {
  define('POT_INCLUDE_PATH', dirname(__FILE__) . '/lib/phpOpenTracker/');
}

if (!defined('POT_CONFIG_PATH')) {
  define('POT_CONFIG_PATH', POT_INCLUDE_PATH  . 'conf/');
}

require_once POT_INCLUDE_PATH . 'Config.php';
require_once POT_INCLUDE_PATH . 'Container.php';
require_once POT_INCLUDE_PATH . 'DB.php';
require_once POT_INCLUDE_PATH . 'Version.php';

/**
* phpOpenTracker
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker {
  /**
  * Wrapper for phpOpenTracker_API::get().
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  * @static
  */
  function &get($parameters) {
    include_once POT_INCLUDE_PATH . 'API.php';

    $api = &phpOpenTracker_API::singleton();

    return $api->get($parameters);
  }

  /**
  * Handles an error according to the debug_level setting.
  *
  * @param           string  $errorMessage
  * @param  optional integer $errorType
  * @return boolean
  * @access public
  * @static
  */
  function handleError($errorMessage, $errorType = E_USER_WARNING) {
    $config = &phpOpenTracker_Config::singleton();

    $prefix = 'phpOpenTracker ' . (($errorType == E_USER_ERROR) ? 'Error' : 'Warning') . ': ';

    if ($config['debug_level'] > 0) {
      echo $prefix . $errorMessage;
    }

    if ($config['log_errors']) {
      @error_log(
        sprintf(
          "%s: %s\n",

          date('d-m-Y H:i:s', time()),
          $errorMessage
        ),

        3,
        dirname(__FILE__) . '/' . 'error.log'
      );
    }

    if ($config['exit_on_fatal_errors'] && $errorType == E_USER_ERROR) {
      exit;
    }

    return false;
  }

  /**
  * Invokes the phpOpenTracker Logging Engine.
  *
  * @param  optional array $parameters
  * @return boolean
  * @access public
  * @static
  */
  function log($parameters = array()) {
    include_once POT_INCLUDE_PATH . 'LoggingEngine.php';

    $le = new phpOpenTracker_LoggingEngine($parameters);

    return $le->log(
      isset($parameters['add_data']) ? $parameters['add_data'] : array()
    );
  }

  /**
  * Wrapper for phpOpenTracker_API::plot().
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  * @static
  */
  function plot($parameters) {
    include_once POT_INCLUDE_PATH . 'API.php';

    $api = &phpOpenTracker_API::singleton();

    $api->plot($parameters);
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
