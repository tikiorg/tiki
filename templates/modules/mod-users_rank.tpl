{* $Id$ *}

{if $prefs.feature_score eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="<a href=\"tiki-list_users.php\">{tr}Top users{/tr}</a>"}{/if}
{tikimodule title=$tpl_module_title name="users_rank" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<table border="0" cellpadding="0" cellspacing="0">
{section loop=$users_rank name=u}
  <tr>
    <td class="module">{$users_rank[u].position})&nbsp;</td>
    <td class="module">{$users_rank[u].score}</td>
    <td class="module">&nbsp;{$users_rank[u].login|userlink}</td>
  </tr>
{/section}
</table>
{/tikimodule}
{/if}
