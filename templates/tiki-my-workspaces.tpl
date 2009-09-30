<h1>{$title}</h1>

{if $viewWS }
 <a href="./tiki-my-workspaces.php" title="Back to My Workspaces" class="button">Back</a>
 <a href="./tiki-switch_perspective.php?perspective={$switchPsp}" title="Enter in this Workspace" class="button">Enter in this Workspace</a>
{else}
  {if not empty($listWS) }
  <a href="./tiki-switch_perspective.php?perspective=0" title="Return to Tiki" class="button">Set Tiki as its normal status</a>
    <table class = admin>
    <tr>
       <th>{tr}Name{/tr}</th>
       <th>{tr}Description{/tr}</th>
    </tr>
    {cycle print=false values="even,odd"}
    {foreach from=$listWS item=data}
	    <tr class="{cycle}">
	    <td><a href="tiki-my-workspaces.php?viewWS={$data.categId}" title="{$data.name}" >{$data.name}</a></td>
	    <td>{$data.description}</td>
	    </tr>
    {/foreach}
    </table>
    <br />
    {if not empty($prev_page)}
	    <a class="button" href = {$prev_page}>{tr}Previous{/tr}</a>
    {/if}
    {if not empty($next_page)}
	    <a class="button" href = {$next_page}>{tr}Next{/tr}</a>
    {/if}
  {else}
	{tr}You do not belong to any workspace.{/tr}
  {/if}
{/if}
