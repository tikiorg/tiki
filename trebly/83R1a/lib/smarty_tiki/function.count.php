<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.count.php 36360 2011-08-20 18:57:22Z chealer $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_count($params, $smarty) {
    extract($params);
    if (empty($var)) {
        $smarty->trigger_error("count: missing 'var' parameter");
        return;
    }
    print(count($var));
}
