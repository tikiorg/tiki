{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-who_is_there.tpl,v 1.14 2006-03-16 13:43:13 sylvieg Exp $ *}

{tikimodule title="{tr}Online users{/tr}" name="who_is_there" flip=$module_params.flip decorations=$module_params.decorations}
<div>
{if $cluster}
{foreach from=$logged_cluster_users item=cant key=tikihost}
{$cant}
{if $cant >1}
{tr}online users{/tr}
{elseif $cant>0}
{tr}online user{/tr}
{/if}
{tr}on host{/tr} {$tikihost}
</div>
{/foreach}
{foreach key=ix item=online_user from=$online_users}
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
{if $online_user.allowMsgs eq 'n'}
<img src="img/icons/icon_ultima_no.gif" width="18" height="9" hspace="2" vspace="0" border="0" alt="-&gt;" />
{else}
<a class="linkmodule" href="messu-compose.php?to={$online_user.user}" title="{tr}Send a message to{/tr} {$online_user.user}"><img src="img/icons/icon_ultima.gif" width="18" height="9" hspace="2" vspace="0" border="0" alt="{tr}Send a message{/tr}" /></a>
{/if}
{/if}
{if $online_user.user_information eq 'public'}
{math equation="x - y" x=$smarty.now y=$online_user.timestamp assign=idle}
{$online_user.user|userlink:"linkmodule":$idle}
{else}
{$online_user.user|userlink:"linkmodule"}
{/if}
({$online_user.tikihost})<br />
{/foreach}
{else}
{$logged_users} 
{if $logged_users>1}
{tr}online users{/tr}
{elseif $logged_users>0}
{tr}online user{/tr}
{/if}
</div>
{foreach key=ix item=online_user from=$online_users}
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
{if $online_user.allowMsgs eq 'n'}
<img src="img/icons/icon_ultima_no.gif" width="18" height="9" hspace="2" vspace="0" border="0" alt="-&gt;" />
{else}
<a class="linkmodule" href="messu-compose.php?to={$online_user.user}" title="{tr}Send a message to{/tr} {$online_user.user}"><img src="img/icons/icon_ultima.gif" width="18" height="9" hspace="2" vspace="0" border="0" alt="{tr}Send a message{/tr}" /></a>
{/if}
{/if}
{if $online_user.user_information eq 'public'}
{math equation="x - y" x=$smarty.now y=$online_user.timestamp assign=idle}
{$online_user.user|userlink:"linkmodule":$idle}<br />
{else}
{$online_user.user|userlink:"linkmodule"}<br />
{/if}
{/foreach}
{/if}
{/tikimodule}

