{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-who_is_there.tpl,v 1.11 2005-03-12 16:51:00 mose Exp $ *}

{tikimodule title="{tr}Online users{/tr}" name="who_is_there" flip=$module_params.flip decorations=$module_params.decorations}
<div>
{$logged_users} 
{if $logged_users>1}
{tr}online users{/tr}
{elseif $logged_users>0}
{tr}online user{/tr}
{/if}
</div>
{section name=ix loop=$online_users}
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<a class="linkmodule" href="messu-compose.php?to={$online_users[ix].user}" title="{tr}Send a message to{/tr} {$online_users[ix].user}"><img src="img/icons/icon_ultima.gif" width="18" height="9" hspace="2" vspace="0" border="0" alt="{tr}Send message{/tr}" /></a>
{/if}
{if $online_users[ix].user_information eq 'public'}
{math equation="x - y" x=$smarty.now y=$online_users[ix].timestamp assign=idle}
{$online_users[ix].user|userlink:"linkmodule":$idle}<br />
{else}
{$online_users[ix].user|userlink:"linkmodule"}<br />
{/if}
{/section}
{/tikimodule}

