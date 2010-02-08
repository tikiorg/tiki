{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="users_rank" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if !empty($users_rank)}{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section loop=$users_rank name=u}
  <li>
    {*<div class="licomponent" style="display:inline">{$users_rank[u].position})&nbsp;</div>*}
    <div class="licomponent" style="display:inline">{$users_rank[u].score}</div>
    <div class="licomponent" style="display:inline">&nbsp;{$users_rank[u].login|userlink}</div>
  </li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/if}
{if $prefs.feature_friends eq 'y'}
<a style="margin-left: 20px" href="tiki-list_users.php">{tr}More{/tr}...</a>
{/if}

{/tikimodule}
