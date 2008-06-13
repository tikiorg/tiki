<h1><a href="tiki-newsletters.php" class="pagetitle">{tr}Newsletters{/tr}</a></h1>
{if $subscribed eq 'y'}
<div class="simplebox highlight">
{tr}Thanks for your subscription. You will receive an email soon to confirm your subscription. No newsletters will be sent to you until the subscription is confirmed.{/tr}</div>
{/if}
{if $unsub eq 'y'}
<div class="highlight">
{tr}Your email address was removed from the list of subscriptors.{/tr}</div>
{/if}

{if $confirm eq 'y'}
<table class="normal">
<tr>
  <td colspan="2" class="heading highlight">{tr}Subscription confirmed!{/tr}</td>
</tr>
<tr>
  <td class="even">{tr}Name{/tr}:</td>
  <th class="even">{$nl_info.name|escape}</th>
</tr>
<tr>
  <td class="even">{tr}Description{/tr}:</td>
  <td class="even">{$nl_info.description|escape|nl2br}</td>
</tr>
</table>
<br />
{/if}

{if $subscribe eq 'y'}
<form method="post" action="tiki-newsletters.php">
<input type="hidden" name="nlId" value="{$nlId|escape}" />
<table class="normal">
<tr>
  <td colspan="2" class="heading">{tr}Subscribe to Newsletter{/tr}</td>
</tr>
<tr>
  <td class="even">{tr}Name{/tr}:</td>
  <th class="even">{$nl_info.name|escape}</th>
</tr>
<tr>
  <td class="even">{tr}Description{/tr}:</td>
  <td class="even">{$nl_info.description|escape|nl2br}</td>
</tr>
{if ($nl_info.allowUserSub eq 'y') or ($tiki_p_admin_newsletters eq 'y')}
{if $tiki_p_subscribe_email eq 'y' and (($nl_info.allowAnySub eq 'y' and $user) || !$user)}
<tr>
  <td class="even">{tr}Email:{/tr}</td>
  <td class="even"><input type="text" name="email" value="{$email|escape}" /></td>
</tr>
{else}
  <input type="hidden" name="email" value="{$email|escape}" />
{/if}
<tr>
  <td class="even">&nbsp;</td>
  <td class="even"><input type="submit" name="subscribe" value="{tr}Subscribe to This Newsletter{/tr}" /></td>
</tr>
{/if}
</table>
</form>
{/if}

{if $showlist eq 'y'}
<h2>{tr}Available Newsletters{/tr}</h2>
<div align="center">
{if $channels or $find ne''}
  {include file='find.tpl' _sort_mode='y'}
{/if}
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletters.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading" width="80px">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
{if $channels[user].tiki_p_subscribe_newsletters eq 'y'}
<tr class="{cycle}">
<td><a class="tablename" href="tiki-newsletters.php?nlId={$channels[user].nlId}&amp;info=1" title="{tr}Subscribe to Newsletter{/tr}">{$channels[user].name}</a></td>
<td>{$channels[user].description|escape|nl2br}</td>
<td>{if $channels[user].tiki_p_subscribe_newsletters eq 'y'}<a class="link" href="tiki-newsletters.php?nlId={$channels[user].nlId}&amp;info=1" title="{tr}Subscribe to Newsletter{/tr}">{icon _id='group' alt='{tr}Subscribe to Newsletter{/tr}'}</a>{/if}
{if $channels[user].tiki_p_admin_newsletters eq 'y'}&nbsp;<a class="link" href="tiki-admin_newsletters.php?nlId={$channels[user].nlId}" title="{tr}Admin{/tr}">{icon _id='page_edit' alt="{tr}Admin{/tr}"}</a>{/if}
{if $channels[user].tiki_p_send_newsletters eq 'y'}&nbsp;<a class="link" href="tiki-send_newsletters.php?nlId={$channels[user].nlId}" title="{tr}Send Newsletter{/tr}">{icon _id='email' alt="{tr}Send Newsletter{/tr}"}</a>{/if}</td>
</tr>
{/if}
{sectionelse}
<tr><td class="odd" colspan="3">{tr}No records{/tr}</td></tr>
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
{/if}
</div>
</div>
