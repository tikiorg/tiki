{title help="Newsletters"}{tr}Sent editions{/tr}{if $nl_info}: {$nl_info.name|escape}{/if}{/title}

<div class="navbar">
	{if $tiki_p_list_newsletters eq "y"}
		{button href="tiki-newsletters.php" _text="{tr}List Newsletters{/tr}"}
	{/if}
	{if $tiki_p_subscribe_newsletters eq "y"}
		{button href="tiki-newsletters.php?nlId=$nlId&amp;info=1" _text="{tr}Subscribe{/tr}"}
	{/if}
	{if $tiki_p_send_newsletters eq "y"}
		{button href="tiki-send_newsletters.php?nlId=$nlId" _text="{tr}Send Newsletters{/tr}"} 
	{/if}
	{if $tiki_p_admin_newsletters eq "y"}
		{button href="tiki-admin_newsletters.php" _text="{tr}Admin Newsletters{/tr}"}
	{/if}
</div>

{if $edition}
<div class="title">
  <h2>{tr}Sent Edition{/tr}</h2>
</div>
<h3>{tr}Subject{/tr}</h3>
<div class="simplebox wikitext">{$edition.subject|escape}</div>

<h3>{tr}HTML version{/tr}</h3>
<div class="simplebox wikitext">{$edition.dataparsed}</div>

{if $allowTxt eq 'y' }
	<h3>{tr}Text version{/tr}</h3>
	{if $edition.datatxt}<div class="simplebox wikitext" >{$info.datatxt|escape|nl2br}</div>{/if}
	{if $txt}<div class="simplebox wikitext">{$txt|escape|nl2br}</div>{/if}
{/if}

{assign var="sent" value=$edition.users}
{tr}The newsletter was sent to {$sent} email addresses{/tr}<br />
{$edition.sent|tiki_short_datetime}
{/if}

{assign var=view_editions value='y'}
{assign var=cur value='ed'}
{assign var=bak value='dr'}
{assign var=sort_mode value=$ed_sort_mode}
{assign var=sort_mode_bak value='sent_desc'}
{assign var=offset value=$ed_offset}
{assign var=offset_bak value=0}
{assign var=find value=$ed_find}
{assign var=find_bak value=''}
{include file='sent_newsletters.tpl'}

{if $edition_errors}
  <h2>{tr}Errors:{/tr} {$edition_info.subject} / {$edition_info.sent|tiki_short_datetime}</h2>
  <a href="tiki-newsletter_archives.php?deleteError={$edition_info.editionId}" title="{tr}Delete errors{/tr}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
  <table class="normal">
    <tr>
      <th>{tr}Email{/tr}</th>
      <th>{tr}User{/tr}</th>
      <th>{tr}Status{/tr}</th>
    </tr>
    {cycle values="odd,even" print=false}
    {section name=ix loop=$edition_errors}
      <tr class="{cycle}">
        <td class="email">{$edition_errors[ix].email}</td>
        <td class="username">{$edition_errors[ix].login}</td>
        <td class="text">{if {$edition_errors[ix].error eq 'y'}{tr}Error{/tr}{else}{tr}Not sent{/tr}{/if}</td>
      </tr>
    {/section}
  </table>
{/if}
