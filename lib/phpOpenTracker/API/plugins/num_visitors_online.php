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
// $Id: num_visitors_online.php,v 1.1 2003-04-25 18:43:54 lrargerich Exp $
//

require_once POT_INCLUDE_PATH . 'API/Plugin.php';

/**
* phpOpenTracker API - Number of Visitors Online
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_API_num_visitors_online extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array('num_visitors_online');

  /**
  * API Type
  *
  * @var string $apiType
  */
  var $apiType = 'get';

  /**
  * Runs the phpOpenTracker API call.
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  */
  function run($parameters) {
    $parameters['session_lifetime'] = isset($parameters['session_lifetime']) ? $parameters['session_lifetime'] : 3;

    $this->db->query(
      sprintf(
        'SELECT COUNT(DISTINCT(accesslog.accesslog_id)) AS visitors
           FROM %s accesslog,
                %s visitors
          WHERE accesslog.client_id    = %d
            AND accesslog.accesslog_id = visitors.accesslog_id
            AND accesslog.timestamp   >= %d
                %s',

        $this->config['accesslog_table'],
        $this->config['visitors_table'],
        $parameters['client_id'],
        time() - ($parameters['session_lifetime'] * 60),
        $this->_constraint($parameters['constraints'])
      )
    );

    if ($row = $this->db->fetchRow()) {
      return intval($row['visitors']);
    } else {
      return false;
    }
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
