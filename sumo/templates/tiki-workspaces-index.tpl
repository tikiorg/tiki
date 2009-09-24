{remarksbox type="info" title="{tr}Welcome{/tr}"}
{tr}Workspaces have been successfully enabled. Please note that this feature is under development. More information in the {/tr} <a class="rbox-link" href="http://dev.tikiwiki.org/workspace">{tr}Workspaces wiki page{/tr}</a>.
{/remarksbox}

{if not empty($listWS) }
<table><tr><td> 
<br /> 
<div class='titlebar'><h3>{tr}My Workspaces{/tr}</h3></div><br />
<table class = admin>
<tr>
   <th>{tr}Name{/tr}</th>
   <th>{tr}Description{/tr}</th>
</tr> 
{cycle print=false values="even,odd"}
{foreach from=$listWS item=data}
	<tr class="{cycle}">
		<td><a href ="tiki-my-workspaces.php?showWS={$data.ws_id}" title="{$data.name}">{$data.name}</a></td>
		<td>{$data.description}</td>
	</tr>
{/foreach}
</table>
{if ($wsQuantity) >= 10 }
<br />
<a href="tiki-my-workspaces.php" title="{tr}List my Workspaces{/tr}" class="button">{tr}List my Workspaces{/tr}</a>
{/if}
{/if}
</td><td valign="top" width="40%" > 
<br /> 
<div class='titlebar'><h3>{tr}Featured Workspaces{/tr}</h3></div><br /> 
<strong>In the future ...</strong><br /> 
<ul><li>You will see a list of Workspaces to join.<br /> 
</li><li>All of these Workspaces will be open and public.<br /> 
</li><li>If a Workspace is private then it won't be listed here. You will need an invitation to join it.<br /> 
</li></ul> 
</td></tr></table>
