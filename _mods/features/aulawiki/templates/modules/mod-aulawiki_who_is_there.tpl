{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tikimodule title="{tr}Workspace Members{/tr}" name="who_is_there" flip=$module_params.flip decorations=$module_params.decorations}
<div class="online_users">
{tr}online users{/tr}
</div>
{section name=ix loop=$online_users}
<div>
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<a class="linkmodule" href="messu-compose.php?to={$online_users[ix].login}" title="{tr}Send a message to{/tr} {$online_users[ix].login}"><img src="images/aulawiki/user_red.gif" hspace="2" vspace="0" border="0" alt="{tr}Send message{/tr}" /></a>
{/if}
{$online_users[ix].login|userlink:"linkmodule"}
</div>
{/section}
<div class="online_users">
{tr}offline users{/tr}
</div>
{section name=ix loop=$offline_users}
<div>
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<a class="linkmodule" href="messu-compose.php?to={$offline_users[ix].login}" title="{tr}Send a message to{/tr} {$offline_users[ix].login}"><img src="images/aulawiki/user_blue.gif" hspace="2" vspace="0" border="0" alt="{tr}Send message{/tr}" /></a>
{/if}
{$offline_users[ix].login|userlink:"linkmodule"}
</div>
{/section}
{/tikimodule}

