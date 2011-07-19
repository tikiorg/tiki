<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     postfilter.ticket.php
 * Type:     postfilter
 * Name:     ticket
 * Version:  1.0
 * Date:     Mar 31, 2004
 * Purpose:  Protect against CSRF web applications vulbnerability
 *           http://openacs.org/forums/message-view?message_id=32884
 *           for details about that security issue
 * Install:  Drop into the plugin directory, call 
 *           $smarty->load_filter('post','ticket');
 *           from application.
 *           Create a table in your db (or hack any other way) for example
 *           create table tickets ( user varchar(32), ticket varchar(16));
 *           Ticket has to be stored there and regenerated at each page
 * Author:   luis@tikiwiki.org for idea and concept
 *           mose@tikiwiki.org for coding
 * -------------------------------------------------------------
 */
 function smarty_outputfilter_ticket($source, &$smarty) {
		global $ticket;
    $source = preg_replace("~((<form[^>]*action=(\"|')[^\"']*tiki-[^\"']*(\"|')[^>]*>(\s*))<)~si",
                            '$2<input type="hidden" name="ticket" value="'.$ticket.'" /><', $source);
		$source = preg_replace("~((href=(\"|')[^\"']*tiki-[^\?\"']*)\?(ticket=[0-9a-z]*&)?([^\"']*(\"|')))~si", 
                           '$2?ticket='.$ticket.'&$5', $source);
    return $source;
 }
