<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_Factory {
  static $counter = 0;

  public static function newFormatter($plugin) {
    self::$counter++;
    return new Search_Formatter($plugin, self::$counter);
  }
}