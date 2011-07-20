{* $Id$ *}
{* Show features *}

{if count($command_result) > 0} {* Can it be == 0 ?? *}
<table  id="features">
<caption>{tr}Features state{/tr}</caption>
<tr>
{section name=i loop=$command_result}
  {* make row new start *}
  {if ($smarty.section.i.index % 3) == 0}
    <tr>
  {/if}

  <td>
    <span class="o{if $command_result[i].value == 'y'}n{else}ff{/if}-option">
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
<small>{tr}Total{/tr} {$smarty.section.i.total} {tr}features matched{/tr}</small>

{/if}{* if count($command_result) > 0 *}
