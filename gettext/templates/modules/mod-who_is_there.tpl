{tikimodule error=$module_params.error title=$tpl_module_title name="who_is_there" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $count}
{if $cluster}
{foreach from=$logged_cluster_users item=cant key=tikihost}
<div>
{$cant}
{if $cant >1}
{tr}online users{/tr}
{elseif $cant>0}
{tr}online user{/tr}
{/if}
{tr}on host{/tr} {$tikihost}
</div>
{/foreach}
{else}
<div>
{$logged_users} 
{if $logged_users>1}
{tr}online users{/tr}
{elseif $logged_users>0}
{tr}online user{/tr}
{/if}
</div>
{/if}
{/if}
{if $list}
{foreach key=ix item=online_user from=$online_users}
{if $user and $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
{if $online_user.allowMsgs eq 'n'}
<img src="img/icons/icon_ultima_no.gif" width="18" height="9" hspace="2" vspace="0" alt="{tr}User does not accept messages{/tr}" />
{else}
<a class="linkmodule" href="messu-compose.php?to={$online_user.user}" title="{tr}Send a message to{/tr} {$online_user.user}"><img src="img/icons/icon_ultima.gif" width="18" height="9" hspace="2" vspace="0" alt="{tr}Send a message{/tr}" /></a>
{/if}
{/if}
{if $online_user.user_information eq 'public'}
{math equation="x - y" x=$smarty.now y=$online_user.timestamp assign=idle}
{$online_user.user|userlink:"linkmodule":$idle}
{else}
{$online_user.user|escape}
{/if}
{if $cluster}({$online_user.tikihost}){/if}
<br />
{/foreach}
{/if}
{/tikimodule}

