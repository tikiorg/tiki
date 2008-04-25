<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

class timer {
    function parseMicro($micro) {
        list($micro, $sec) = explode(' ', microtime());

        return $sec + $micro;
    }

    function start($timer = 'default', $restart = FALSE) {
        if (isset($this->timer[$timer]) && !$restart) {
            // report error - timer already exists
        }
        $this->timer[$timer] = $this->parseMicro(microtime());
    }

    function stop($timer = 'default') {
        $result = $this->elapsed($timer);
        unset ($this->timer[$timer]);
        return $result;
    }

    function elapsed($timer = 'default') {
        return $this->parseMicro(microtime()) - $this->timer[$timer];
    }
}
