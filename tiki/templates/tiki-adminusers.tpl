{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-adminusers.tpl,v 1.39 2004-01-28 12:17:49 mose Exp $ *}

<a href="tiki-adminusers.php" class="pagetitle">{tr}Admin users{/tr}</a>
  
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=UserAdministrationScreen" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin users{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-adminusers.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin users tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}
<br />

<span class="button2"><a href="tiki-admingroups.php" class="linkbut">{tr}Admin groups{/tr}</a></span>
<span class="button2"><a href="tiki-adminusers.php" class="linkbut">{tr}Admin users{/tr}</a></span>
{if $username}
<span class="button2"><a href="tiki-adminusers.php?add=1" class="linkbut">{tr}Add a new user{/tr}</a></span>
{/if}
<br /><br /><br />

{cycle name=tabs values="1,2,3,4" print=false advance=false}

{* ---------------------- tabs -------------------- *}
<div class="tabs">
<span id="tab{cycle name=tabs}" class="tab tabActive">{tr}Users{/tr}</span>
{if $username}
<span id="tab{cycle name=tabs}" class="tab">{tr}Edit user{/tr} <i>{$username}</i></span>
{if $fields}
<span id="tab{cycle name=tabs}" class="tab">{tr}More info{/tr}</span>
{/if}
{else}
<span id="tab{cycle name=tabs}" class="tab">{tr}Add a new user{/tr}</span>
{/if}
</span>
</div>

{cycle name=content values="1,2,3,4" print=false advance=false}

{* ---------------------- tab with list -------------------- *}
<div id="content{cycle name=content}" class="content">
<h2>{tr}Users{/tr}</h2>
<div align="center">
<form method="get" action="tiki-adminusers.php">
<table class="findtable"><tr>
<td>{tr}Find{/tr}</td>
<td><input type="text" name="find" value="{$find|escape}" /></td>
<td><input type="submit" value="{tr}find{/tr}" name="search" /></td>
<td>{tr}Number of displayed rows{/tr}</td>
<td><input type="text" size="4" name="numrows" value="{$numrows|escape}">
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" /></td>
</tr></table>
</form>

{if $cant_pages > 1}
<div align="center">
{section name=ini loop=$initials}
{if $initial and $initials[ini] eq $initial}
<span class="button2"><span class="linkbuton">{$initials[ini]|capitalize}</span></span> . 
{else}
<a href="tiki-adminusers.php?initial={$initials[ini]}{if $find}&amp;find={$find|escape:"url"}{/if}{if $offset}&amp;offset={$offset}{/if}{if $numrows}&amp;numrows={$numrows}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}" 
class="prevnext">{$initials[ini]}</a> . 
{/if}
{/section}
<a href="tiki-adminusers.php?initial={if $find}&amp;find={$find|escape:"url"}{/if}{if $offset}&amp;offset={$offset}{/if}{if $numrows}&amp;numrows={$numrows}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}" 
class="prevnext">{tr}All{/tr}</a>
</div>
{/if}

<table class="normal">
<tr>
<td class="heading thin"></td>
<td class="heading thin"></td>
<td class="heading"><a class="tableheading" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'currentLogin_desc'}currentLogin_asc{else}currentLogin_desc{/if}">{tr}Last login{/tr}</a></td>
<td class="heading thin">&nbsp;</td>
<td class="heading">{tr}Groups{/tr}</td>
<td class="heading thin"></td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$users}
<tr class="{cycle}">
<td class="thin"><a class="link" href="tiki-user_preferences.php?view_user={$users[user].user}" title="{tr}Configure/Options{/tr}"><img border="0" alt="{tr}Configure/Options{/tr}" src="img/icons/config.gif" /></a></td>
<td class="thin"><a class="link" href="tiki-adminusers.php?user={$users[user].userId}"  title="{tr}Click here to edit this user{/tr}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a></td>
<td><a class="link" href="tiki-adminusers.php?user={$users[user].userId}"  title="{tr}Click here to edit this user{/tr}">{$users[user].user}</a></td>
<td>{$users[user].email}</td>
<td>{if $users[user].currentLogin eq ''}{tr}Never{/tr}{else}{$users[user].currentLogin|dbg|tiki_long_datetime}{/if}</td>
<td class="thin"><a class="link" href="tiki-assignuser.php?assign_user={$users[user].user}" title="{tr}Assign Group{/tr}"><img border="0" alt="{tr}Assign Group{/tr}" src="img/icons/key.gif" /></a></td>
<td>
{foreach from=$users[user].groups item=grs}
{if $grs != "Anonymous"}
<a class="link" href="tiki-admingroups.php?group={$grs|escape:"url"}">{$grs}</a>
(<a class="link" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}&amp;user={$users[user].user}&amp;action=removegroup&amp;group={$grs|escape:"url"}">x</a>)<br />
{/if}
{/foreach}
<td  class="thin">{if $users[user].user ne 'admin'}<a class="link" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;user={$users[user].user}"
title="{tr}Remove{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>{/if}
</td>
</tr>
{/section}
</table>
{if $cant_pages > 1}
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-adminusers.php?{if $find}find={$find|escape:"url"}&amp;{/if}{if $initial}initial={$initial}&amp;{/if}offset={$prev_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-adminusers.php?{if $find}find={$find|escape:"url"}&amp;{/if}{if $initial}initial={$initial}&amp;{/if}offset={$next_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}

<a class="prevnext" href="tiki-adminusers.php?find={$find|escape:"url"}&amp;{if $initial}initial={$initial}&amp;{/if}offset={$selector_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>
{/if}
</div>

{* ---------------------- tab with form -------------------- *}
<div id="content{cycle name=content}" class="content">
<h2>{tr}Add a new user{/tr}</h2>

<form action="tiki-adminusers.php" method="post" enctype="multipart/form-data">
<table class="normal">
<tr class="formcolor"><td>{tr}User{/tr}:</td><td><input type="text" name="name"  value="{$username|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Pass{/tr}:</td><td><input type="password" name="pass" /></td></tr>
<tr class="formcolor"><td>{tr}Again{/tr}:</td><td><input type="password" name="pass2" /></td></tr>
<tr class="formcolor"><td>{tr}Email{/tr}:</td><td><input type="text" name="email" size="30"  value="{$usermail|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Batch upload (CSV file){/tr}:</td><td><input type="file" name="csvlist"
/><br/>{tr}Overwrite{/tr}: <input type="checkbox" name="overwrite" checked="checked" /></td></tr>
<tr class="formcolor"><td>&nbsp;</td><td>
<input type="hidden" name="oluser" value="{$user|escape}">
<input type="submit" name="newuser" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<br/>
<table class="normal">
<tr class="formcolor"><td>
<a class="link" href="javascript:genPass('genepass','pass','pass2');">{tr}Generate a password{/tr}</a></td>
<td><input id='genepass' type="text" /></td></tr>
</table>
<br/>
{if $added != "" or $discarded != ""}
<h2>{tr}Batch Upload Results{/tr}</h2>
{tr}Added users{/tr}: {$added}
{if $discarded != ""}
- {tr}Rejected users{/tr}: {$discarded}<br/><br/>
<table class="normal">
<tr><td class="heading">{tr}Username{/tr}</td><td class="heading">{tr}Reason{/tr}</td></tr>
{section name=reject loop=$discardlist}
<tr><td class="odd">{$discardlist[reject].name}</td><td class="odd">{$discardlist[reject].reason}</td></tr>
{/section}
</table>
{/if}
{/if}
</div>

{* ---------------------- tab with more info -------------------- *}
{if $username and $fields}
<div id="content{cycle name=content}" class="content">
<table class="normal">

{* copypaste from templates/tiki-view_tracker_item.tpl s/ins_fields/fields/g *}
{section name=ix loop=$fields}
{if $fields[ix].isPublic eq 'y' or $tiki_p_admin_trackers eq 'y'}

{if $fields[ix].type eq 'h'}
</table>
<h3>{$fields[ix].name}</h3>
<table class="normal">

{elseif $fields[ix].type ne 'x'}
{if $fields[ix].type eq 'c' or $fields[ix].type eq 't' and $fields[ix].options_array[0] eq '1'}
<tr class="formcolor"><td class="formlabel">{$fields[ix].name}</td><td>
{elseif $stick eq 'y'}
<td class="formlabel right">{$fields[ix].name}</td><td>
{else}
<tr class="formcolor"><td>{$fields[ix].name}</td>
<td colspan="3">
{/if}
{if $fields[ix].type eq 'f' or $fields[ix].type eq 'j'}
{$fields[ix].value|date_format:$daformat}</td></tr>

{elseif $fields[ix].type eq 'a'}
{$fields[ix].pvalue}

{elseif $fields[ix].type eq 'e'}
{assign var=fca value=$fields[ix].options}
<table width="100%"><tr>{cycle name=$fca values=",</tr><tr>" advance=false print=false}
{foreach key=ku item=iu from=$fields[ix].$fca}
{assign var=fcat value=$iu.categId }
<td width="50%" nowrap="nowrap">
{if $fields[ix].cat.$fcat eq 'y'}
<tt>X&nbsp;</tt><b>{$iu.name}</b></td>
{else}
<tt>&nbsp;&nbsp;</tt><s>{$iu.name}</s></td>
{/if}
{cycle name=$fca}
{/foreach}
</tr></table></td></tr>

{elseif $fields[ix].type eq 'c'}
{$fields[ix].value|replace:"y":"{tr}Yes{/tr}"|replace:"n":"{tr}No{/tr}"}
{if $fields[ix].options_array[0] eq '1' and $stick ne 'y'}
</td>
{assign var=stick value="y"}
{else}
</td></tr>
{assign var=stick value="n"}
{/if}

{elseif $fields[ix].type eq 't'}
{if $fields[ix].options_array[2]}
{$fields[ix].value|default:"0"} <span class="formunit">&nbsp;{$fields[ix].options_array[2]}</span>
{else}
{if $fields[ix].linkId}
<a href="tiki-view_tracker_item.php?trackerId={$fields[ix].options_array[0]}&amp;itemId={$fields[ix].linkId}" class="link">{$fields[ix].value}</a>
{else}
{$fields[ix].value}
{/if}
{/if}
{if $fields[ix].options_array[0] eq '1' and $stick ne 'y'}
</td>
{assign var=stick value="y"}
{else}
</td></tr>
{assign var=stick value="n"}
{/if}

{else}
{$fields[ix].value}
{if $fields[ix].options_array[0] eq '1' and $stick ne 'y'}
</td>
{assign var=stick value="y"}
{else}
</td></tr>
{assign var=stick value="n"}
{/if}
{/if}
{/if}
{/if}
{/section}
{* end of copypasted block *}

</table>
<span class="button2"><a href="tiki-view_tracker_item.php?trackerId={$usersTrackerId}&amp;itemId={$useritemId}&amp;show=mod" class="linkbut">{tr}Edit informations{/tr}</a></span>
</div>
{/if}
