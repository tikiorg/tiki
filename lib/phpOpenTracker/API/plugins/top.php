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
// $Id: top.php,v 1.1 2003-04-25 18:43:57 lrargerich Exp $
//

require_once POT_INCLUDE_PATH . 'API/Plugin.php';

/**
* phpOpenTracker API - Top
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_API_top extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array('top');

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
    if (!isset($parameters['what'])) {
      return phpOpenTracker::handleError(
        'Required parameter "what" missing.'
      );
    }

    list($constraint, $selfJoin) = $this->_constraint(
      $parameters['constraints'],
      true
    );

    if ($selfJoin) {
      $selfJoinConstraint = 'AND accesslog.accesslog_id = accesslog2.accesslog_id';

      $selfJoinTable = sprintf(
        '%s accesslog2,',

        $this->config['accesslog_table']
      );
    } else {
      $selfJoinConstraint = '';
      $selfJoinTable      = '';
    }

    $timerange = $this->_whereTimerange(
      $parameters['start'],
      $parameters['end']
    );

    switch ($parameters['result_format']) {
      case 'csv': {
        $csv = "Rank;Item;Count;Percent\n";
      }
      break;

      case 'xml':
      case 'xml_object': {
        $tree = new XML_Tree;
        $root = &$tree->addRoot('top');
      }
      break;

      case 'separate_result_arrays': {
        $names   = array();
        $values  = array();
        $percent = array();
      }
      break;

      default: {
        $topItems = array();
      }
    }

    switch ($parameters['what']) {
      case 'document':
      case 'exit_target':
      case 'host':
      case 'operating_system':
      case 'referer':
      case 'user_agent':
      case 'entry_document':
      case 'exit_document': {
        $dataConstraint = '';

        switch ($parameters['what']) {
          case 'document': {
            $column    = 'accesslog.document_id';
            $dataTable = $this->config['documents_table'];
          }
          break;

          case 'entry_document': {
            $column         = 'accesslog.document_id';
            $dataConstraint = "AND accesslog.entry_document = '1'";
            $dataTable      = $this->config['documents_table'];
          }
          break;

          case 'exit_document': {
            $column         = 'accesslog.document_id';
            $dataConstraint = "AND accesslog.exit_target_id <> 0";
            $dataTable      = $this->config['documents_table'];
          }
          break;

          case 'exit_target': {
            $column         = 'accesslog.exit_target_id';
            $dataConstraint = "AND accesslog.exit_target_id <> 0";
            $dataTable      = $this->config['exit_targets_table'];
          }
          break;

          case 'host': {
            $column    = 'visitors.host_id';
            $dataTable = $this->config['hostnames_table'];
          }
          break;

          case 'operating_system': {
            $column    = 'visitors.operating_system_id';
            $dataTable = $this->config['operating_systems_table'];
          }
          break;

          case 'referer': {
            $column    = 'visitors.referer_id';
            $dataTable = $this->config['referers_table'];
          }
          break;

          case 'user_agent': {
            $column    = 'visitors.user_agent_id';
            $dataTable = $this->config['user_agents_table'];
          }
          break;
        }

        $nestedQuery = sprintf(
          "SELECT data_table.string AS item
             FROM %s accesslog,
                  %s
                  %s visitors,
                  %s data_table
            WHERE visitors.client_id    = %d
              AND visitors.accesslog_id = accesslog.accesslog_id
              AND data_table.data_id    = %s
                  %s
                  %s
                  %s
                  %s
            GROUP BY visitors.accesslog_id,
                     data_table.string",

          $this->config['accesslog_table'],
          $selfJoinTable,
          $this->config['visitors_table'],
          $dataTable,
          $parameters['client_id'],
          $column,
          $selfJoinConstraint,
          $dataConstraint,
          $constraint,
          $timerange
        );
      }
      break;

      default: {
        $nestedQuery = sprintf(
          "SELECT add_data.data_value AS item
             FROM %s accesslog,
                  %s
                  %s visitors,
                  %s add_data
            WHERE visitors.client_id     = %d
              AND visitors.accesslog_id  = accesslog.accesslog_id
              AND accesslog.accesslog_id = add_data.accesslog_id
              AND add_data.data_field    = '%s'
                  %s
                  %s
                  %s
            GROUP BY visitors.accesslog_id,
                     add_data.data_value",

          $this->config['accesslog_table'],
          $selfJoinTable,
          $this->config['visitors_table'],
          $this->config['additional_data_table'],
          $parameters['client_id'],
          $parameters['what'],
          $selfJoinConstraint,
          $constraint,
          $timerange
        );
      }
    }

    if ($this->db->supportsNestedQueries()) {
      $queryTotalUnique = sprintf(
        'SELECT COUNT(item)           AS total_items,
                COUNT(DISTINCT(item)) AS unique_items
           FROM (%s) items',

        $nestedQuery
      );

      $queryItems = sprintf(
        'SELECT COUNT(item) AS item_count,
                item
           FROM (%s) items
          GROUP BY item
          ORDER BY item_count %s,
                   item',

        $nestedQuery,
        $parameters['order']
      );
    } else {
      if ($this->config['db_type'] == 'mysql') {
        $dropTemporaryTable = true;

        $this->db->query(
          sprintf(
            'CREATE TEMPORARY TABLE pot_temporary_table %s',

            $nestedQuery
          )
        );

        $queryTotalUnique = sprintf(
          'SELECT COUNT(item)           AS total_items,
                  COUNT(DISTINCT(item)) AS unique_items
             FROM pot_temporary_table',

          $nestedQuery
        );

        $queryItems = sprintf(
          'SELECT COUNT(item) AS item_count,
                  item
             FROM pot_temporary_table
            GROUP BY item
            ORDER BY item_count %s,
                     item',

          $parameters['order']
        );
      } else {
        return phpOpenTracker::handleError(
          'You need a database system capable of nested queries.',
          E_USER_ERROR
        );
      }
    }

    $this->db->query($queryTotalUnique);

    if ($row = $this->db->fetchRow()) {
      $totalItems  = intval($row['total_items']);
      $uniqueItems = intval($row['unique_items']);
    } else {
      return phpOpenTracker::handleError(
        'Database query failed.'
      );
    }

    if ($totalItems > 0) {
      $this->db->query($queryItems, $parameters['limit']);

      $i = 0;

      while ($row = $this->db->fetchRow()) {
        $percentValue = doubleval(
          number_format(
            ((100 * $row['item_count']) / $totalItems),
            2
          )
        );

        switch ($parameters['result_format']) {
          case 'csv': {
            $csv = sprintf(
              "%d;%s;%d;%d\n",

              $i+1,
              $row['item'],
              intval($row['item_count']),
              $percentValue
            );
          }
          break;

          case 'xml':
          case 'xml_object': {
            $itemChild = &$root->addChild('item');

            $itemChild->addChild('rank',    $i+1);
            $itemChild->addChild('string',  $row['item']);
            $itemChild->addChild('count',   intval($row['item_count']));
            $itemChild->addChild('percent', $percentValue);

            if (isset($row['document_url'])) {
              $itemChild->addChild('url',  $row['document_url']);
            }
          }
          break;

          case 'separate_result_arrays': {
            $names[$i]   = $row['item'];
            $values[$i]  = intval($row['item_count']);
            $percent[$i] = $percentValue;
          }
          break;

          default: {
            $topItems[$i]['count'  ] = intval($row['item_count']);
            $topItems[$i]['string' ] = $row['item'];
            $topItems[$i]['percent'] = $percentValue;

            if (isset($row['document_url'])) {
              $topItems[$i]['url' ] = $row['document_url'];
            }
          }
        }

        $i++;
      }
    }

    if (isset($dropTemporaryTable)) {
      $this->db->query('DROP TABLE pot_temporary_table');
    }

    switch ($parameters['result_format']) {
      case 'csv': {
        return $csv;
      }
      break;

      case 'xml':
      case 'xml_object': {
        $root->addChild('total',  $totalItems);
        $root->addChild('unique', $uniqueItems);

        switch ($parameters['result_format']) {
          case 'xml': {
            return $root->get();
          }
          break;

          case 'xml_object': {
            return $root;
          }
          break;
        }
      }
      break;

      case 'separate_result_arrays': {
        return array(
          $names,
          $values,
          $percent,
          $uniqueItems
        );
      }
      break;

      default: {
        return array(
          'top_items'    => $topItems,
          'unique_items' => $uniqueItems
        );
      }
    }
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
