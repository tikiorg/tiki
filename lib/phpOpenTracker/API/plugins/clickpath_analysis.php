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
// $Id: clickpath_analysis.php,v 1.2 2003-05-12 16:34:50 lechuckdapirate Exp $
//

require_once POT_INCLUDE_PATH . 'API/Clickpath.php';
require_once POT_INCLUDE_PATH . 'API/Plugin.php';

/**
* phpOpenTracker API - Clickpath Analysis
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.2 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_API_clickpath_analysis extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array(
    'all_paths',
    'top_paths',
    'longest_paths',
    'shortest_paths'
  );

  /**
  * API Type
  *
  * @var string $apiType
  */
  var $apiType = 'get';

  /**
  * Documents
  *
  * @var array $_documents
  */
  var $_documents = array();

  /**
  * Paths
  *
  * @var array $_paths
  */
  var $_paths = array();

  /**
  * Subpath Statistics
  *
  * @var array $_subpathStatistics
  */
  var $_subpathStatistics = array();

  /**
  * Runs the phpOpenTracker API call.
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  */
  function run($parameters) {
    if (!@include_once('Image/GraphViz.php')) {
      phpOpenTracker::handleError(
        'Could not find PEAR Image_GraphViz package, exiting.',
        E_USER_ERROR
      );
    }

    $parameters['document_color']    = isset($parameters['document_color'])    ? $parameters['document_color']    : 'black';
    $parameters['exit_targets']      = isset($parameters['exit_targets'])      ? $parameters['exit_targets']      : false;
    $parameters['exit_target_color'] = isset($parameters['exit_target_color']) ? $parameters['exit_target_color'] : 'red';
    $parameters['from']              = isset($parameters['from'])              ? crc32($parameters['from'])       : 0;
    $parameters['length']            = isset($parameters['length'])            ? $parameters['length']            : false;
    $parameters['referers']          = isset($parameters['referers'])          ? $parameters['referers']          : false;
    $parameters['referer_color']     = isset($parameters['referer_color'])     ? $parameters['referer_color']     : 'green';
    $parameters['result_format']     = isset($parameters['result_format'])     ? $parameters['result_format']     : 'graphviz';
    $parameters['subpaths']          = isset($parameters['subpaths'])          ? $parameters['subpaths']          : false;
    $parameters['to']                = isset($parameters['to'])                ? crc32($parameters['to'])         : 0;

    if ($parameters['api_call'] == 'all_paths') {
      $parameters['subpaths'] = true;
    }

    $this->_paths             = array();
    $this->_subpathStatistics = array();

    switch ($parameters['result_format']) {
      case 'graphviz':
      case 'graphviz_object': {
        $result = new Image_GraphViz;

        if ($parameters['exit_targets']) {
          $result->addCluster('exit_targets', 'Exit Targets');
        }

        if ($parameters['referers']) {
          $result->addCluster('referers', 'Referers');
        }
      }
      break;

      default: {
        $result = array();
      }
    }

    $accesslogID =  0;
    $visitor     = -1;

    $this->db->query(
      sprintf(
        "SELECT accesslog.accesslog_id AS accesslog_id,
                accesslog.document_id  AS document_id,
                accesslog.timestamp    AS timestamp,
                documents.string       AS document,
                documents.document_url AS document_url
           FROM %s accesslog,
                %s visitors,
                %s documents
          WHERE accesslog.client_id    = %d
            AND accesslog.accesslog_id = visitors.accesslog_id
            AND accesslog.document_id  = documents.data_id
                %s
                %s
          ORDER BY accesslog.accesslog_id,
                   accesslog.timestamp",

        $this->config['accesslog_table'],
        $this->config['visitors_table'],
        $this->config['documents_table'],
        $parameters['client_id'],
        $this->_constraint($parameters['constraints']),
        $this->_whereTimerange(
          $parameters['start'],
          $parameters['end'],
          'accesslog'
        )
      )
    );

    while ($row = $this->db->fetchRow()) {
      if (!isset($this->_documents[$row['document_id']])) {
        $this->_documents[$row['document_id']] = array(
          $row['document'],
          $row['document_url'],
          'document'
        );
      }

      if ($accesslogID != $row['accesslog_id']) {
        $accesslogID = $row['accesslog_id'];
        $node        = 0;
        $visitor++;

        unset($previousTimestamp);

        $accesslog_ids[$visitor] = $row['accesslog_id'];
      }

      $clickpaths[$visitor][$node]['document']   = $row['document_id'];
      $clickpaths[$visitor][$node]['time_spent'] = 1;

      if (isset($previousTimestamp)) {
        $clickpaths[$visitor][$node-1]['time_spent'] = $row['timestamp'] - $previousTimestamp;
      }

      $previousTimestamp = $row['timestamp'];
      $node++;
    }

    if (!isset($clickpaths)) {
      return $result;
    }

    $numClickpaths = sizeof($clickpaths) - 1;

    if ($parameters['exit_targets']) {
      for ($i = 0; $i <= $numClickpaths; $i++) {
        $this->db->query(
          sprintf(
            'SELECT accesslog.document_id    AS document_id,
                    accesslog.exit_target_id AS exit_target_id,
                    exit_targets.string      AS exit_target
               FROM %s accesslog,
                    %s exit_targets
              WHERE accesslog.accesslog_id   = %d
                AND accesslog.exit_target_id = exit_targets.data_id',

            $this->config['accesslog_table'],
            $this->config['exit_targets_table'],
            $accesslog_ids[$i]
          )
        );

        while ($row = $this->db->fetchRow()) {
          $this->_documents[$row['exit_target_id']] = array(
            $row['exit_target'],
            'http://' . $row['exit_target'],
            'exit_target'
          );

          $visitor = sizeof($clickpaths);

          $clickpaths[$visitor][0]['document']   = $row['document_id'];
          $clickpaths[$visitor][0]['time_spent'] = 0;

          $clickpaths[$visitor][1]['document']   = $row['exit_target_id'];
          $clickpaths[$visitor][1]['time_spent'] = 0;
        }
      }
    }

    for ($i = 0; $i < sizeof($clickpaths) - 1; $i++) {
      $pathLength = sizeof($clickpaths[$i]);

      if ($parameters['referers'] && $i <= $numClickpaths) {
        $this->db->query(
          sprintf(
            'SELECT referers.string     AS referer,
                    visitors.referer_id AS referer_id
               FROM %s visitors,
                    %s referers
              WHERE visitors.accesslog_id = %d
                AND visitors.referer_id   = referers.data_id',

            $this->config['visitors_table'],
            $this->config['referers_table'],
            $accesslog_ids[$i]
          )
        );

        if ($row = $this->db->fetchRow()) {
          $this->_documents[$row['referer_id']] = array(
            $row['referer'],
            'http://' . $row['referer'],
            'referer'
          );

          array_unshift(
            $clickpaths[$i],
            array(
              'document'   => $row['referer_id'],
              'time_spent' => 0,
            )
          );
        }
      }

      if (!$parameters['subpaths']) {
        $this->_processPath(
          $clickpaths[$i],
          $parameters['from'],
          $parameters['to'],
          $parameters['length']
        );
      } else {
        for ($j = 2; $j <= $pathLength; $j++) {
          $subpath       = array_slice($clickpaths[$i], 0, $j);
          $subpathLength = sizeof($subpath);

          for ($k = 2; $k <= $subpathLength; $k++) {
            $this->_processPath(
              array_slice($subpath, (0 - $k)),
              $parameters['from'],
              $parameters['to'],
              $parameters['length']
            );
          }
        }
      }
    }

    if (empty($this->_paths)) {
      return $result;
    }

    foreach ($this->_subpathStatistics as $fromNode => $toNodes) {
      foreach ($toNodes as $toNode => $data) {
        if ($parameters['api_call'] == 'all_paths') {
          $fromColor = $parameters['document_color'];
          $fromGroup = 'default';
          $fromID    = $this->_documents[$fromNode][0];
          $fromRank  = 'same';

          $toColor   = $parameters['document_color'];
          $toGroup   = 'default';
          $toID      = $this->_documents[$toNode][0];
          $toRank    = 'same';

          $edgeColor = $parameters['document_color'];

          if ($this->_documents[$toNode][2] == 'exit_target') {
            $toColor   = $parameters['exit_target_color'];
            $toGroup   = 'exit_targets';
            $toID      = 'exit_target_' . $toID;
            $toRank    = 'sink';

            $edgeColor = $parameters['exit_target_color'];
          }

          if ($this->_documents[$fromNode][2] == 'referer') {
            $fromColor = $parameters['referer_color'];
            $fromGroup = 'referers';
            $fromID    = 'referer_' . $fromID;
            $fromRank  = 'source';

            $edgeColor = $parameters['referer_color'];
          }

          $result->addNode(
            $fromID,
            array(
              'label' => $this->_documents[$fromNode][0],
              'url'   => $this->_documents[$fromNode][1],
              'color' => $fromColor,
              'rank'  => $fromRank,
              'shape' => 'box'
            ),
            $fromGroup
          );

          $result->addNode(
            $toID,
            array(
              'label' => $this->_documents[$toNode][0],
              'url'   => $this->_documents[$toNode][1],
              'color' => $toColor,
              'rank'  => $toRank,
              'shape' => 'box'
            ),
            $toGroup
          );

          $result->addEdge(
            array(
              $fromID => $toID
            ),
            array(
              'color' => $edgeColor,
              'label' => $data['count']
            )
          );
        }

        else if (isset($data['count'])) {
          $this->_subpathStatistics[$fromNode][$toNode]['time_spent'] = floor($data['time_spent'] / $data['count']);
        }
      }
    }

    if ($parameters['api_call'] != 'all_paths') {
      switch ($parameters['api_call']) {
        case 'shortest_paths': {
          $field = 'length';
          $sort  = SORT_ASC;
        }
        break;

        case 'longest_paths': {
          $field = 'length';
          $sort  = SORT_DESC;
        }
        break;

        case 'top_paths': {
          $field = 'count';
          $sort  = SORT_DESC;
        }
        break;
      }

      foreach($this->_paths as $path) $tmp[] = $path[$field];
      array_multisort($tmp, $sort, $this->_paths);

      if ($parameters['limit'] && sizeof($this->_paths) > $parameters['limit']) {
        $this->_paths = array_slice($this->_paths, 0, $parameters['limit']);
      }

      $rank = 1;

      foreach ($this->_paths as $path => $statistics) {
        $documents    = array();
        $documentURLs = array();
        $path         = explode(':', $path);
        $pathLength   = sizeof($path);
        $where        = '';

        if ($parameters['subpaths']) {
          $subpathStatistics = array();

          for ($i = 0; $i < $pathLength - 1; $i++) {
            $subpathStatistics[] = array(
              'count'      => intval($this->_subpathStatistics[$path[$i]][$path[$i+1]]['count']),
              'time_spent' => intval($this->_subpathStatistics[$path[$i]][$path[$i+1]]['time_spent'])
            );
          }
        } else {
          $subpathStatistics = false;
        }

        for ($i = 0; $i < $pathLength; $i++) {
          $documents[$i]    = $this->_documents[$path[$i]][0];
          $documentURLs[$i] = $this->_documents[$path[$i]][1];
        }

        if ($parameters['result_format'] == 'array') {
          $result[] = new phpOpenTracker_Clickpath(
            $documents,
            $documentURLs,
            $subpathStatistics,
            intval($statistics['count'])
          );
        } else {
          $result->addCluster(
            $rank,
            sprintf(
              '%s. Taken by %s visitors.',

              $rank,
              $statistics['count']
            )
          );

          for ($i = 0; $i < $pathLength - 1; $i++) {
            $fromID = $rank . '_' . $documents[$i];
            $toID   = $rank . '_' . $documents[$i+1];

            $result->addNode(
              $fromID,
              array(
                'label' => $documents[$i],
                'url'   => $documentURLs[$i],
                'color' => $parameters['document_color'],
                'shape' => 'box'
              ),
              $rank
            );

            $result->addNode(
              $toID,
              array(
                'label' => $documents[$i+1],
                'url'   => $documentURLs[$i+1],
                'color' => $parameters['document_color'],
                'shape' => 'box'
              ),
              $rank
            );

            $result->addEdge(
              array(
                $fromID => $toID
              ),
              array(
                'color' => $parameters['document_color']
              )
            );
          }
        }

        $rank++;
      }
    }

    if ($parameters['result_format'] == 'graphviz') {
      return $result->parse();
    } else {
      return $result;
    }
  }

  /**
  * Helper function for _clickpath_analysis()
  *
  * @param  string  $path
  * @param  integer $from
  * @param  integer $to
  * @param  integer $length
  * @access private
  */
  function _processPath($path, $from, $to, $length) {
    $pathLength = sizeof($path);

    $first = $path[0]['document'];
    $last  = $path[$pathLength - 1]['document'];

    if ($pathLength > 1 &&
        ((  $from == false && $to     == false) ||
         (  $from != false && $from   == $first && $to == false) ||
         (  $from == false && $to     != false  && $to == $last) ||
         (  $from != false && $from   == $first && $to != false && $to == $last)) &&
         ($length == false || $length == $pathLength)) {
      $id = '';

      for ($i = 0; $i < $pathLength; $i++) {
        $separator = empty($id) ? '' : ':';
        $id .= $separator . $path[$i]['document'];
      }

      if (!isset($this->_paths[$id])) {
        $this->_paths[$id]['count']  = 1;
        $this->_paths[$id]['length'] = $pathLength;

        if ($pathLength == 2) {
          $this->_subpathStatistics[$first][$last]['count']      = 1;
          $this->_subpathStatistics[$first][$last]['time_spent'] = $path[0]['time_spent'];
        }
      } else {
        $this->_paths[$id]['count']++;

        if ($pathLength == 2) {
          $this->_subpathStatistics[$first][$last]['count']++;
          $this->_subpathStatistics[$first][$last]['time_spent'] += $path[0]['time_spent'];
        }
      }
    }
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
