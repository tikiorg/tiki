{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/modules/mod-since_last_visit.tpl,v 1.1 2004-05-09 23:09:44 damosoft Exp $ *}

{if $user}
{tikimodule title="{tr}Since Your Last Login{/tr}" name="since_last_visit"}
{tr}Since your last visit on{/tr}<br/>
<b>{$nvi_info.lastVisit|tiki_short_datetime|replace:"[":""|replace:"]":""}</b><br/>
{$nvi_info.images} {tr}new images{/tr}<br/>
{$nvi_info.pages} {tr}wiki pages changed{/tr}<br/>
{$nvi_info.files} {tr}new files{/tr}<br/>
{$nvi_info.comments} {tr}new comments{/tr}<br/>
{$nvi_info.users} {tr}new users{/tr}<br/>
{/tikimodule}
{/if}
