<?php

// +----------------------------------------------------------------------+
// | Wollabot version 0.3.0a (prerelease)                                 |
// +----------------------------------------------------------------------+
// | Copyright (C) 2002 Christian Joergensen (mail@phpguru.dk)            |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 2 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the         |
// | GNU General Public License for more details                          |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the                        |
// | Free Software Foundation, Inc., 59 Temple Place                      |
// | Suite 330, Boston, MA  02111-1307, USA                               |
// +----------------------------------------------------------------------+
// | Author(s): Christian Jørgensen <mail@phpguru.dk>                     |
// +----------------------------------------------------------------------+
// 
// $Id: Wollabot_Base.php,v 1.1 2003-11-15 11:34:31 mose Exp $                                                                 

class Wollabot_Base extends Wollabot_Module {

  function Wollabot_Base () {

    $this->bind_onconnect('join_channels');

  }


  function join_channels() {

    if (sizeof($this->wollabot->current_channels)) {
      foreach($this->wollabot->current_channels as $channel => $settings) {
      
	$this->join($channel, $settings['key']);
	if (strlen($settings['on_join']) > 0) {
	  $cmds = explode("\\", $settings['on_join']);
	  foreach($cmds as $cmd) $this->send_raw($cmd);
	}
	
      }
    }

  }

}

$wollabot->register_module("Wollabot_Base");


?>