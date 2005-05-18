{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-online_users.tpl,v 1.7 2005-05-18 11:03:30 mose Exp $ *}

{tikimodule title="{tr}Online users{/tr}" name="online_users" flip=$module_params.flip decorations=$module_params.decorations}
{foreach key=ix from=$online_users item=online_user}
{if $online_user.user_information eq 'public'}
{$online_user.user|userlink}<br />
{else}
{$online_user.user}<br />
{/if}
{/foreach}
{/tikimodule}

