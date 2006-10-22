{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tiki_workspaces_module title="{tr}Workspace Members{/tr}" name="workspaces_who_is_there" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
<div class="online_users">
{tr}online users{/tr}
</div>
{foreach key=key item=online_user from=$online_users}
<div>
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<a class="linkmodule" href="messu-compose.php?to={$online_user.login}" title="{tr}Send a message to{/tr} {$online_users[ix].login}"><img src="images/workspaces/user_red.gif" hspace="2" vspace="0" border="0" alt="{tr}Send message{/tr}" /></a>
{/if}
{$online_user.login|userlink:"linkmodule"}
</div>
{/foreach}
<div class="online_users">
{tr}offline users{/tr}
</div>
{foreach key=key item=offline_user from=$offline_users}
<div>
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<a class="linkmodule" href="messu-compose.php?to={$offline_user.login}" title="{tr}Send a message to{/tr} {$offline_users[ix].login}"><img src="images/workspaces/user_blue.gif" hspace="2" vspace="0" border="0" alt="{tr}Send message{/tr}" /></a>
{/if}
{$offline_user.login|userlink:"linkmodule"}
</div>
{/foreach}
{/tiki_workspaces_module}

