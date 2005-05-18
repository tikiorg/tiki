{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-since_last_visit.tpl,v 1.10 2005-05-18 11:03:31 mose Exp $ *}

{if $user}
{tikimodule title="{tr}Since your last visit{/tr}" name="since_last_visit" flip=$module_params.flip decorations=$module_params.decorations}
{tr}Since your last visit on{/tr}<br />
<b>{$nvi_info.lastVisit|tiki_short_datetime|replace:"[":""|replace:"]":""}</b><br />
{$nvi_info.images} {tr}new images{/tr}<br />
{$nvi_info.pages} {tr}wiki pages changed{/tr}<br />
{$nvi_info.files} {tr}new files{/tr}<br />
{$nvi_info.comments} {tr}new comments{/tr}<br />
{$nvi_info.users} {tr}new users{/tr}<br />
{/tikimodule}
{/if}
