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
// $Id: individual_clickpath.php,v 1.2 2003-05-12 16:34:50 lechuckdapirate Exp $
//

require_once POT_INCLUDE_PATH . 'API/Clickpath.php';
require_once POT_INCLUDE_PATH . 'API/Plugin.php';

/**
* phpOpenTracker API - Individual Clickpath
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.2 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_API_individual_clickpath extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array('individual_clickpath');

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
    if (!isset($parameters['accesslog_id'])) {
      return phpOpenTracker::handleError(
        'Required parameter "accesslog_id" missing.'
      );
    }

    $parameters['resolve_ids'] = isset($parameters['resolve_ids']) ? $parameters['resolve_ids'] : true;

    if ($parameters['resolve_ids']) {
      $this->db->query(
        sprintf(
          'SELECT data_values.string       AS document,
                  data_values.document_url AS document_url,
                  accesslog.timestamp      AS timestamp
             FROM %s accesslog,
                  %s data_values
            WHERE accesslog.accesslog_id = %d
              AND accesslog.document_id  = data_values.data_id
            ORDER BY timestamp',

          $this->config['accesslog_table'],
          $this->config['documents_table'],
          $parameters['accesslog_id']
        )
      );
    } else {
      $this->db->query(
        sprintf(
          'SELECT accesslog.document_id AS document,
                  accesslog.timestamp
             FROM %s accesslog
            WHERE accesslog.accesslog_id = %d
            ORDER BY timestamp',

          $this->config['accesslog_table'],
          $parameters['accesslog_id']
        )
      );
    }

    $i = 0;

    while ($row = $this->db->fetchRow()) {
      $documents[$i]    = $row['document'];
      $documentURLs[$i] = $row['document_url'];

      if (isset($previousTimestamp)) {
        $timeSpent[$i-1] = $row['timestamp'] - $previousTimestamp;
      }

      $previousTimestamp = $row['timestamp'];
      $i++;
    }

    if (!isset($documents)) {
      return new phpOpenTracker_Clickpath(array());
    }

    $timeSpent[sizeof($documents)-1] = 1;

    $clickpath = new phpOpenTracker_Clickpath(
      $documents,
      $documentURLs,
      $timeSpent
    );

    switch ($parameters['result_format']) {
      case 'graphviz': {
        return $clickpath->toGraph();
      }
      break;

      case 'graphviz_object': {
        return $clickpath->toGraph(true);
      }
      break;

      case 'xml': {
        return $clickpath->toXML();
      }
      break;

      case 'xml_object': {
        return $clickpath->toXML(true);
      }
      break;

      default: {
        return $clickpath;
      }
    }
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
