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
// $Id: Plugin.php,v 1.1 2003-04-25 18:43:53 lrargerich Exp $
//

/**
* Base Class for phpOpenTracker API plugins
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_API_Plugin {
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
  * Constructor.
  *
  * @access public
  */
  function phpOpenTracker_API_Plugin() {
    $this->config    = &phpOpenTracker_Config::singleton();
    $this->container = &phpOpenTracker_Container::singleton();
    $this->db        = &phpOpenTracker_DB::singleton();
  }

  /**
  * Builds constraint clause.
  *
  * @param           array   $constraints
  * @param  optional boolean $selfJoinPossiblyRequired
  * @return mixed
  * @access protected
  * @since  phpOpenTracker 1.1.0
  */
  function _constraint($constraints, $selfJoinPossiblyRequired = false) {
    $constraint       = '';
    $selfJoinRequired = false;

    foreach ($constraints as $field => $value) {
      switch ($field) {
        case 'document': {
          $constraint .= sprintf(
            " AND accesslog%s.document_id = %d",

            ($selfJoinPossiblyRequired) ? '2' : '',
            $value,
            ($selfJoinPossiblyRequired) ? '2' : ''
          );

          if ($selfJoinPossiblyRequired) {
            $selfJoinRequired = true;
          }
        }
        break;

        case 'entry_document': {
          $constraint .= sprintf(
            " AND accesslog%s.document_id = %d AND accesslog%s.entry_document = '1'",

            ($selfJoinPossiblyRequired) ? '2' : '',
            $value,
            ($selfJoinPossiblyRequired) ? '2' : ''
          );

          if ($selfJoinPossiblyRequired) {
            $selfJoinRequired = true;
          }
        }
        break;

        case 'exit_document': {
          $constraint .= sprintf(
            ' AND accesslog%s.document_id = %d AND accesslog%s.exit_target_id <> 0',

            ($selfJoinPossiblyRequired) ? '2' : '',
            $value,
            ($selfJoinPossiblyRequired) ? '2' : ''
          );

          if ($selfJoinPossiblyRequired) {
            $selfJoinRequired = true;
          }
        }
        break;

        case 'exit_target': {
          $constraint .= sprintf(
            ' AND accesslog%s.exit_target_id = %d',

            ($selfJoinPossiblyRequired) ? '2' : '',
            $value
          );

          if ($selfJoinPossiblyRequired) {
            $selfJoinRequired = true;
          }
        }
        break;

        case 'host':
        case 'operating_system':
        case 'referer':
        case 'user_agent': {
          $constraint .= sprintf(
            ' AND visitors.%s_id = %d',

            $field,
            $value
          );
        }
        break;
      }
    }

    if ($selfJoinPossiblyRequired) {
      return array(
        $constraint,
        $selfJoinRequired
      );
    } else {
      return $constraint;
    }
  }

  /**
  * Builds timerange where clause for interval (start, end).
  *
  * @param           integer start
  * @param           integer end
  * @param  optional string  table
  * @return string
  * @access protected
  */
  function _whereTimerange($start, $end, $table = 'visitors') {
    $table    .= '.';
    $timerange = ' AND ';

    if ($start && $end) {
      $timerange .= $table . "timestamp BETWEEN $start AND $end";
    }

    elseif ($start && !$end) {
      $timerange .= $table . "timestamp >= $start";
    }

    elseif (!$start && $end) {
      $timerange .= $table . "timestamp <= $end";
    }

    elseif (!$start && !$end) {
      return '';
    }

    return $timerange;
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
