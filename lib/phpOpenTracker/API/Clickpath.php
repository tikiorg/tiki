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
// $Id: Clickpath.php,v 1.1 2003-04-25 18:43:53 lrargerich Exp $
//

/**
* Clickpath
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_Clickpath {
  /**
  * Count
  *
  * @var integer $count
  */
  var $count;

  /**
  * Length
  *
  * @var integer $length
  */
  var $length;

  /**
  * documents
  *
  * @var array $documents
  */
  var $documents;

  /**
  * document_urls
  *
  * @var array $document_urls
  */
  var $document_urls;

  /**
  * Statistics
  *
  * @var array $statistics
  */
  var $statistics;

  /**
  * Constructor.
  *
  * @param  array            $documents
  * @param  optional array   $document_urls
  * @param  optional array   $statistics
  * @param  optional integer $count
  * @access public
  */
  function phpOpenTracker_Clickpath($documents, $document_urls = array(), $statistics = array(), $count = 1) {
    $this->documents     = $documents;
    $this->document_urls = $document_urls;
    $this->count         = $count;
    $this->length        = sizeof($documents);
    $this->statistics    = $statistics;
    print("hey");
  }

  /**
  * Returns GraphViz/dot markup for the graph.
  *
  * @param  optional boolean $returnObject
  * @return mixed
  * @access public
  */
  function toGraph($returnObject = false) {
    if (!@include_once('Image/GraphViz.php')) {
      phpOpenTracker::handleError(
        'Could not find PEAR Image_GraphViz package, exiting.',
        E_USER_ERROR
      );
    }

    $graph = new Image_GraphViz();

    for ($i = 0; $i < $this->length - 1; $i++) {
      $graph->addNode(
        $i,
        array(
          'url'   => $this->document_urls[$i],
          'label' => $this->documents[$i],
          'shape' => 'box'
        )
      );

      $graph->addNode(
        $i+1,
        array(
          'url'   => $this->document_urls[$i+1],
          'label' => $this->documents[$i+1],
          'shape' => 'box'
        )
      );

      if (isset($this->statistics[$i]['count'])) {
        $label = sprintf(
          'count: %d\naverage time: %d seconds',

          $this->statistics[$i]['count'],
          $this->statistics[$i]['time_spent']
        );
      } else {
        $label = sprintf(
          'time spent: %d seconds',

          $this->statistics[$i]
        );
      }

      $graph->addEdge(
        array(
          $i => $i+1
        ),
        array(
          'label' => $label
        )
      );
    }

    if ($returnObject) {
      return $graph;
    } else {
      return $graph->parse();
    }
  }

  /**
  * Returns XML markup for the graph.
  *
  * @param  optional boolean $returnObject
  * @return mixed
  * @access public
  */
  function toXML($returnObject = false) {
    if (!@include_once('XML/Tree.php')) {
      phpOpenTracker::handleError(
        'Could not find PEAR XML_Tree package, exiting.',
        E_USER_ERROR
      );
    }

    $tree = new XML_Tree;
    $root = &$tree->addRoot('clickpath');

    for ($i = 0; $i < $this->length; $i++) {
      $root->addChild('length', $this->length);

      $node = &$root->addChild('node');

      $node->addChild('document', $this->documents[$i]);

      if (!isset($this->statistics[$i]['count'])) {
        $node->addChild('timespent', $this->statistics[$i]);
      }
    }

    if (!$returnObject) {
      return $root->get();
    } else {
      return $root;
    }
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
