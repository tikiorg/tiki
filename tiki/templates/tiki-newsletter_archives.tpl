<h1><a class="pagetitle" href="tiki-newsletter_archives.php?nlId={$nlId}">{tr}Sent editions{/tr}{if $nl_info}: {$nl_info.name}{/if}</a>
 {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Newsletters" target="tikihelp" class="tikihelp" title="{tr}Newsletters{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}
 {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-newsletter_archives.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin Newsletters Template{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}Edit{/tr}' /></a>{/if}</h1>

<span class="button2"><a class="linkbut" href="tiki-newsletters.php">{tr}List Newsletters{/tr}</a></span>
{if $tiki_p_subscribe_newsletters eq "y"}<span class="button2"><a class="linkbut" href="tiki-newsletters.php?nlId={$nlId}&amp;info=1">{tr}subscribe{/tr}</a></span>{/if}
{if $tiki_p_send_newsletters eq "y"}<span class="button2"><a class="linkbut" href="tiki-send_newsletters.php?nlId={$nlId}">{tr}Send Newsletters{/tr}</a></span>{/if}
{if $tiki_p_admin_newsletters eq "y"}<span class="button2"><a class="linkbut" href="tiki-admin_newsletters.php">{tr}Admin Newsletters{/tr}</a></span>{/if}

{if $edition}
<h2>{tr}Sent edition{/tr}</h2>
{tr}Subject{/tr}
<div class="wikitext">{$edition.subject}</div>
{tr}Data{/tr}
<div class="wikitext">{$edition.dataparsed}</div>
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
{include file=sent_newsletters.tpl }

{if $edition_errors}
<h2>{tr}Errors:{/tr} {$edition_info.subject} / {$edition_info.sent|tiki_short_datetime}</h2>
<a href="tiki-newsletter_archives.php?deleteError={$edition_info.editionId}" title="{tr}Delete errors{/tr}"><img src="pics/icons/cross.png" border="0" width="16" height="16" alt='{tr}Remove{/tr}' /></a>
<table class="normal">
<tr><th>{tr}Email{/tr}</th><th>{tr}User{/tr}</th><th>{tr}Status{/tr}</th></tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$edition_errors}
<tr><td class="{cycle advance=false}">{$edition_errors[ix].email}</td><td class="{cycle advance=false}">{$edition_errors[ix].login}</td><td class="{cycle}">{if {$edition_errors[ix].error eq 'y'}{tr}Error{/tr}{else}{tr}Not sent{/tr}{/if}</td></tr>
{/section}
</table>
{/if}
