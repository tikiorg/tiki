{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-online_users.tpl,v 1.2 2003-08-07 20:56:53 zaufi Exp $ *}

<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Online users{/tr}" module_name="online_users"}
</div>
<div class="box-data">
{section name=ix loop=$online_users}
{if $online_users[ix].user_information eq 'public'}
<a class="linkmodule" href="tiki-user_information.php?view_user={$online_users[ix].user}">{$online_users[ix].user}</a><br/>
{else}
{$online_users[ix].user}<br/>
{/if}
{/section}
</div>
</div>

