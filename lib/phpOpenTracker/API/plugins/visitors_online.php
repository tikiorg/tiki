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
// $Id: visitors_online.php,v 1.1 2003-04-25 18:43:59 lrargerich Exp $
//

require_once POT_INCLUDE_PATH . 'API/Plugin.php';

/**
* phpOpenTracker API - Visitors Online
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_API_visitors_online extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array('visitors_online');

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

    switch ($parameters['result_format']) {
      case 'xml':
      case 'xml_object': {
        $tree     = new XML_Tree;
        $root     = &$tree->addRoot('visitorsonline');
        $children = array();
      }
      break;

      default: {
        $result = array();
      }
    }

    $accesslogIDs = array();

    $this->db->query(
      sprintf(
        'SELECT DISTINCT(accesslog.accesslog_id) AS accesslog_id
           FROM %s accesslog
          WHERE accesslog.client_id  = %d
            AND accesslog.timestamp >= %d',

        $this->config['accesslog_table'],
        $parameters['client_id'],
        time() - ($parameters['session_lifetime'] * 60)
      )
    );

    while ($row = $this->db->fetchRow()) {
      $accesslogIDs[] = $row['accesslog_id'];
    }

    for ($i = 0, $max = sizeof($accesslogIDs); $i < $max; $i++) {
      switch ($parameters['result_format']) {
        case 'xml':
        case 'xml_object': {
          $visitorNode = &$root->addChild('visitor');

          $visitorNode->addChild(
            phpOpenTracker::get(
              array(
                'client_id'     => $parameters['client_id'],
                'api_call'      => 'individual_clickpath',
                'accesslog_id'  => $accesslogIDs[$i],
                'result_format' => 'xml_object'
              )
            )
          );
        }
        break;

        default: {
          $result[$i]['clickpath'] = phpOpenTracker::get(
            array(
              'client_id'    => $parameters['client_id'],
              'api_call'     => 'individual_clickpath',
              'accesslog_id' => $accesslogIDs[$i]
            )
          );
        }
      }

      $this->db->query(
        sprintf(
          "SELECT MAX(timestamp) as last_access
             FROM %s
            WHERE accesslog_id = '%s'",

          $this->config['accesslog_table'],
          $accesslogIDs[$i]
        )
      );

      if ($row = $this->db->fetchRow()) {
        switch ($parameters['result_format']) {
          case 'xml':
          case 'xml_object': {
            $visitorNode->addChild('last_access', $row['last_access']);
          }
          break;

          default: {
            $result[$i]['last_access'] = $row['last_access'];
          }
        }
      } else {
        return phpOpenTracker::handleError(
          'Database query failed.'
        );
      }

      $this->db->query(
        sprintf(
          'SELECT hosts.string       AS host,
                  user_agents.string AS user_agent
             FROM %s visitors,
                  %s hosts,
                  %s user_agents
            WHERE visitors.accesslog_id  = %d
              AND visitors.host_id       = hosts.data_id
              AND visitors.user_agent_id = user_agents.data_id',

          $this->config['visitors_table'],
          $this->config['hostnames_table'],
          $this->config['user_agents_table'],
          $accesslogIDs[$i]
        )
      );

      if ($row = $this->db->fetchRow()) {
        switch ($parameters['result_format']) {
          case 'xml':
          case 'xml_object': {
            $visitorNode->addChild('host',       $row['host']);
            $visitorNode->addChild('user_agent', $row['user_agent']);
          }
          break;

          default: {
            $result[$i]['host']       = $row['host'];
            $result[$i]['user_agent'] = $row['user_agent'];
          }
        }
      } else {
        return phpOpenTracker::handleError(
          'Database query failed.'
        );
      }

      $this->db->query(
        sprintf(
          'SELECT referers.string AS referer
             FROM %s visitors,
                  %s referers
            WHERE visitors.accesslog_id = %d
              AND visitors.referer_id   = referers.data_id',

          $this->config['visitors_table'],
          $this->config['referers_table'],
          $accesslogIDs[$i]
        )
      );

      if ($row = $this->db->fetchRow()) {
        $referer = $row['referer'];
      } else {
        $referer = '';
      }

      switch ($parameters['result_format']) {
        case 'xml':
        case 'xml_object': {
          $visitorNode->addChild('referer', $referer);
        }
        break;

        default: {
          $result[$i]['referer'] = $referer;
        }
      }
    }

    switch ($parameters['result_format']) {
      case 'xml': {
        return $root->get();
      }
      break;

      case 'xml_object': {
        return $root;
      }
      break;

      default: {
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
