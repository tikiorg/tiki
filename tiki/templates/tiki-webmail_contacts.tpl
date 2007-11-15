<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{* Index we display a wiki page here *}
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/{$prefs.style}" type="text/css" />
    {include file="bidi.tpl"}
    <title>{tr}Address book{/tr}</title>
  </head>
  <body>
  <div id="tiki-clean">

<h2>{tr}Contacts{/tr}</h2>
<div align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-webmail_contacts.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="element" value="{$element|escape}" />
     <input type="hidden" name="section" value="contacts" />
   </form>
   </td>
</tr>
</table>
<a class="link" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{tr}All{/tr}</a>
{section name=ix loop=$letters}
<a class="link" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;letter={$letters[ix]}">{$letters[ix]}</a>
{/section}
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'firstName_desc'}firstName_asc{else}firstName_desc{/if}">{tr}First Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastName_desc'}lastName_asc{else}lastName_desc{/if}">{tr}Last Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'nickname_desc'}nickname_asc{else}nickname_desc{/if}">{tr}Nickname{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].firstName}</td>
<td class="{cycle advance=false}">{$channels[user].lastName}</td>
<td class="{cycle advance=false}"><a class="link" href="#" onclick="javascript:window.opener.document.getElementById('{$element}').value=window.opener.document.getElementById('{$element}').value + '{$channels[user].email}' + ' ';">{$channels[user].email}</a>
[&nbsp;&nbsp;<a class="link" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}" 
title="{tr}Delete{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}Delete{/tr}' /></a>&nbsp;&nbsp;]
</td>
<td class="{cycle advance=false}">{$channels[user].nickname}</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
</div>
</body>
</html>
