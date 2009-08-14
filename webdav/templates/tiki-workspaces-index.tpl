{remarksbox type="info" title="{tr}Welcome{/tr}"}
{tr}You have succesfully enabled  Workspaces in TikiWiki. This feature is <em>under development</em>, so please don't expect nothing if not works properly. If you want to get more info go to the {/tr} <a class="rbox-link" href="http://dev.tikiwiki.org/workspace">{tr}Workspaces wiki page{/tr}</a>.
<hr />
{tr}Things you can currently do: <br />
<ul>
<li>If you want to test Workspaces quickly you can go to <a href="tiki-admin.php?page=workspaces" title="Workspaces config page">Workspaces config page</a> and in the <em>Workspaces Dev Tools Tab</em> choose the option you want to test.</li>
<li>If you want to manage Workspaces, you can go to <a href="tiki-manage-workspaces.php" title="Manage Workspaces">manage Workspaces page</a> (very experimental, buggy, and more).</li>
</ul>
<strong>All you can see here could change in the future!</strong>{/tr}
{/remarksbox}

<table border='0' cellpadding='0' cellspacing='0' class='wikiplugin-split normal'><tr><td valign="top" width="60%" > 
<br /> 
<div class='titlebar'><h3>My Workspaces</h3></div><br /> 
<!--<strong>Who Should Use This</strong><br /> 
<ul><li>You want to get started quickly<br /> 
</li><li>You don't feel like learning the Admin Panel right away<br /> 
</li><li>You want to quickly test out some of Tiki's Features<br /> 
</li>
</ul>
 <br /> --><table class = admin>
<tr>
   <th>Name</th>
   <th>Description</th>
   <th>Path</th>
</tr> 
{foreach from=$listWS item=data}
	<tr>
		<td><a href = {$data.href}>{$data.name}</a></td>
		<td>{$data.description}</td>
		<td>{$data.wspath}</td>
	</tr>
{/foreach}
</table>

</td><td valign="top" width="40%" > 
<br /> 
<div class='titlebar'><h3>Featured Workspaces</h3></div><br /> 
<strong>In the future ...</strong><br /> 
<ul><li>You will see a list of Workspaces to join in<br /> 
</li><li>All of these Workspaces are open and public<br /> 
</li><li>If a Workspace is private then it won't be listed here. You need an invitation to sign in.<br /> 
</li></ul> 
<br /> 
<br /> 
</td></tr></table>
