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
// |         Oliver Lehmann <oliver@phpOpenTracker.de>                   |
// +---------------------------------------------------------------------+
//
// $Id: page_impressions.php,v 1.2 2003-05-12 16:34:55 lechuckdapirate Exp $
//

require_once POT_INCLUDE_PATH . 'API/Plugin.php';

/**
* phpOpenTracker API - Page Impressions
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.2 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_API_page_impressions extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array('page_impressions');

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
    $parameters['interval'] = isset($parameters['interval']) ? $parameters['interval'] : false;

    $intervalStrings = array();
    $timestamps      = array();
    $values          = array();

    switch ($parameters['result_format']) {
      case 'csv': {
        $csv = "Interval;Page Impressions\n";
      }
      break;

      case 'xml':
      case 'xml_object': {
        $tree = new XML_Tree;
        $root = &$tree->addRoot('pageimpressions');
      }
      break;

      default: {
        $result = array();
      }
    }

    if ($parameters['interval'] != false) {
      $start = $parameters['start'] ? $parameters['start'] : 0;
      $end   = $parameters['end']   ? $parameters['end']   : time();

      for ($i = $parameters['start']; $i < $parameters['end']; $i += $parameters['interval']) {
        $correct = ((mktime(0, 0, 0, date('m', $i), date('d', $i) + 1, date('Y', $i))
                   - mktime(0, 0, 0, date('m', $i), date('d', $i),     date('Y', $i)))
                   * ($parameters['interval'] / 86400))
                   - $parameters['interval'];

        $intervalStrings[] = sprintf(
          '%s - %s',

          date('d-m-Y', $i),
          date('d-m-Y', $i + $parameters['interval'] + $correct)
        );

        $values[] = phpOpenTracker::get(
          array(
            'client_id'   => $parameters['client_id'],
            'api_call'    => 'page_impressions',
            'start'       => $i,
            'end'         => $i + $parameters['interval'] + $correct,
            'constraints' => $parameters['constraints']
          )
        );

        $timestamps[] = $i;

        $i += $correct;
      }
    } else {
      $this->db->query(
        sprintf(
          'SELECT COUNT(*) AS page_impressions
             FROM %s accesslog,
                  %s visitors
            WHERE accesslog.client_id    = %d
              AND accesslog.accesslog_id = visitors.accesslog_id
                  %s
                  %s',

          $this->config['accesslog_table'],
          $this->config['visitors_table'],
          $parameters['client_id'],
          $this->_constraint($parameters['constraints']),
          $this->_whereTimerange(
            $parameters['start'],
            $parameters['end'],
            'accesslog'
          )
        )
      );

      if ($row = $this->db->fetchRow()) {
        $values = array(intval($row['page_impressions']));
      } else {
        $values = array(0);
      }

      if ($parameters['start'] != false &&
          $parameters['end']   != false) {
        $intervalStrings = array(
          sprintf(
            '%s - %s',

            date('d-m-Y', $parameters['start']),
            date('d-m-Y', $parameters['end'])
          )
        );
      } else {
        $intervalStrings = array('');
      }
    }

    switch ($parameters['result_format']) {
      case 'csv': {
        for ($i = 0; $i < sizeof($values); $i++) {
          $csv .= sprintf(
            "%s;%d\n",

            $intervalStrings[$i],
            $values[$i]
          );
        }

        return $csv;
      }
      break;

      case 'xml':
      case 'xml_object': {
        for ($i = 0; $i < sizeof($values); $i++) {
          $intervalChild = &$root->addChild('interval');

          $intervalChild->addChild('interval', $intervalStrings[$i]);
          $intervalChild->addChild('value',    $values[$i]);
        }

        if ($parameters['result_format'] == 'xml') {
          return $root->get();
        } else {
          return $root;
        }
      }
      break;

      default: {
        if (sizeof($values) == 1) {
          return $values[0];
        } else {
          $result = array();

          for ($i = 0; $i < sizeof($values); $i++) {
            $result[] = array(
              'timestamp' => $timestamps[$i],
              'value'     => $values[$i]
            );
          }
        }

        return $result;
      }
    }
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
