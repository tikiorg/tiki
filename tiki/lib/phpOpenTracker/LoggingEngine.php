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
// $Id: LoggingEngine.php,v 1.1 2003-04-25 18:43:52 lrargerich Exp $
//

/**
* phpOpenTracker Logging Engine
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_LoggingEngine {
  /**
  * Configuration
  *
  * @var array $config
  */
  var $config = array();

  /**
  * Container
  *
  * @var array $container
  */
  var $container = array();

  /**
  * DB
  *
  * @var object $db
  */
  var $db;

  /**
  * Plugins
  *
  * @var array $plugins
  */
  var $plugins = array();

  /**
  * Constructor.
  *
  * @param  optional array $parameters
  * @access public
  */
  function phpOpenTracker_LoggingEngine($parameters = array()) {
    $parameters['init'] = true;

    $this->config    = &phpOpenTracker_Config::singleton();
    $this->container = &phpOpenTracker_Container::singleton($parameters);
    $this->db        = &phpOpenTracker_DB::singleton();

    $this->_loadPlugins();
  }

  /**
  * Logs an access.
  *
  * @param  array   $addData
  * @return boolean
  * @access public
  */
  function log($addData) {
    if ($this->_isLocked()) {
      return false;
    }

    if ($this->config['track_returning_visitors'] &&
        !empty($this->config['returning_visitors_cookie'])) {
      $cookie = sprintf(
        '%s_%d',

        $this->config['returning_visitors_cookie'],
        $this->container['client_id']
      );
    } else {
      $cookie = false;
    }

    if ($this->container['first_request']) {
      if ($cookie &&
          isset($_COOKIE[$cookie])) {
        $this->container['visitor_id']        = $_COOKIE[$cookie];
        $this->container['returning_visitor'] = true;
      } else {
        $this->container['visitor_id']        = $this->container['accesslog_id'];
        $this->container['returning_visitor'] = false;

        if ($cookie) {
          @setcookie(
            $cookie,
            $this->container['visitor_id'],
            $this->container['timestamp'] + ($this->config['returning_visitors_cookie_lifetime'] * 24 * 60 * 60)
          );
        }
      }

      $this->_storeVisitData($addData);
      $this->_storeRequestData();
    } else {
      if ($this->config['log_reload'] ||
          $this->container['document'] != $this->container['last_document']) {
        $this->_storeRequestData();
      }
    }

    return true;
  }

  /**
  * Checks if a locking rule applies to this visitor's data.
  *
  * @return boolean true, if a locking rule applies and the
  *                 current request must not be counted.
  * @access private
  */
  function _isLocked() {
    if ($this->config['locking']) {
      if ($rules = @file(POT_CONFIG_PATH . 'lock.ini', 1)) {
        foreach ($rules as $rule) {
          $field   = substr($rule, 0, strpos($rule, ' '));
          $pattern = chop(substr($rule, strpos($rule, ' ') + 1));

          if (substr($field, 0, 1) != '#' &&
              preg_match($pattern, $this->container[$field . '_orig'])) {
            return true;
          }
        }
      } else {
        return phpOpenTracker::handleError(
          sprintf(
            'Could not open "%s", Locking disabled.',

            POT_CONFIG_PATH . 'lock.ini'
          )
        );
      }
    }

    $result = false;

    foreach ($this->plugins as $plugin) {
      if (!$plugin->pre()) {
        $result = true;
      }
    }

    return $result;
  }

  /**
  * Loads the phpOpenTracker Logging Engine plugins.
  *
  * @access private
  */
  function _loadPlugins() {
    foreach ($this->config['logging_engine_plugins'] as $pluginName) {
      if (@include(POT_INCLUDE_PATH . 'LoggingEngine/plugins/' . $pluginName . '.php')) {
        $pluginClass     = 'phpOpenTracker_LoggingEngine_Plugin_' . $pluginName;
        $this->plugins[] = new $pluginClass;
      } else {
        phpOpenTracker::handleError(
          sprintf(
            'Could not load plugin "%s".',

            $pluginName
          )
        );
      }
    }
  }

  /**
  * Runs the phpOpenTracker Logging Engine 'post' plugins.
  *
  * @access private
  */
  function _runPostPlugins() {
    $addData = array();

    foreach ($this->plugins as $plugin) {
      $addData = array_merge($addData, $plugin->post());
    }

    if ($this->container['first_request']) {
      $this->db->storeAddData($this->container['accesslog_id'], $addData);
    }
  }

  /**
  * Stores the request information.
  *
  * @access private
  */
  function _storeRequestData() {
    if (!$this->container['first_request']) {
      $this->_runPostPlugins();
    }

    $this->db->query(
      sprintf(
        "INSERT
           INTO %s
                (client_id,   accesslog_id,
                 document_id, timestamp,
                 entry_document)
         VALUES (%d, %d,
                 %d, %d,
                 '%d')",

        $this->config['accesslog_table'],
        $this->container['client_id'],
        $this->container['accesslog_id'],
        $this->container['document_id'],
        $this->container['timestamp'],
        $this->container['first_request'] ? 1 : 0
      )
    );
  }

  /**
  * Stores the visit information.
  *
  * @param  array   $addData
  * @access private
  */
  function _storeVisitData(&$addData) {
    $this->_runPostPlugins();

    $this->db->query(
      sprintf(
        "INSERT
           INTO %s
                (client_id,           accesslog_id,  visitor_id,
                 operating_system_id, user_agent_id, host_id,
                 referer_id,          timestamp,     returning_visitor)
         VALUES (%d, %d, %d,
                 %d, %d, %d,
                 %d, %d, '%d')",

        $this->config['visitors_table'],
        $this->container['client_id'],
        $this->container['accesslog_id'],
        $this->container['visitor_id'],
        $this->container['operating_system_id'],
        $this->container['user_agent_id'],
        $this->container['host_id'],
        $this->container['referer_id'],
        $this->container['timestamp'],
        $this->container['returning_visitor'] ? 1 : 0
      )
    );

    $this->db->storeAddData($this->container['accesslog_id'], $addData);
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
