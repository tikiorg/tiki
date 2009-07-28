{tabset name="manageWS"}

   {tab name="{tr}List WorkSpaces{/tr}"}
	<table class = admin>
		<tr>
			<th>Name</th>
			<th>Description</th>
			<th>Path</th>
		</tr> 
		{foreach from=$listWS.data item=data}
			<tr>
				<td><a href = {$data.href}>{$data.name}</a></td>
				<td>{$data.description}</td>
				<td>{$data.categpath}</td>
			</tr>
		{/foreach}
	</table>
	{if not empty($prev_page)}
		<a class="button" href = {$prev_page}>Back</a>
	{/if}
	{if not empty($next_page)}
		<a class="button" href = {$next_page}>Next</a>
	{/if}
   {/tab}
   
   {tab name="{tr}Add Workspace{/tr}"}
      <div class="cbox" align="left">	
	  <form action="tiki-manage-workspaces.php" method="get" enctype="text/plain">
	
		<p>
			<label for="wsName">Name: </label>
				<input type="text" id="wsName" name="wsName" size="20" />
		</p>
		
		<p>
			<label for="wsDesc">Description: </label>
				<textarea name="wsDesc" id="wsDesc" cols="30" rows="10"></textarea>
		</p>
		
		<p>
			<label for="parentWS">Select a Parent WS (optional): </label>
			<select name="parentWS" id="parentWS">
				<option value=0> </option>
				{foreach from=$listParentWS.data item=ws}
					<option value={$ws.categId}>{$ws.categpath}</option>
				{/foreach}
			</select>
		</p>
	
		<p>
			Choose a group option: <br />
			<input type="radio" name="groupSelect" id="new" value="new" />
				<label for="new"> Create a new group:
				</label> 
					<input type="text" id="newGroup" name="newGroup" size="20" />
				<label for="groupDescrition"> Description: 
				</label> 
					<textarea name="groupDesc" id="groupDesc" cols="30" rows="1"></textarea>
				<br />
			</br>
			<input type="radio" name="groupSelect" id="old" value="old" checked="checked"/>
				<label for="old"> Select an old group: 
				</label>
					<select name="oldGroup" id="oldGroup">
						{foreach from=$listGroups.data item=group}
							<option value={$group.groupName}>{$group.groupName}</option>
						{/foreach}
					</select>
				
		</p>
		
		<p>
			<label for="adminPerms">Select an admin permission for this group (optional): </label>
			<select name="adminPerms" id="adminPerms">
				<option value="tiki_p_ws_view"> </option>
				{foreach from=$listPerms item=perm}
					<option value={$perm.permName}>{$perm.permName} - {$perm.permDesc}</option>
				{/foreach}
			</select>
		</p>
      </div>	
      <input type="submit" value="Save" class="button" align="middle">
      </form>
   {/tab}
   
{/tabset}