{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-online_users.tpl,v 1.6 2005-03-12 16:51:00 mose Exp $ *}

{tikimodule title="{tr}Online users{/tr}" name="online_users" flip=$module_params.flip decorations=$module_params.decorations}
{section name=ix loop=$online_users}
{if $online_users[ix].user_information eq 'public'}
{$online_users[ix].user|userlink}<br />
{else}
{$online_users[ix].user}<br />
{/if}
{/section}
{/tikimodule}

