{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_newsletters.tpl,v 1.40.2.3 2007-12-23 23:07:41 sylvieg Exp $ *}
<h1><a class="pagetitle" href="tiki-admin_newsletters.php">{tr}Admin newsletters{/tr}</a>
  
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Newsletters" target="tikihelp" class="tikihelp" title="{tr}Newsletters{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_newsletters.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin Newsletters Template{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit{/tr}' /></a>{/if}</h1>

<div class="navbar">
<span class="button2"><a class="linkbut" href="tiki-newsletters.php">{tr}List Newsletters{/tr}</a></span>
<span class="button2"><a class="linkbut" href="tiki-send_newsletters.php">{tr}Send Newsletters{/tr}</a></span>
</div>

<h2>{tr}Create/edit newsletters{/tr}</h2>
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName={$info.name|escape:"url"}&amp;objectType=newsletter&amp;permType=newsletters&amp;objectId={$info.nlId}">{tr}There are individual permissions set for this newsletter{/tr}</a><br /><br />
{/if}
<form action="tiki-admin_newsletters.php" method="post">
<input type="hidden" name="nlId" value="{$info.nlId|escape}" />
<input type="hidden" name="author" value="{$user|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$info.name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$info.description|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Users can subscribe/unsubscribe to this list{/tr}</td><td class="formcolor">
<input type="checkbox" name="allowUserSub" {if $info.allowUserSub eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Users can subscribe any email address{/tr}</td><td class="formcolor">
<input type="checkbox" name="allowAnySub" {if $info.allowAnySub eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Add unsubscribe instructions to each newsletter{/tr}</td><td class="formcolor">
<input type="checkbox" name="unsubMsg" {if $info.unsubMsg eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Validate email addresses{/tr}</td><td class="formcolor">
<input type="checkbox" name="validateAddr" {if $info.validateAddr eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Allow customized text message to be sent with the html version{/tr}</td><td class="formcolor">
<input type="checkbox" name="allowTxt" {if $info.allowTxt eq 'y'}checked="checked"{/if} /></td></tr>
{* <tr><td class="formcolor">{tr}Frequency{/tr}</td><td class="formcolor">
<select name="frequency">
{section name=ix loop=$freqs}
<option value="{$freqs[ix].t|escape}" {if $info.frequency eq $freqs[ix].t}selected="selected"{/if}>{$freqs[ix].i} {tr}days{/tr}</option>
{/section}
</select>
</td></tr>
*}
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Newsletters{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_newsletters.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'nlId_desc'}nlId_asc{else}nlId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'author_desc'}author_asc{else}author_desc{/if}">{tr}Author{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}">{tr}Users{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'editions_desc'}editions_asc{else}editions_desc{/if}">{tr}Editions{/tr}</a></td>
<td class="heading"><a class="tableheading">{tr}Drafts{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastSent_desc'}lastSent_asc{else}lastSent_desc{/if}">{tr}Last Sent{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">
<a class="link" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].nlId}" title="{tr}Remove{/tr}"><img src="pics/icons/cross.png" border="0" width="16" height="16" alt='{tr}Remove{/tr}' /></a>
</td>
<td class="{cycle advance=false}">{$channels[user].nlId}</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;nlId={$channels[user].nlId}" title="{tr}Edit{/tr}">{$channels[user].name}</a></td>
<td class="{cycle advance=false}">{$channels[user].description}</td>
<td class="{cycle advance=false}">{$channels[user].author}</td>
<td class="{cycle advance=false}">{$channels[user].users} ({$channels[user].confirmed})</td>
<td class="{cycle advance=false}">{$channels[user].editions}</td>
<td class="{cycle advance=false}">{$channels[user].drafts}</td>
<td class="{cycle advance=false}">{$channels[user].lastSent|tiki_short_datetime}</td>
<td class="{cycle}">
<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=newsletter&amp;permType=newsletters&amp;objectId={$channels[user].nlId}" title="{tr}Assign Permissions{/tr}"><img 
border="0" width="16" height="16" alt="{tr}Assign Permissions{/tr}" src="pics/icons/key{if $channels[user].individual eq 'y'}_active{/if}.png" /></a>
<a class="link" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;nlId={$channels[user].nlId}" title="{tr}Edit{/tr}"><img src="pics/icons/page_edit.png" border="0"  width="16" height="16" alt="{tr}Edit{/tr}" /></a>
<a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$channels[user].nlId}" title="{tr}Subscriptions{/tr}"><img src="pics/icons/group.png" border="0" width="16" height="16" alt='{tr}Subscriptions{/tr}' /></a>
<a class="link" href="tiki-send_newsletters.php?nlId={$channels[user].nlId}" title="{tr}Send Newsletter{/tr}"><img border="0" width="16" height="16" src="pics/icons/email.png" alt="{tr}Send Newsletter{/tr}" /></a>
<a class="link" href="tiki-newsletter_archives.php?nlId={$channels[user].nlId}" title="{tr}archives{/tr}"><img border="0" width="16" height="16" src="pics/icons/database.png" alt="{tr}archives{/tr}" /></a>
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_newsletters.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_newsletters.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_newsletters.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

