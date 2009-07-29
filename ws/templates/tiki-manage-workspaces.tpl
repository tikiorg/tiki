<h1>{$title}</h1>

{tabset name="manageWS"}

 {if $editWS == "y"}
 
   {tab name="{tr}Name & Description{/tr}"}
	<div class="cbox" align="left">	
	  <form action="tiki-manage-workspaces.php" method="post">
	
		<div>
			<label for="wsNewName">Name: <br/></label>
				<input type="text" id="wsNewName" name="wsNewName" size="30" value="{$wsName}"/>
		</div>
		
		<div>
			<label for="wsNewDesc">Description: <br/></label>
				<textarea name="wsNewDesc" id="wsNewDesc" cols="30" rows="10" >{$wsDesc}</textarea>
		</div>
      </div>
      <input type="hidden" id="editedWS" name="editedWS" value={$wsId} />
      <input type="submit" value="Save" class="button" align="middle">
      </form>
   {/tab}
   {tab name="{tr}Groups{/tr}"}
   {/tab}
   {tab name="{tr}Objects{/tr}"}
	<table border='0' cellpadding='0' cellspacing='0' class='wikiplugin-split normal'>
	<tr>
		<td valign="top" width="60%" >
			
		</td>
		<td valign="top" width="40%" >
			<h2>Add Object in '{$wsName}'</h2>
			<br>
			<div class="cbox" align="left">
				<form action="tiki-manage-workspaces.php" method="post">
				<div>
					<label for="objectName">Name: <br/></label>
					<input type="text" id="objectName" name="objectName" size="30" />
				</div>
				<div>
					<label for="selectType">Type: </br> </label>
					<select name="selectType" id="selectType">
						<option value="wiki page">Wiki Page</option>
						<option value="forum">Forum</option>
						<option value="blog">Blog</option>
						<option value="fgal">File Gallery</option>
						<option value="gallery">Image Gallery</option>
						<option value="tracker">Tracker</option>
						<option value="faq">Faq</option>
						<option value="quiz">Quiz</option>
						<option value="article">article</option>
						<option value="calendar">Calendar</option>
						<option value="sheet">Sheet</option>
					</select>
				</div>
				<div>
					<label for="objectDesc">Description: <br/></label>
					<textarea name="objectDesc" id="objectDesc" cols="30" rows="5" ></textarea>
				</div>
			</div>
			<input type="submit" value="Create Object" class="button">
			<input type="hidden" id="addObjectinWS" name="addObjectinWS" value={$wsId} />
			</form>
		</td> 
	</tr>
	</table>
   {/tab}
   
 {else}
 
   {tab name="{tr}List WorkSpaces{/tr}"}
	<div class="cbox">
	   <table class = admin>
		<tr>
			<th>Name</th>
			<th>Description</th>
			<th>Path</th>
		</tr> 
		{foreach from=$listWS item=data}
			<tr>
				<td><a href = {$data.href}>{$data.name}</a></td>
				<td>{$data.description}</td>
				<td>{$data.categpath}</td>
			</tr>
		{/foreach}
	   </table>
	</div>
	{if not empty($prev_page)}
		<a class="button" href = {$prev_page}>Back</a>
	{/if}
	{if not empty($next_page)}
		<a class="button" href = {$next_page}>Next</a>
	{/if}
   {/tab}
   
   {tab name="{tr}Add Workspace{/tr}"}
      <div class="cbox" align="left">	
	  <form action="tiki-manage-workspaces.php" method="post">
	
		<div>
			<label for="wsName">Name: <br/></label>
				<input type="text" id="wsName" name="wsName" size="20" />
		</div>
		
		<div>
			<label for="wsDesc">Description: <br/></label>
				<textarea name="wsDesc" id="wsDesc" cols="30" rows="10"></textarea>
		</div>
		
		<div>
			<label for="parentWS">Select a Parent WS (optional): </label>
			<select name="parentWS" id="parentWS">
				<option value=0> </option>
				{foreach from=$listParentWS.data item=ws}
					<option value={$ws.categId}>{$ws.categpath}</option>
				{/foreach}
			</select>
		</div>
	
		<div>
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
				
		</div>
		
		<div>
			<label for="adminPerms">Select an admin permission for this group (optional): </label>
			<select name="adminPerms" id="adminPerms">
				<option value="tiki_p_ws_view"> </option>
				{foreach from=$listPerms item=perm}
					<option value={$perm.permName}>{$perm.permName} - {$perm.permDesc}</option>
				{/foreach}
			</select>
		</div>
      </div>	
      <input type="submit" value="Save" class="button" align="middle">
      </form>
   {/tab}
  
 {/if}  
{/tabset}