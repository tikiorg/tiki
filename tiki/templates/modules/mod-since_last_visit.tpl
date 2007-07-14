{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-since_last_visit.tpl,v 1.13 2007-07-14 20:33:35 nyloth Exp $ *}

{if $user}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Since your last visit{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="since_last_visit" flip=$module_params.flip decorations=$module_params.decorations}
{tr}Since your last visit on{/tr}<br />
<b>{$nvi_info.lastVisit|tiki_short_datetime|replace:"[":""|replace:"]":""}</b><br />
{$nvi_info.images} {tr}new images{/tr}<br />
{$nvi_info.pages} {tr}wiki pages changed{/tr}<br />
{$nvi_info.files} {tr}new files{/tr}<br />
{$nvi_info.comments} {tr}new comments{/tr}<br />
{$nvi_info.trackers} {tr}new tracker items{/tr}<br />
{$nvi_info.calendar} {tr}new calendar events{/tr}<br />
{$nvi_info.users} {tr}new users{/tr}<br />
{/tikimodule}
{/if}
