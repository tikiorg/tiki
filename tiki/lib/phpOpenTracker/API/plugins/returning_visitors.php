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
// $Id: returning_visitors.php,v 1.2 2003-05-12 16:35:01 lechuckdapirate Exp $
//

require_once POT_INCLUDE_PATH . 'API/Plugin.php';

/**
* phpOpenTracker API - Returning Visitors
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.2 $
* @since    phpOpenTracker 1.0.5
*/
class phpOpenTracker_API_returning_visitors extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array('returning_visitors');

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
    switch ($parameters['result_format']) {
      case 'xml':
      case 'xml_object': {
        $tree = new XML_Tree;
        $root = &$tree->addRoot('returningVisitors');
      }
      break;

      default: {
        $visitors = array();
      }
    }

    $this->db->query(
      sprintf(
        'SELECT visitors.accesslog_id AS accesslog_id,
                visitors.visitor_id   AS visitor_id,
                visitors.timestamp    AS timestamp
           FROM %s accesslog,
                %s visitors
          WHERE accesslog.client_id    = %d
            AND accesslog.accesslog_id = visitors.accesslog_id
                %s
                %s
          ORDER BY visitors.visitor_id,
                   visitors.timestamp',

        $this->config['accesslog_table'],
        $this->config['visitors_table'],
        $parameters['client_id'],
        $this->_constraint($parameters['constraints']),
        $this->_whereTimerange(
          $parameters['start'],
          $parameters['end']
        )
      )
    );

    while ($row = $this->db->fetchRow()) {
      $visitors[$row['visitor_id']]['accesslog_ids'][] = $row['accesslog_id'];
      $visitors[$row['visitor_id']]['timestamps'][]    = $row['timestamp'];
    }

    if (!empty($visitors)) {
      $keys    = array_keys($visitors);
      $numKeys = sizeof($keys);

      for ($i = 0; $i < $numKeys; $i++) {
        $visitors[$keys[$i]]['num_visits'] = sizeof($visitors[$keys[$i]]['accesslog_ids']);
        $visitors[$keys[$i]]['visitor_id'] = $keys[$i];

        if ($visitors[$keys[$i]]['num_visits'] > 1) {
          $time = 0;

          for ($j = 0; $j < $visitors[$keys[$i]]['num_visits'] - 1; $j++) {
            $time += ($visitors[$keys[$i]]['timestamps'][$j+1] -
                      $visitors[$keys[$i]]['timestamps'][$j]);
          }

          $visitors[$keys[$i]]['average_time_between_visits'] = floor($time / ($visitors[$keys[$i]]['num_visits'] - 1));
        } else {
          unset ($visitors[$keys[$i]]);
        }
      }

      if (!empty($visitors)) {
        foreach($visitors as $visitor) $tmp[] = $visitor['num_visits'];
        array_multisort($tmp, SORT_DESC, $visitors);
      }
    }

    switch ($parameters['result_format']) {
      case 'xml':
      case 'xml_object': {
        $numVisitors = sizeof($visitors);

        for ($i = 0; $i < $numVisitors; $i++) {
          $visitorChild = &$root->addChild('visitor');
          $visitsChild  = &$visitorChild->addChild('visits');

          $numVisits = sizeof($visitors[$i]['accesslog_ids']);

          for ($j = 0; $j < $numVisits; $j++) {
            $visitChild = &$visitsChild->addChild('visit');

            $visitChild->addChild('accesslogID', $visitors[$i]['accesslog_ids'][$j]);
            $visitChild->addChild('timestamp',   $visitors[$i]['timestamps'][$j]);
          }

          $visitorChild->addChild('numVisits', $visitors[$i]['num_visits']);
          $visitorChild->addChild('visitorID', $visitors[$i]['visitor_id']);
        }

        if ($parameters['result_format'] == 'xml') {
          return $root->get();
        } else {
          return $root;
        }
      }
      break;

      default: {
        return $visitors;
      }
    }
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
