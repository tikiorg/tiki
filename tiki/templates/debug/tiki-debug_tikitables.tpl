{* $Header: /cvsroot/tikiwiki/tiki/templates/debug/tiki-debug_tikitables.tpl,v 1.2 2003-08-01 10:31:14 redflo Exp $ *}
{* Show Tiki tables *}

{if count($command_result) > 0} {* Can it be == 0 ?? *}
<table width="100%" id="tikitables">
<caption>Tables in Tiki DB</caption>
{section name=i loop=$command_result}
  {* make row new start *}
  {if ($smarty.section.i.index % 3) == 0}
    <tr>
  {/if}

  <td> {$command_result[i]} </td>

  {if ($smarty.section.i.index % 3) == 2}
    </tr>
  {/if}
{/section}

{* Close <TR> if still opened... *}
{if $smarty.section.i.index % 3}
  </tr>
{/if}

</table>
<small>Total {$smarty.section.i.total} tables matched</small>

{/if}{* if count($command_result) > 0 *}
