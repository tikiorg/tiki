{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/debug/tiki-debug_permissions.tpl,v 1.1 2004-01-07 04:21:56 musus Exp $ *}
{* Show permissions table *}

{if count($command_result) > 0} {* Can it be == 0 ?? *}
<table  id="permissions">
<caption>Permissions for {if $user}{$user}{else}anonymous{/if}</caption>
{section name=i loop=$command_result}
  {* make row new start *}
  {if ($smarty.section.i.index % 3) == 0}
    <tr>
  {/if}

  <td>
    <span class="o{if $command_result[i].value == 'y' }n{else}ff{/if}-option" title="{$command_result[i].description}">
      {$command_result[i].name}
    </span>
  </td>

  {if ($smarty.section.i.index % 3) == 2}
    </tr>
  {/if}
{/section}

{* Close <TR> if still opened... *}
{if $smarty.section.i.index % 3}
  </tr>
{/if}

</table>
<small>Total {$smarty.section.i.total} permissions matched</small>

{/if}{* if count($command_result) > 0 *}
