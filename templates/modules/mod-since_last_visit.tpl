{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-since_last_visit.tpl,v 1.5 2003-08-15 21:13:55 redflo Exp $ *}

{if $user}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Since your last visit{/tr}" module_name="since_last_visit"}
</div>
<div class="box-data">
{tr}Since your last visit on{/tr}<br/>
<b>{$nvi_info.lastVisit|tiki_short_datetime|replace:"[":""|replace:"]":""}</b><br/>
{$nvi_info.images} {tr}new images{/tr}<br/>
{$nvi_info.pages} {tr}wiki pages changed{/tr}<br/>
{$nvi_info.files} {tr}new files{/tr}<br/>
{$nvi_info.comments} {tr}new comments{/tr}<br/>
{$nvi_info.users} {tr}new users{/tr}<br/>
</div>
</div>
{/if}
