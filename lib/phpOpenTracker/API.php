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
// $Id: API.php,v 1.2 2003-05-12 16:34:49 lechuckdapirate Exp $
//

/**
* phpOpenTracker API
*
* This class provides an interface to the data gathered by
* the phpOpenTracker Logging Engine.
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.2 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_API {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array();

  /**
  * Cache_Lite
  *
  * @var object $cache
  */
  var $cache = null;

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
  * Plugins
  *
  * @var array $plugins
  */
  var $plugins = array();

  /**
  * Constructor.
  *
  * @access public
  */
  function phpOpenTracker_API() {
    $this->config = &phpOpenTracker_Config::singleton();

    if ($this->config['query_cache']) {
      @include_once 'Cache/Lite.php';

      if (class_exists('Cache_Lite')) {
        $this->cache = new Cache_Lite(
          array(
            'cacheDir' => $this->config['query_cache_dir'],
            'lifeTime' => $this->config['query_cache_lifetime']
          )
        );
      } else {
        phpOpenTracker::handleError(
          'Could not find PEAR Cache_Lite package, Query Cache disabled.'
        );
      }
    }

    $this->_loadPlugins();
  }

  /**
  * Singleton.
  *
  * @return object  phpOpenTracker_API
  * @access public
  * @static
  */
  function &singleton() {
    static $instance;

    if (!isset($instance)) {
      $instance = new phpOpenTracker_API;
    }

    return $instance;
  }

  /**
  * get() is the main method of the phpOpenTracker API. You pass it an
  * array, which defines the type of information you want. Please refer to
  * the chapter 'API Reference' of the phpOpenTracker Manual for detailed
  * information and examples.
  *
  * @param  array   $parameters
  * @return mixed
  * @access public
  */
  function &get($parameters) {
    if (!$this->_parseParameters($parameters)) {
      return false;
    }

    $doCache = false;

    if (is_object($this->cache)) {
      ksort($parameters);

      $cacheID      = md5(serialize($parameters));
      $cachedResult = $this->cache->get($cacheID, 'phpOpenTracker');

      if ($cachedResult != NULL) {
        return unserialize($cachedResult);
      }

      if ($parameters['start'] != false && $parameters['start'] < time() &&
          $parameters['end']   != false && $parameters['end']   < time()) {
        $doCache = true;
      }
    }

    if (isset($this->apiCalls['get'][$parameters['api_call']])) {
      $result = $this->apiCalls['get'][$parameters['api_call']]->run($parameters);
    } else {
      return phpOpenTracker::handleError(
        sprintf(
          'Unknown API Call %s.',

          $parameters['api_call']
        )
      );
    }

    if ($doCache && $result !== false) {
      $this->cache->save(serialize($result), $cacheID, 'phpOpenTracker');
    }

    return $result;
  }

  /**
  * Plot a chart.
  *
  * @param  array   $parameters
  * @access public
  */
  function plot($parameters) {
    if (!$this->_parseParameters($parameters)) {
      return false;
    }

    if (empty($this->config['jpgraph_path'])) {
      return phpOpenTracker::handleError(
        'The JPGraph package is not installed, exiting.',
        E_USER_ERROR
      );
    }

    include_once $this->config['jpgraph_path'] . 'jpgraph.php';
    include_once $this->config['jpgraph_path'] . 'jpgraph_line.php';
    include_once $this->config['jpgraph_path'] . 'jpgraph_pie.php';
    include_once $this->config['jpgraph_path'] . 'jpgraph_pie3d.php';

    $parameters['color1']     = isset($parameters['color1'])     ? $parameters['color1']     : 'red';
    $parameters['color2']     = isset($parameters['color2'])     ? $parameters['color2']     : 'blue';
    $parameters['font']       = isset($parameters['font'])       ? $parameters['font']       : FF_VERDANA;
    $parameters['font_style'] = isset($parameters['font_style']) ? $parameters['font_style'] : FS_NORMAL;
    $parameters['font_size']  = isset($parameters['font_size'])  ? $parameters['font_size']  : 10;
    $parameters['width']      = isset($parameters['width'])      ? $parameters['width']      : 640;
    $parameters['height']     = isset($parameters['height'])     ? $parameters['height']     : 480;

    if (isset($this->apiCalls['plot'][$parameters['api_call']])) {
      // Prevent caching
      header('Expires: Sat, 22 Apr 1978 02:19:00 GMT');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: no-store, no-cache, must-revalidate');
      header('Cache-Control: post-check=0, pre-check=0', false);
      header('Pragma: no-cache');

      $this->apiCalls['plot'][$parameters['api_call']]->run($parameters);
    } else {
      return phpOpenTracker::handleError(
        sprintf(
          'Unknown API Call %s.',

          $parameters['api_call']
        )
      );
    }
  }

  /**
  * Checks if a given phpOpenTracker API plugin is loaded.
  *
  * @param  string  $plugin
  * @return boolean
  * @access public
  * @static
  */
  function pluginLoaded($plugin) {
    $api = &phpOpenTracker_API::singleton();

    return in_array(
      $plugin,
      $api->plugins
    );
  }

  /**
  * Loads the phpOpenTracker API plugins.
  *
  * @access private
  */
  function _loadPlugins() {
    if ($dir = @opendir(POT_INCLUDE_PATH . 'API/plugins')) {
      while (($file = @readdir($dir)) !== false) {
        if (strstr($file, '.php')) {
          if (@include(POT_INCLUDE_PATH . 'API/plugins/' . $file)) {
            $this->plugins[] = substr($file, 0, -4);

            $class  = 'phpOpenTracker_API_' . substr($file, 0, -4);
            $plugin = new $class;

            foreach ($plugin->apiCalls as $apiCall) {
              if (!isset($this->apiCalls[$apiCall])) {
                $this->apiCalls[$plugin->apiType][$apiCall] = $plugin;
              } else {
                phpOpenTracker::handleError(
                  sprintf(
                    'API Call "%s" already registered.',

                    $apiCall
                  )
                );
              }
            }
          } else {
            phpOpenTracker::handleError(
              sprintf(
                'Cannot load plugin "%s".',

                substr($file, 0, -4)
              )
            );
          }
        }
      }

      @closedir($dir);
    }
  }

  /**
  * Parses the parameters of an phpOpenTracker API call.
  *
  * @param  array   $parameters
  * @return boolean
  * @access private
  */
  function _parseParameters(&$parameters) {
    if (!is_array($parameters) || !isset($parameters['api_call'])) {
      return phpOpenTracker::handleError(
        'No array was passed to API Call, or "api_call" field missing.'
      );
    }

    if (array_search('this', $parameters, true) !== false) {
      $this->container = &phpOpenTracker_Container::singleton(
        array(
          'initAPI' => true
        )
      );
    }

    if (!isset($parameters['client_id'])) {
      $parameters['client_id'] = isset($this->container['client_id']) ? $this->container['client_id'] : 1;
    }

    $parameters['start']         = isset($parameters['start'])         ? $parameters['start']         : false;
    $parameters['end']           = isset($parameters['end'])           ? $parameters['end']           : false;
    $parameters['constraints']   = isset($parameters['constraints'])   ? $parameters['constraints']   : array();
    $parameters['limit']         = isset($parameters['limit'])         ? $parameters['limit']         : false;
    $parameters['order']         = isset($parameters['order'])         ? $parameters['order']         : 'DESC';
    $parameters['range']         = isset($parameters['range'])         ? $parameters['range']         : 'total';
    $parameters['range_start']   = isset($parameters['range_start'])   ? $parameters['range_start']   : 0;
    $parameters['range_length']  = isset($parameters['range_length'])  ? $parameters['range_length']  : 0;
    $parameters['result_format'] = isset($parameters['result_format']) ? $parameters['result_format'] : false;

    if (!$parameters['start'] && !$parameters['end']) {
      if ($parameters['range'] != 'total') {
        $timerange = $this->_timerange(
          $parameters['range'],
          $parameters['range_start'],
          $parameters['range_length']
        );

        if ($timerange) {
          $parameters['start'] = $timerange[0];
          $parameters['end']   = $timerange[1];
        } else {
          return false;
        }
      }
    }

    foreach ($parameters['constraints'] as $field => $value) {
      if (is_string($value)) {
        if ($value == 'this') {
          $parameters['constraints'][$field] = $this->container[$field . '_id'];
        } else {
          $parameters['constraints'][$field] = crc32(strtolower($value));
        }
      }
    }

    if (stristr($parameters['result_format'], 'xml')) {
      if (!@include_once('XML/Tree.php')) {
        phpOpenTracker::handleError(
          'Could not find PEAR XML_Tree package, exiting.',
          E_USER_ERROR
        );
      }
    }

    return true;
  }

  /**
  * Parses plain-text timeranges into timestamp intervals.
  *
  * @param  string            $range
  * @param  optional  integer $start
  * @param  optional  integer $length
  * @return array
  * @access private
  */
  function _timerange($range, $start = 0, $length = 0) {
    switch ($range) {
      case 'current_minute': {
        return $this->_timerange('minute');
      }
      break;

      case 'previous_minute': {
        return $this->_timerange('minute', 1, 1);
      }
      break;

      case 'current_hour': {
        return $this->_timerange('hour');
      }
      break;

      case 'previous_hour': {
        return $this->_timerange('hour', 1, 1);
      }
      break;

      case 'today': {
        return $this->_timerange('day');
      }
      break;

      case 'yesterday': {
        return $this->_timerange('day', 1, 1);
      }
      break;

      case 'current_month': {
        return $this->_timerange('month');
      }
      break;

      case 'previous_month': {
        return $this->_timerange('month', 1, 1);
      }
      break;

      case 'current_year': {
        return $this->_timerange('year');
      }
      break;

      case 'previous_year': {
        return $this->_timerange('year', 1, 1);
      }
      break;

      case 'minute': {
        $start = mktime(date('H'), date('i') - $start,   0);
        $end   = mktime(date('H'), date('i') - $length, 59);
      }
      break;

      case 'hour': {
        $start = mktime(date('H') - $start,  0,   0);
        $end   = mktime(date('H') - $length, 59, 59);
      }
      break;

      case 'day': {
        $start = mktime( 0,  0,  0, date('m'), date('d') - $start);
        $end   = mktime(23, 59, 59, date('m'), date('d') - $length);
      }
      break;

      case 'month': {
        $start = mktime( 0,  0,  0, date('m') - $start, 1);
        $end   = mktime(23, 59, 59, date('m') - $length, date('t', mktime(0, 0, 0, date('m') - $length)));
      }
      break;

      case 'year': {
        $start = mktime( 0,  0,  0,  1,  1, date('Y') - $start);
        $end   = mktime(23, 59, 59, 12, 31, date('Y') - $length);
      }
      break;

      default: {
        return phpOpenTracker::handleError(
          'Syntax error in timerange specification.'
        );
      }
    }

    return array(
      $start,
      $end
    );
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
