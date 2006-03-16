{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-online_users.tpl,v 1.9 2006-03-16 13:43:13 sylvieg Exp $ *}

{tikimodule title="{tr}Online users{/tr}" name="online_users" flip=$module_params.flip decorations=$module_params.decorations}
{foreach key=ix from=$online_users item=online_user}
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
    {if $online_user.allowMsgs eq 'n'}
	<img src="img/icons/icon_ultima_no.gif" width="18" height="9" hspace="2" vspace="0" border="0" alt="-&gt;" />
    {else}
	<a class="linkmodule" href="messu-compose.php?to={$online_user.user}" title="{tr}Send a message to{/tr} {$online_user.realName}"><img src="img/icons/icon_ultima.gif" width="18" height="9" hspace="2" vspace="0" border="0" alt="{tr}Send a message{/tr}" /></a>
{/if}
{/if}
{if $online_user.user_information eq 'public'}
    {$online_user.user|userlink:"link":"not_set":$online_user.realName}<br />
{else}
    {$online_user.realName|default:$online_user.user}<br />
{/if}
{/foreach}
{/tikimodule}

