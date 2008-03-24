{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_newsletter_subscriptions.tpl,v 1.32.2.8 2008-02-05 16:14:43 jyhem Exp $ *}
<h1><a class="pagetitle" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}">{tr}Admin newsletter subscriptions{/tr}</a></h1>

<div class="navbar">
<a class="linkbut" href="tiki-newsletters.php">{tr}List Newsletters{/tr}</a>
 <a class="linkbut" href="tiki-admin_newsletters.php?nlId={$nlId}">{tr}Admin Newsletters{/tr}</a> 
 <a class="linkbut" href="tiki-send_newsletters.php?nlId={$nlId}">{tr}Send Newsletters{/tr}</a>
</div>

<table class="normal">
<tr>
  <td colspan="2" class="heading">{tr}Newsletter{/tr}</td>
</tr>
<tr>
  <td class="even" width="30%">{tr}Name{/tr}:</td>
  <td class="even">{$nl_info.name}</td>
</tr>
<tr>
  <td class="even">{tr}Description{/tr}:</td>
  <td class="even">{$nl_info.description}</td>
</tr>
</table>

<h2>{tr}Add a subscription newsletters{/tr}</h2>
<form action="tiki-admin_newsletter_subscriptions.php" method="post">
<input type="hidden" name="nlId" value="{$nlId|escape}" />
<table class="normal">
<tr><td class="formcolor" width="30%">{tr}Email{/tr}:</td><td colspan="2" class="formcolor"><textarea cols="70" rows="6" wrap="soft" name="email"></textarea>
<br /><i>{tr}You can add several email addresses by separating them with commas.{/tr}</i>
</td></tr>
<tr><td class="formcolor">{tr}User{/tr}:</td><td class="formcolor">
<select name="subuser">
<option value="">---</option>
{foreach key=id item=one from=$users}
<option value="{$one|escape}">{$one|escape}</option>
{/foreach}
</select>
</td>
<td class="formcolor" rowspan="3" valign="middle">
<table border="0" cellspacing="0" cellpadding="0"><tr><td>{tr}Add email:{/tr}</td><td><input type="radio" name="addemail" value="y" /></td></tr><tr><td>{tr}Add user:{/tr}</td><td><input type="radio" name="addemail" value="n" checked="checked" /></td></tr></table>
</td></tr>
<tr><td class="formcolor">{tr}All users{/tr}:</td><td class="formcolor"><input type="checkbox" name="addall" /></td></tr>
<tr><td class="formcolor">{tr}Group users{/tr}:</td><td class="formcolor">
<select name="group">
<option value="">---</option>
{section name=x loop=$groups}
<option value="{$groups[x]|escape}">{$groups[x]|escape}</option>
{/section}
</select><br /><i>{tr}Group subscription also subscribes included groups{/tr}</i>
</td></tr>
{if $nl_info.validateAddr eq "y"}
<tr><td class="formcolor">{tr}Don't send confirmation mail{/tr}</td><td colspan="2" class="formcolor"><input type="checkbox" name="confirmEmail" /></td></tr>
{/if}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor" colspan="2"><i>{tr}The user email will be refreshed at each newsletter sending{/tr}</i></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor" colspan="2"><input type="submit" name="add" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>

{if $tiki_p_batch_subscribe_email eq "y" && $tiki_p_subscribe_email eq "y"} 	 
<h2>{tr}Batch e-mail subscribe{/tr}</h2> 	 
<form action="tiki-admin_newsletter_subscriptions.php" method="post" enctype="multipart/form-data"> 	 
<input type="hidden" name="nlId" value="{$nlId|escape}" /> 	 
<table class="normal"> 	 
<tr><td class="formcolor" width="30%">{tr}File{/tr}:</td><td class="formcolor" colspan="2"><input type="file" name="batch_subscription" /><br /><i>{tr}txt file, one e-mail per line{/tr}</td></tr> 	 
<tr><td class="formcolor">&nbsp;</td><td class="formcolor" colspan="2"><input type="submit" name="addbatch" value="{tr}Add{/tr}" /></td></tr> 	 
</table> 	 
</form> 	 
{/if}

<h2>{tr}Export Subscriber Emails{/tr}</h2>
<form action="tiki-admin_newsletter_subscriptions.php" method="post">
<input type="hidden" name="nlId" value="{$nlId|escape}" /> 
<table class="normal">
<tr><td class="formcolor">&nbsp;</td><td class="formcolor" colspan="2"><input type="submit" name="export" value="{tr}Export{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Subscribe group{/tr}</h2>
<form action="tiki-admin_newsletter_subscriptions.php" method="post">
<input type="hidden" name="nlId" value="{$nlId|escape}" />
<table class="normal">
<tr><td class="formcolor" width="30%">{tr}Group{/tr}:</td><td class="formcolor" colspan="2">
<select name="group">
<option value="">---</option>
{section name=x loop=$groups}
<option value="{$groups[x]|escape}">{$groups[x]|escape}</option>
{/section}
</select><br /><i>{tr}Included group, group users and emails will be refreshed at each newsletter sending{/tr}</i>
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor" colspan="2"><input type="submit" name="addgroup" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Add subscribers of another newsletter{/tr}</h2>
<form action="tiki-admin_newsletter_subscriptions.php" method="post">
<input type="hidden" name="nlId" value="{$nlId|escape}" />
<table class="normal">
<tr><td class="formcolor" width="30%">{tr}Newsletter{/tr}:</td><td class="formcolor" colspan="2">
<select name="included">
<option value="">---</option>
{section name=x loop=$newsletters}
{if $nlId ne $newsletters[x].nlId}<option value="{$newsletters[x].nlId|escape}">{$newsletters[x].name|escape}</option>{/if}
{/section}
</select><br />
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor" colspan="2"><input type="submit" name="addincluded" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Subscriptions{/tr}</h2>
{* groups------------------------------------ *}
{if $nb_groups > 0}
<div  align="center">
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;offset={$offset_g}&amp;sort_mode_g={if $sort_mode_g eq 'groupName_asc'}groupName_desc{else}groupName_asc{/if}">{tr}Group{/tr}</a></td><td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$groups_g}
<tr>
<td class="{cycle advance=false}">{$groups_g[ix].groupName|escape}</td>
<td class="{cycle}"><a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$groups_g[ix].nlId}&amp;group={$groups_g[ix].groupName}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
</tr>
{/section}
</table>
</div>
{/if}
{* /groups------------------------------------ *}

{* included------------------------------------ *}
{if $nb_included > 0}
<div  align="center">
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;offset={$offset_g}&amp;sort_mode_i={if $sort_mode_i eq 'name_asc'}name_desc{else}name_asc{/if}">{tr}Newsletter{/tr}</a></td><td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{foreach key=incId item=incName from=$included_n}
<tr>
<td class="{cycle advance=false}"><a href="tiki-admin_newsletter_subscriptions.php?nlId={$incId}">{$incName|escape}</a></td>
<td class="{cycle}"><a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$nlId}&amp;included={$incId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
</tr>
{/foreach}
</table>
</div>
{/if}
{* /included------------------------------------ *}
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_newsletter_subscriptions.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="nlId" value="{$nlId}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}eMail{/tr} - {tr}User{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'valid_desc'}valid_asc{else}valid_desc{/if}">{tr}Valid{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'subscribed_desc'}subscribed_asc{else}subscribed_desc{/if}">{tr}subscribed{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{if $channels[user].isUser == "y"}{$channels[user].email|userlink}{else}{$channels[user].email}{/if}</td>
<td class="{cycle advance=false}">{if $channels[user].valid == "n"}
<a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;valid={$channels[user].nlId}&amp;{if $channels[user].isUser eq "y"}user{else}email{/if}={$channels[user].email}" title="{tr}Valid{/tr}">{tr}No{/tr}</a>
{else}{tr}Yes{/tr}{/if}</td>
<td class="{cycle advance=false}">{$channels[user].subscribed|tiki_short_datetime}</td>
<td class="{cycle}">
   <a class="link" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].nlId}&amp;{if $channels[user].isUser eq "y"}subuser{else}email{/if}={$channels[user].email}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{if $cant_pages > 0}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{/if}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_newsletter_subscriptions.php?nlId={$nlId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

