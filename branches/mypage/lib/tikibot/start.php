<?php

// +----------------------------------------------------------------------+
// | Wollabot - A modular service bot for IRC channels written in PHP     |
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
// $Id: start.php,v 1.4 2003-12-15 00:08:05 redflo Exp $                                                                 

ini_set('include_path', '.:../wollabot:../smartirc:../..:../../lib/pear:../../lib/adodb');

require_once "lib/wollabot.php";
include_once("../init/initlib.php");

require_once("../../db/tiki-db.php");
require_once("../tikilib.php");
require_once("../userslib.php");
require_once("../stats/statslib.php");
		
$tikilib = new TikiLib($dbTiki);
$userlib = new UsersLib($dbTiki);

// This file is only meant to work as a list of tasks wollabot must finish
// in a given order.

$wollabot->register_modules();
$wollabot->prepare_modules();
$wollabot->connect_and_register();

$wollabot->loop();

$wollabot->disconnect();

?>
