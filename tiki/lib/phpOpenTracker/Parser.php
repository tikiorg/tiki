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
// $Id: Parser.php,v 1.1 2003-04-25 18:43:52 lrargerich Exp $
//

/**
* phpOpenTracker Parser for Hostname,
* Operating System and User Agent information.
*
* The regular expressions used in this class are taken from
* the ModLogAn (http://jan.kneschke.de/projects/modlogan/)
* project.
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_Parser {
  /**
  * Parses a given string for Hostname information.
  *
  * @param  string $string
  * @return string
  * @access public
  * @static
  */
  function hostname($string) {
    return phpOpenTracker_Parser::match(
      $string,
      phpOpenTracker_Parser::readRules(POT_CONFIG_PATH . 'hosts.ini')
    );
  }

  /**
  * Parses a given string for Operating System and
  * User Agent information.
  *
  * @param  string $string
  * @return array
  * @access public
  * @static
  */
  function userAgent($string) {
    if (preg_match('#\((.*?)\)#', $string, $tmp)) {
      $elements   = explode(';', $tmp[1]);
      $elements[] = $string;
    } else {
      $elements = array($string);
    }

    if ($elements[0] != 'compatible') {
      $elements[] = substr($string, 0, strpos($string, '('));
    }

    $result['operating_system'] = phpOpenTracker_Parser::match(
      $elements,
      phpOpenTracker_Parser::readRules(
        POT_CONFIG_PATH . 'operating_systems.ini'
      )
    );

    $result['user_agent'] = phpOpenTracker_Parser::match(
      $elements,
      phpOpenTracker_Parser::readRules(
        POT_CONFIG_PATH . 'user_agents.ini'
      )
    );

    return $result;
  }

  /**
  * Matches a string against a set of regular expressions.
  *
  * @param  mixed   $elements
  * @param  array   $rules
  * @return string
  * @access public
  * @static
  */
  function match($elements, $rules) {
    if (!is_array($elements)) {
      $noMatch  = $elements;
      $elements = array($elements);
    } else {
      $noMatch = 'Not identified';
    }

    foreach ($rules as $rule) {
      if (!isset($result)) {
        foreach ($elements as $element) {
          $element = trim($element);
          $pattern = trim($rule['pattern']);

          if (preg_match($pattern, $element, $tmp)) {
            $result = sprintf(
              trim($rule['string']),
              isset($tmp[1]) ? $tmp[1] : '',
              isset($tmp[2]) ? $tmp[2] : '',
              isset($tmp[3]) ? $tmp[3] : ''
            );

            break;
          }
        }
      } else {
        break;
      }
    }

    return isset($result) ? $result : $noMatch;
  }

  /**
  * Reads a set of regular expressions from a given file.
  *
  * @param  string $rulesFile
  * @return array
  * @access public
  * @static
  */
  function readRules($rulesFile) {
    $rules = array();

    if ($file = @file($rulesFile)) {
      $index    = 0;
      $numLines = sizeof($file);

      for ($i = 0; $i < $numLines; $i += 3) {
        $rules[$index]['pattern'] = $file[$i];
        $rules[$index]['string']  = $file[$i+1];
        $index++;
      }
    } else {
      return phpOpenTracker::handleError(
        sprintf(
          'Cannot open "%s".',
          $rulesFile
        ),
        E_USER_ERROR
      );
    }

    return $rules;
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
