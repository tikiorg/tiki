{php}header('Content-Type: text/html; charset=utf-8');{/php}{*

$Id$

--- IMPORTANT: If you edit this (or any other TPL file) file via the Tiki built-in TPL editor
(tiki-edit_templates.php), all the javascript will be stripped. This will cause problems.
(Ex.: menus stop collapsing/expanding).

You should only modify header.tpl via a text editor through console, or ssh, or FTP edit commands. 
And only if you know what you are doing ;-)

You are most likely wanting to modify the top of your Tiki site. 
Please consider using Look & Feel feature or modifying tiki-top_bar.tpl 
which you can do safely via the web-based interface.       
---

*}<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if isset($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if isset($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
<head>
{include file="header.tpl"}
</head>

<body {if isset($section) and $section eq 'wiki page' and $prefs.user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'}ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if}
onload="{if $prefs.feature_tabs eq 'y'}tikitabs({if $cookietab neq ''}{$cookietab}{else}1{/if},5);{/if}{if $msgError} javascript:location.hash='msgError'{/if}"
{if $section} class="tiki_{$section}"{/if}>
{if $prefs.minical_reminders>100}
<iframe width='0' height='0' frameborder="0" src="tiki-minical_reminders.php"></iframe>
{/if}

{if $prefs.feature_community_mouseover eq 'y'}{popup_init src="lib/overlib.js"}{/if}
{if $prefs.feature_siteidentity eq 'y' and $filegals_manager ne 'y'}
{* Site identity header section *}
	<div id="siteheader">
		{include file="tiki-site_header.tpl"}
	</div>
{/if}

{if $prefs.feature_fullscreen eq 'y' and $filegals_manager ne 'y' and $print_page ne 'y'}
{if $smarty.session.fullscreen eq 'y'}
<a href="{$smarty.server.SCRIPT_NAME}{if $fsquery}?{$fsquery}&amp;{else}?{/if}fullscreen=n" style="float:right;padding:0 10px;font-size:80%;" class="menulink" id="fullscreenbutton">{tr}Cancel Fullscreen{/tr}</a>
{else}
<a href="{$smarty.server.SCRIPT_NAME}{if $fsquery}?{$fsquery}&amp;{else}?{/if}fullscreen=y" style="float:right;padding:0 10px;font-size:80%;" class="menulink" id="fullscreenbutton">{tr}Fullscreen{/tr}</a>
{/if}
{/if}


{* Index we display a wiki page here *}
{if $prefs.feature_bidi eq 'y'}
<div dir="rtl">
{/if}
{if $prefs.feature_ajax eq 'y'}
{include file="tiki-ajax_header.tpl"}
{/if}
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
<div id="tiki-main">
  {if $prefs.feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div>
  {/if}
  <div id="tiki-mid">
  <table id="tiki-midtbl" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      {if $prefs.feature_left_column ne 'n' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n'}
      <td id="leftcolumn" valign="top"
			{if $prefs.feature_left_column eq 'user'} 
			style="display:{if isset($cookie.show_leftcolumn) and $cookie.show_leftcolumn ne 'y'}none{else}table-cell;_display:block{/if};"
			{/if}>
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </td>
      {/if}
      <td id="centercolumn" valign="top">
 		{/if}
      {if $prefs.feature_left_column eq 'user' or $prefs.feature_right_column eq 'user'}
        <div id="showhide_columns">
      {if $prefs.feature_left_column eq 'user' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n'}
	<div style="text-align:left;float:left;"><a class="flip" href="javascript:flip('leftcolumn','table-cell');">
        <img name="leftcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Left Menus{/tr}&nbsp;</a></div>
      {/if}
      {if $prefs.feature_right_column eq 'user'&& $right_modules|@count > 0 && $show_columns.right_modules ne 'n'}
        <div style="text-align:right;float:right;"><a class="flip" href="javascript:flip('rightcolumn','table-cell');">
        &nbsp;{tr}Show/Hide Right Menus{/tr}&nbsp;<img name="rightcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" /></a></div>
      {/if}
        <br clear="all" />
        </div>
      {/if}

			<div id="tiki-center">
			{$mid_data}
      </div>
			
			{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
      </td>
      {if $prefs.feature_right_column ne 'n' && $right_modules|@count > 0 && $show_columns.right_modules ne 'n'}
      <td id="rightcolumn" valign="top" 
			{if $prefs.feature_right_column eq 'user'} 
			style="display:{if isset($cookie.show_rightcolumn) and $cookie.show_rightcolumn ne 'y'}none{else}table-cell;_display:block{/if};"
			{/if}>
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}
      </td>
      {/if}
    </tr>
    </table>
  </div>
  {if $prefs.feature_bot_bar eq 'y'}
  <div id="tiki-bot">
    {include file="tiki-bot_bar.tpl"}
  </div>
  {/if}
</div>
{/if}

{if $prefs.feature_bidi eq 'y'}
</div>
{/if}

{include file="footer.tpl"}
	</body>
</html>