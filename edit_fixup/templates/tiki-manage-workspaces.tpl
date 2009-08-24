{* $Id: $ *}

{title help="Workspaces+Management" admpage="login"}{tr}Workspaces Management{/tr}{/title}

{if $feedback}
    {remarksbox type="$type" title="{tr}Note{/tr}"}
	{tr}{$feedback}{/tr}
    {/remarksbox}
{/if}

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
				<textarea name="wsNewDesc" id="wsNewDesc" cols="40" rows="10" >{$wsDesc}</textarea>
		</div>
      </div>
      <input type="hidden" id="editedWS" name="editedWS" value={$wsId} />
      <input type="submit" value="Save" class="button" align="middle">
      </form>
   {/tab}
   
   {tab name="{tr}Groups{/tr}"}
   <table class='wikiplugin-split normal'>
	<tr>
		<td valign="top" width="60%" >
			<h2>Groups in '{$wsName}'</h2>
			<div class="cbox">
			<table class="admin">
				<tr>
   					<th>Group Name</th>
   					<th>Description</th>
				</tr> 
				{foreach from=$groups item=data}
					<tr>
						<td><a>{$data.groupName}</a></td>
						<td>{$data.groupDesc}</td>
					</tr>
				{/foreach}
			</table>
			</div>
		{if not empty($prev_pageGroup)}
			<a class="button" href = {$prev_pageGroup}>Back</a>
		{/if}
		{if not empty($next_pageGroup)}
			<a class="button" href = {$next_pageGroup}>Next</a>
		{/if}
		</td>		
		<td valign="top" width="40%" >
			<h2>Add Group in '{$wsName}'</h2>
			<form action="tiki-manage-workspaces.php" method="post">
			<div class="cbox" align="left">
				Choose a group option: <br />
				<input type="radio" name="addGroupSelect" id="addNew" value="addNew" />
				<label for="new"> Create a new group:</label> 
					<input type="text" id="addNewGroup" name="addNewGroup" size="20" />
				<label for="groupDescrition"><br />Description:</label> 
					<textarea name="addGroupDesc" id="addGroupDesc" cols="30" rows="1"></textarea>
				<br />
				<input type="radio" name="addGroupSelect" id="addOld" value="addOld" checked="checked"/>
				<label for="addOld"> Select an old group: 
				</label>
					<select name="addOldGroup" id="addOldGroup">
						{foreach from=$listGroupsforAdd.data item=group}
							<option value="{$group.groupName}">{$group.groupName}</option>
						{/foreach}
					</select>
			</div>
			<div>
			<label for="addAdminPerms">Select an admin permission for this group (optional): <br /></label>
			<select name="addAdminPerms" id="addAdminPerms">
				<option> </option>
				{foreach from=$listPerms item=perm}
					<option value={$perm.permName}>{$perm.permName} - {$perm.permDesc}</option>
				{/foreach}
			</select>
			</div>
			<input type="submit" value="Add Group" class="button">
			<input type="hidden" id="addGroupinWS" name="addGroupinWS" value={$wsId} />
			</form>
		</td> 
	</tr>
   </table>
   {/tab}
   
   {tab name="{tr}Objects{/tr}"}
	<table border='0' cellpadding='0' cellspacing='0' class='wikiplugin-split normal'>
	<tr>
		<td valign="top" width="60%" >
			<h2>Objects in '{$wsName}'</h2>
			<div class="cbox">
			<table class="admin">
				<tr>
   					<th>Object Name</th>
   					<th>Type</th>
   					<th>Description</th>
				</tr> 
				{foreach from=$resources item=data}
					<tr>
						<td><a href = {$data.href}>{$data.name}</a></td>
						<td>{$data.type}</td>
						<td>{$data.description}</td>
					</tr>
				{/foreach}
			</table>
			</div>
		{if not empty($prev_pageObj)}
			<a class="button" href = {$prev_pageObj}>Back</a>
		{/if}
		{if not empty($next_pageObj)}
			<a class="button" href = {$next_pageObj}>Next</a>
		{/if}
		</td>		
		<td valign="top" width="40%" >
			<h2>Add Object in '{$wsName}'</h2>
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
						<option value="article">Article</option>
						<option value="calendar">Calendar</option>
						<option value="sheet">Sheet</option>
						<option value="survey">Survey</option>
						<option value="category">Category</option>
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

	<h2>{tr}List of Workspaces{/tr}</h2>

	{include file='find.tpl' find_show_num_rows='y'}

	{* if $cant_pages > 1 or !empty($initial) or !empty($find) *}
		{initials_filter_links}
	{* /if *}

	{if not empty($prev_pageWS)}
	    <a class="button" href="{$prev_pageWS}">Back</a>
	{/if}
	{if not empty($next_pageWS)}
	    <a class="button" href="{$next_pageWS}">Next</a>
       {/if}

	   <table class ="normal">
		<tr>
			<th class="auto">{select_all checkbox_names='checked[]'}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
			<th>{tr}Description{/tr}</th>
			<th>{tr}Path{/tr}</th>
			<th>{tr}Actions{/tr}</th>
		</tr> 
		{cycle print=false values="even,odd"}
		{foreach from=$listWS item=data}
			<tr class="{cycle}">
				<td><input type="checkbox" name="checked[]" value="{$data.name}" /></td>
				<td><a href ="{$data.href_edit}">{$data.name}</a></td>
				<td>{$data.description}</td>
				<td>{$data.categpath}</td>
				<td>Something</td>
			</tr>
		{/foreach}
		<tr>
			<td class="form" colspan="18">
				<form name="mult_edit" method="post" action="{$smarty.server.PHP_SELF}">
				<p align="left">
				{tr}Perform action with checked:{/tr}
				<select name="submit_mult">
				    <option value="" selected="selected">-</option>
				    <option value="remove_workspaces" >{tr}Remove{/tr}</option>
				</select>
				<input type="submit" value="{tr}OK{/tr}" />
				</p>
				</form>
			</td>
		</tr>
	   </table>
	</div>
   {/tab}
   
   {tab name="{tr}Add Workspace{/tr}"}

   <div class="cbox">	
   <h2>{tr}Add a new Workspace{/tr}</h2>
 
	  <form action="tiki-manage-workspaces.php" method="post">
		<table class="normal">
			<tr class="formcolor">
				<td><label for="wsName">Name:</label></td>
				<td><input type="text" id="wsName" name="wsName" size="20" /></td>
			</tr>
			<tr class="formcolor">
				<td><label for="wsDesc">Description:</label></td>
				<td><textarea name="wsDesc" id="wsDesc" style="width:95%"></textarea></td> 
			</tr>
			<tr class="formcolor">
				<td><label for="parentWS">Select a Parent WS (optional):</label></td>
				<td>
				    <select name="parentWS" id="parentWS">
					<option value=0> </option>
					{foreach from=$listParentWS.data item=ws}
					    <option value={$ws.categId}>{$ws.categpath}</option>
					{/foreach}
				    </select>
				</td>
			</tr>
			<tr class="formcolor">
				<td><input type="radio" name="groupSelect" id="new" value="new" /><label for="new">Create a new group:</label><br /><br /><label for="groupDescrition">&nbsp;&nbsp;&nbsp;&nbsp;Description:</label></td>
				<td><input type="text" id="newGroup" name="newGroup" size="20" /><br /><br /><textarea name="groupDesc" id="groupDesc" cols="30" rows="1"></textarea>
</td>
			</tr>
			<tr class="formcolor">
				<td><input type="radio" name="groupSelect" id="old" value="old" checked="checked"/><label for="old"> Select an old group:</label></td>
				<td><select name="oldGroup" id="oldGroup">
						{foreach from=$listGroups.data item=group}
							<option value="{$group.groupName}">{$group.groupName}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr class="formcolor">
			    <td><label for="adminPerms">Select an admin permission for this group (optional): </label></td>
			    <td>
				<select name="adminPerms" id="adminPerms">
				    <option value="tiki_p_ws_view"> </option>
				    {foreach from=$listPerms item=perm}
					<option value={$perm.permName}>{$perm.permName} - {$perm.permDesc}</option>
				    {/foreach}
				</select>
			    </td>
			</tr>
		</table>
      </div>	
      <input type="hidden" value="create" name="create" />
      <input type="submit" value="Create" class="button" align="middle" />
      </form>
   {/tab}
  
 {/if}  
{/tabset}
