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
// $Id: DB.php,v 1.1 2003-04-25 18:43:52 lrargerich Exp $
//

/**
* Base Class for phpOpenTracker Database Handlers.
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_DB {
  /**
  * Config
  *
  * @var array $config
  */
  var $config = array();

  /**
  * Connection
  *
  * @var  integer $connection
  */
  var $connection;

  /**
  * Number of performed queries
  *
  * @var  integer $numQueries
  */
  var $numQueries = 0;

  /**
  * Result
  *
  * @var  integer $result
  */
  var $result;

  /**
  * Constructor.
  *
  * @access public
  */
  function phpOpenTracker_DB() {
    $this->config = &phpOpenTracker_Config::singleton();
  }

  /**
  * Singleton.
  *
  * @access public
  * @return object
  * @static
  */
  function &singleton() {
    static $db;

    if (!isset($db)) {
      $config  = &phpOpenTracker_Config::singleton();
      $dbClass = 'phpOpenTracker_DB_' . $config['db_type'];

      if (!@include(POT_INCLUDE_PATH . 'DB/' . $config['db_type'] . '.php')) {
        phpOpenTracker::handleError(
          sprintf(
            'Unknown database handler "%s".',
            $config['db_type']
          ),
          E_USER_ERROR
        );
      }

      $db = new $dbClass;
    }

    return $db;
  }

  /**
  * Prints debug information for an SQL query.
  *
  * @param  string  $query
  * @access public
  */
  function debugQuery($query) {
    printf(
      '<table border="1" width="100%%"><tr><td valign="top" width="50">%s</td><td valign="top"><pre>%s</pre></td></tr>',

      ++$this->numQueries,
      $query
    );
  }

  /**
  * Stores additional data associated with a given accesslog_id.
  *
  * @param  integer $accesslogID
  * @param  array   $addData
  * @access public
  */
  function storeAddData($accesslogID, $addData) {
    foreach ($addData as $field => $value) {
      $this->query(
        sprintf(
          "INSERT
             INTO %s
                  (accesslog_id,
                   data_field, data_value)
            VALUES(%d,
                   '%s', '%s')",

          $this->config['additional_data_table'],
          $accesslogID,
          $field,
          $value
        )
      );
    }
  }

  /**
  * Stores a string into the database.
  *
  * @param  string   $table
  * @param  string   $string1
  * @param  optional $string2
  * @return integer
  */
  function storeIntoDataTable($table, $string1, $string2 = '') {
    if (empty($string1)) {
      return 0;
    }

    if ($table == $this->config['documents_table']) {
      $urlField = ', document_url';
      $urlValue = ", '" . $this->prepareString($string2) . "'";
    } else {
      $urlField = '';
      $urlValue = '';
    }

    $dataID = crc32(strtolower($string1));

    $this->query(
      sprintf(
        "INSERT INTO %s
                     (data_id, string%s)
              VALUES (%d, '%s'%s)",

        $table,
        $urlField,
        $dataID,
        $this->prepareString($string1),
        $urlValue
      ),
      false,
      false
    );

    return $dataID;
  }

  /**
  * Prepares a string for an SQL query.
  *
  * @param  string $string
  * @return string
  * @access public
  */
  function prepareString($string) {
    $string = substr($string, 0, 254);

    if (ini_get('magic_quotes_gpc')) {
      $string = stripslashes($string);
    }

    $string = str_replace("'", "''", $string);

    return $string;
  }

  /**
  * Returns TRUE if the database supports nested queries
  * and FALSE otherwise.
  *
  * @return boolean
  * @access public
  * @since  phpOpenTracker 1.1.0
  */
  function supportsNestedQueries() {
    return true;
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
