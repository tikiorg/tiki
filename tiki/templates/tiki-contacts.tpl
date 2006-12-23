<h1><a href="tiki-contacts.php" class="pagetitle">{tr}Contacts{/tr}</a></h1>

<div class="page-bar">
<span class="button2"><a href="#" onclick="flip('editform');return false;" class="linkbut">{tr}Create/edit contacts{/tr}</a></span>
</div>

<form action="tiki-contacts.php" method="post" id="editform" style="display:{ if $contactId}block{else}none{/if};">
<input type="hidden" name="locSection" value="contacts" />
<input type="hidden" name="contactId" value="{$contactId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}First Name{/tr}:</td><td><input type="text" maxlength="80" size="20" name="firstName" value="{$info.firstName|escape}" /></td>
<td rowspan="5">
{tr}Publish this contact to groups{/tr}:<br />
<select multiple="multiple" name="groups[]" size="6">
<option value=""></option>
{foreach item=group from=$groups}
<option value="{$group|escape}"{if in_array($group,$info.groups)} selected="selected"{/if}>{$group}</option>
{/foreach}
</select>
</td></tr>
<tr class="formcolor"><td>{tr}Last Name{/tr}:</td><td><input type="text" maxlength="80" size="20" name="lastName" value="{$info.lastName|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Email{/tr}:</td><td><input type="text" maxlength="80" size="20" name="email" value="{$info.email|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Nickname{/tr}:</td><td><input type="text" maxlength="80" size="20" name="nickname" value="{$info.nickname|escape}" /></td></tr>
<tr class="formcolor"><td></td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
<br /><br />
</form>

<div align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-contacts.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{tr}all{/tr}</a>
{section name=ix loop=$letters}
<a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;letter={$letters[ix]}">{$letters[ix]}</a>
{/section}
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'firstName_desc'}firstName_asc{else}firstName_desc{/if}">{tr}First Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastName_desc'}lastName_asc{else}lastName_desc{/if}">{tr}Last Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'nickname_desc'}nickname_asc{else}nickname_desc{/if}">{tr}Nickname{/tr}</a></td>
<td class="heading">{tr}Groups{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{foreach key=k item=channels from=$all}
{if count($channels)}
<tr><td colspan="6" style="font-size:80%;color:#999;">{tr}from{/tr} <b>{$k}</b></td></tr>
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].firstName}</td>
<td class="{cycle advance=false}">{$channels[user].lastName}</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}">{$channels[user].email}</a>
</td>
<td class="{cycle advance=false}">{$channels[user].nickname}</td>
<td class="{cycle advance=false}">{if isset($channels[user].groups)}{foreach item=it name=gr from=$channels[user].groups}{$it}{if $smarty.foreach.gr.index+1 ne $smarty.foreach.gr.last}, {/if}{/foreach}{else}&nbsp;{/if}</td>
<td class="{cycle advance=false}">&nbsp;
{if $k eq 'user_personal_contacts'}
<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}" 
title="{tr}edit{/tr}"><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit{/tr}' /></a><a 
href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}" style="margin-left:20px;"
title="{tr}delete{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
{elseif $tiki_p_admin eq 'y'}
<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}" style="margin-left:36px;"
title="{tr}delete{/tr}"><img src="pics/icons/cross_admin.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
{/if}
</td>
</tr>
{/section}
{/if}
{/foreach}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

