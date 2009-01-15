<?php

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
 *           $smarty->load_filter('pre','ticket');
 *           from application.
 *           Create a table in your db (or hack any other way) for example
 *           create table tickets ( user varchar(32), ticket varchar(16));
 *           Ticket has to be stored there and regenerated at each page
 * Author:   luis@tikiwiki.org for idea and concept
 *           mose@tikiwiki.org for coding
 * -------------------------------------------------------------
 */
 function smarty_prefilter_ticket($source, &$smarty) {
    $source = preg_replace("~((<form[^>]*action=(\"|')[^\"']*tiki-[^\"']*(\"|')[^>]*>(\s*))<)~si",
                            '$2{if $ticket}<input type="hidden" name="ticket" value="{$ticket}" />{/if}<', $source);
		$source = preg_replace("~((href=(\"|')[^\"']*tiki-[^\?\"']*)\?([^\"']*=[^\"']*(\"|')))~si", 
                           '$2?{if $ticket}ticket={$ticket}&{/if}$4', $source);
    return $source;
 }
?>
