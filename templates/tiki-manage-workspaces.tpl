{* $Id: $ *}

{title help="Workspaces+Management" admpage="login"}{tr}Workspaces Management{/tr}{/title}

{if $feedback}
    {remarksbox type="$type" title="{tr}Note{/tr}"}
	{tr}{$feedback}{/tr}
    {/remarksbox}
{/if}

{tabset name="manageWS"}
 
 {tab name="{tr}List WorkSpaces{/tr}"}
	<div class="cbox">

	<h2>{tr}List of Workspaces{/tr}</h2>

	{include file='find.tpl' find_show_num_rows='y'}

	{initials_filter_links}

	<form name="mult_edit" method="post" action="{$smarty.server.PHP_SELF}">
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
				<td><a href ="tiki-manage-workspaces.php?editWS={$data.href_edit}" title="View {$data.name}">{$data.name}</a></td>
				<td>{$data.description}</td>
				<td>{$data.categpath}</td>
				<td>
					{self_link _class="link" editWS=$data.categId _icon="page_edit" _title="{tr}Edit Workspace Settings{/tr}: `$data.name`"}{/self_link}
					{self_link _class="link" deleteWS=$data.categId _icon="cross" _title="{tr}Remove this Workspace and its childrens (if apply){/tr}"}{/self_link}
				</td>
			</tr>
		{/foreach}
		<tr>
			<td class="form" colspan="18">
				<p align="left">
				{tr}Perform action with checked:{/tr}
				<select name="submit_mult">
				    <option value="" selected="selected">-</option>
				    <option value="remove_workspaces" >{tr}Remove{/tr}</option>
				</select>
				<input type="submit" value="{tr}OK{/tr}" />
				</p>
			</td>
		</tr>
	   </table>
	   </form>
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
  
{/tabset}
