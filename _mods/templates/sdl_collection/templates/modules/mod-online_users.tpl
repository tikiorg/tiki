{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/modules/mod-online_users.tpl,v 1.1 2004-05-09 23:09:44 damosoft Exp $ *}

{tikimodule title="{tr}Online Users{/tr}" name="online_users"}
{section name=ix loop=$online_users}
{if $online_users[ix].user_information eq 'public'}
<a class="linkmodule" href="tiki-user_information.php?view_user={$online_users[ix].user}">{$online_users[ix].user}</a><br/>
{else}
{$online_users[ix].user}<br/>
{/if}
{/section}
{/tikimodule}

