{* $Header: /cvsroot/tikiwiki/tiki/templates/debug/tiki-debug_permissions.tpl,v 1.1 2003-07-13 00:11:00 zaufi Exp $ *}
{* Show help for debugger commands *}

{if count($command_result) > 0} {* Can it be == 0 ?? *}
<table border="0">
<tr>
<small>Current user: {if $user}{$user}{else}anonymous{/if}</small>
<hr>
{section name=i loop=$command_result}
  {* make row new start *}
  {if ($smarty.section.i.index % 3) == 0}
    <tr>
  {/if}

  <td>
    <span class="o{if $command_result[i].value == 'y' }n{else}ff{/if}-option">
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
<hr>
<small>Total {$smarty.section.i.total} permissions matched</small>

{/if}{* if count($command_result) > 0 *}