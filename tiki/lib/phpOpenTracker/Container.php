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
// $Id: Container.php,v 1.2 2003-05-12 16:35:07 lechuckdapirate Exp $
//

require_once POT_INCLUDE_PATH . 'Parser.php';

/**
* phpOpenTracker Session Container
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.2 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_Container {
  /**
  * Returns a reference to phpOpenTracker's session container.
  *
  * The first call to this method during a request triggers, if
  * $parameters['init'] is not set to false, the generation of
  * the session container array.
  *
  * @param  optional array $parameters
  * @return array
  * @access public
  * @static
  */
  function &singleton($parameters = array()) {
    static $isInitialized;

    $init          = isset($parameters['init'])        ? $parameters['init']        : false;
    $initAPI       = isset($parameters['initAPI'])     ? $parameters['initAPI']     : false;
    $initNoSetup   = isset($parameters['initNoSetup']) ? $parameters['initNoSetup'] : false;
    $isInitialized = isset($isInitialized)             ? $isInitialized             : false;

    if ($initAPI) {
      $init          = true;
      $isInitialized = false;
    }

    else if ($initNoSetup) {
      $init = true;
    }

    if ($isInitialized && !$init) {
      return $_SESSION['_phpOpenTracker_Container'];
    }

    if ($init && !$initAPI) {
      session_register('_phpOpenTracker_Container');
      $container = &$_SESSION['_phpOpenTracker_Container'];
    }

    if ($init && !$isInitialized && !$initNoSetup) {
      $config = &phpOpenTracker_Config::singleton();
      $db     = &phpOpenTracker_DB::singleton();

      if (!isset($container['first_request'])) {
        $container['first_request'] = true;
      } else {
        $container['first_request'] = false;
      }

      if ($container['first_request']) {
        $container['client_id'] = isset($parameters['client_id']) ? $parameters['client_id'] : 1;

        if (function_exists('posix_getpid')) {
          $container['accesslog_id'] = crc32(microtime() . posix_getpid());
        } else {
          $container['accesslog_id'] = crc32(microtime() . session_id());
        }

        if (!isset($parameters['host']) &&
            !isset($parameters['ip_address'])) {
          $container['ip_address'] = $_SERVER['REMOTE_ADDR'];

          if (isset($_SERVER['REMOTE_HOST'])) {
            $container['host_orig'] = $_SERVER['REMOTE_HOST'];
          } else {
            if ($config['resolve_hostname']) {
              $container['host_orig'] = gethostbyaddr($container['ip_address']);
            } else {
              $container['host_orig'] = $container['ip_address'];
            }
          }
        } else {
          $container['host_orig']  = isset($parameters['host'])       ? $parameters['host']       : '';
          $container['ip_address'] = isset($parameters['ip_address']) ? $parameters['ip_address'] : '';
        }

        $container['host'] = $container['host_orig'];

        if ($config['group_hostnames']) {
          $container['host'] = phpOpenTracker_Parser::hostname($container['host']);
        }

        $container['user_agent_orig'] = isset($parameters['user_agent']) ? $parameters['user_agent'] : $_SERVER['HTTP_USER_AGENT'];

        if ($config['group_user_agents']) {
          $container = array_merge(
            $container,
            phpOpenTracker_Parser::userAgent($container['user_agent_orig'])
          );
        } else {
          $container['operating_system'] = 'Not identified';
          $container['user_agent']       = $container['user_agent_orig'];
        }

        if (isset($parameters['referer'])) {
          $container['referer_orig'] = $parameters['referer'];
        } else {
          $container['referer_orig'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        }

        $container['referer_orig'] = urldecode($container['referer_orig']);

        if ($config['clean_referer_string']) {
          $container['referer'] = preg_replace('/(\?.*)/', '', $container['referer_orig']);
        } else {
          $container['referer'] = $container['referer_orig'];
        }

        if (!empty($container['referer'])) {
          $container['referer'] = str_replace('http://', '', $container['referer']);
        }

        $container['host_id'] = $db->storeIntoDataTable(
          $config['hostnames_table'],
          $container['host']
        );

        $container['referer_id'] = $db->storeIntoDataTable(
          $config['referers_table'],
          $container['referer']
        );

        $container['operating_system_id'] = $db->storeIntoDataTable(
          $config['operating_systems_table'],
          $container['operating_system']
        );

        $container['user_agent_id'] = $db->storeIntoDataTable(
          $config['user_agents_table'],
          $container['user_agent']
        );
      } else {
        $container['last_document'] = $container['document'];
      }

      $container['document_url'] = isset($parameters['document_url']) ? $parameters['document_url'] : urldecode($_SERVER[$config['document_env_var']]);

      if ($config['clean_query_string']) {
        $container['document_url'] = preg_replace(
          '/(\?.*)/',
          '',
          $container['document_url']
        );
      } else {
        $filters = array_merge(
          $config['get_parameter_filter'],
          array(
            session_name()
          )
        );

        foreach ($filters as $filter) {
          $container['document_url'] = preg_replace(
            '#\?' .
            $filter .
            "=.{32}$|&" .
            $filter .
            "=.{32}$|" .
            $filter .
            '=.{32}&#msiU',
            '',
            $container['document_url']
          );
        }
      }

      $container['document']    = isset($parameters['document']) ? $parameters['document'] : $container['document_url'];
      $container['document_id'] = $db->storeIntoDataTable(
        $config['documents_table'],
        $container['document'],
        $container['document_url']
      );

      $container['timestamp'] = isset($parameters['timestamp']) ? $parameters['timestamp'] : time();

      $isInitialized = true;
    }

    return $container;
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
