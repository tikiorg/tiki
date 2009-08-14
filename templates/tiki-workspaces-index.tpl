{remarksbox type="info" title="{tr}Welcome{/tr}"}
{tr}You have succesfully enabled  Workspaces in TikiWiki. This feature is <em>under development</em>, so please don't expect nothing if not works properly. If you want to get more info go to the {/tr} <a class="rbox-link" href="http://dev.tikiwiki.org/workspace">{tr}Workspaces wiki page{/tr}</a>.
{/remarksbox}

{if not empty($listWS) }
<table><tr><td> 
<br /> 
<div class='titlebar'><h3>My Workspaces</h3></div><br />
<table class = admin>
<tr>
   <th>Name</th>
   <th>Description</th>
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
<a href="tiki-my-workspaces.php" title="See more Workspaces" class="button">See more Workspaces ...</a>
{/if}
{/if}
</td><td valign="top" width="40%" > 
<br /> 
<div class='titlebar'><h3>Featured Workspaces</h3></div><br /> 
<strong>In the future ...</strong><br /> 
<ul><li>You will see a list of Workspaces to join in<br /> 
</li><li>All of these Workspaces are open and public<br /> 
</li><li>If a Workspace is private then it won't be listed here. You need an invitation to sign in.<br /> 
</li></ul> 
</td></tr></table>
