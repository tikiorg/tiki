<a class="pagetitle" href="tiki-admin_newsletter_subscriptions.php">Admin newsletter subscriptions</a><br/><br/>
[<a class="link" href="tiki-newsletters.php">{tr}list newsletters{/tr}</a>
 |<a class="link" href="tiki-admin_newsletters.php">{tr}admin newsletters{/tr}</a> 
]<br/><br/>
<table class="normal">
<tr>
  <td colspan="2" class="heading">{tr}Newsletter{/tr}</td>
</tr>
<tr>
  <td class="even">{tr}Name{/tr}:</td>
  <td class="even">{$nl_info.name}</td>
</tr>
<tr>
  <td class="even">{tr}Description{/tr}:</td>
  <td class="even">{$nl_info.description}</td>
</tr>
</table>

<h2>{tr}Add a subscription newsletters{/tr}</h2>
<form action="tiki-admin_newsletter_subscriptions.php" method="post">
<input type="hidden" name="nlId" value="{$info.nlId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Email{/tr}:</td><td class="formcolor"><input type="text" name="email" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>Subscriptions</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_newsletter_subscriptions.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletter_subscriptions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}email{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletter_subscriptions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'valid_desc'}valid_asc{else}valid_desc{/if}">{tr}valid{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_newsletter_subscriptions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'subscribed_desc'}subscribed_asc{else}subscribed_desc{/if}">{tr}subscribed{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].email}</td>
<td class="{cycle advance=false}">{$channels[user].valid}</td>
<td class="{cycle advance=false}">{$channels[user].subscribed|date_format:"%a %d of %b [%H:%M]"}</td>
<td class="{cycle}">
   <a class="link" href="tiki-admin_newsletter_subscriptions.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].nlId}&amp;email={$channels[user].email}">{tr}remove{/tr}</a>
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_newsletter_subscriptions.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_newsletter_subscriptions.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>

