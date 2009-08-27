<h1>{tr}My Workspaces{/tr}</h1>

{if not empty($listWS) }
    <table class = admin>
    <tr>
       <th>{tr}Name{/tr}</th>
       <th>{tr}Description{/tr}</th>
       <th>{tr}Path{/tr}</th>
    </tr>
    {cycle print=false values="even,odd"}
    {foreach from=$listWS item=data}
	    <tr class="{cycle}">
	    <td><a href="tiki-my-workspaces.php?showWS={$data.ws_id}" title="{$data.name}" >{$data.name}</a></td>
		    <td>{$data.description}</td>
		    <td>{$data.wspath}</td>
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
