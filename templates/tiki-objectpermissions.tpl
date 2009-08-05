{* $Id$ *}

{title help="Permission"}{tr}Assign permissions to {/tr}{tr}{$objectType|escape}{/tr}: {$objectName|escape}{/title}

<div class="navbar">
{button href="$referer" _text="{tr}Back{/tr}"}
{button href="tiki-list_object_permissions.php" _text="{tr}Object Permissions List{/tr}"}
</div>

{tabset name='tabs_objectpermissions'}

	{tab name='{tr}View Permissions{/tr}'}

		{if $prefs.feature_tabs neq 'y'}
	<h2>{tr}View Permissions{/tr}</h2>
		{/if}
		{if empty($filegals_manager)}
			{remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}These permissions override any global permissions or category permissions affecting this object.{/tr}<br />
				{if $tiki_p_admin eq 'y'}{tr}To edit global permissions <a class="rbox-link" href="tiki-admingroups.php">click here</a>.{/tr}{/if}
			{/remarksbox}
		{/if}

	<h2>{tr}Current permissions for this object{/tr}</h2>
	
	<form method="post" action="tiki-objectpermissions.php">
		<input type="hidden" name="referer" value="{$referer|escape}" />
		<input type="hidden" name="objectName" value="{$objectName|escape}" />
		<input type="hidden" name="objectType" value="{$objectType|escape}" />
		<input type="hidden" name="objectId" value="{$objectId|escape}" />
		<input type="hidden" name="permType" value="{$permType|escape}" />
		<table class="normal">
			<tr>
				<th>
			{if $page_perms}
				{select_all checkbox_names='checked[]'}
			{/if}
				</th>
				<th>{tr}Permissions{/tr}</th>
				<th>{tr}Groups{/tr}</th>
				<th style="width: 20px">{tr}Action{/tr}</th>
			</tr>
	
			{cycle values="odd,even" print=false}
			{section  name=pg loop=$page_perms}
			<tr>
				<td class="{cycle advance=false}">
					<input type="checkbox" name="checked[]" value="{$page_perms[pg].permName|cat:' '|cat:$page_perms[pg].groupName|escape}" />
				</td>
				<td class="{cycle advance=false}">
				{$page_perms[pg].permName|escape}<br /><em>{tr}{$page_perms[pg].permDesc|escape}{/tr}</em>
				</td>
				<td class="{cycle advance=false}">
				{if $page_perms[pg].groupName eq $prefs.trackerCreatorGroupName}<i>{tr}Creator Group{/tr}</i>{else}{$page_perms[pg].groupName|escape}{/if}
				</td>
				<td class="{cycle advance=true}">
					<a class="link" href="tiki-objectpermissions.php?referer={$referer|escape:"url"}&amp;action=remove&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;perm={$page_perms[pg].permName}&amp;group={$page_perms[pg].groupName}{if !empty($filegals_manager)}&amp;filegals_manager={$filegals_manager|escape}{/if}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
				</td>
			</tr>
			{sectionelse}
			<tr>
				<td colspan="4" class="odd">
					{if !empty($categ_perms)}<strong>{tr}No individual permissions but category permissions apply{/tr}</strong>
					{else}{tr}There are no individual permissions and no category permissions applied{/tr}
					{/if}
				</td>
			</tr>
			{/section}
		</table>
	
		{if $page_perms}
			<div>
				{tr}Perform action with checked:{/tr} 
				<input type="image" name="delsel" src='pics/icons/cross.png' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' />
				{if isset($inStructure)}
					{tr}and also to all pages of the sub-structure:{/tr}
					<input name="removestructure" type="checkbox" />
				{/if}
			</div>
		{/if}
	</form>

	{if isset($commentCreatorGroup) && $commentCreatorGroup eq 'y'}
		{remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}Creator group perms apply only if no tiki_p_view_trackers{/tr}{/remarksbox}
	{/if}

	<hr />

	<h2>{tr}Current permissions for categories that this object belongs to{/tr}:</h2>
		{if !empty($page_perms) && !empty($categ_perms)}
			{remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}These permissions do not apply. Special permissions apply.{/tr}{/remarksbox}
		{/if}
	<table class="normal">
		<tr>
			<th>{tr}Permission{/tr}</th>
			<th>{tr}Group{/tr}</th>
			<th>{tr}Category{/tr}</th>
		</tr>
		{cycle print=false values="even,odd"}
		{section  name=x loop=$categ_perms}
			{section name=y loop=$categ_perms[x]}
		<tr class="{cycle advance=true}">
			<td class="{cycle advance=false}">{$categ_perms[x][y].permName|escape}<br />{if isset($categ_perms[x][y].permDesc)}<i>{$categ_perms[x][y].permDesc}</i>{/if}</td>
			<td class="{cycle advance=false}">{$categ_perms[x][y].groupName|escape}</td>
			<td class="{cycle advance=false}">{$categ_perms[x][0].catpath}</td>
		</tr>
			{/section}
		{sectionelse}
		<tr>
			<td colspan="3">{if empty($page_perms)}{tr}No category permissions; global permissions apply{/tr}{else}{tr}No category permissions; special permissions apply{/tr}{/if}</td>
		</tr>
		{/section}
	</table>
	{/tab}

	{tab name='{tr}Assign Permissions{/tr}'}

		{if $prefs.feature_tabs neq 'y'}
	<h2>{tr}Edit Permissions{/tr}</h2>
		{/if}
	<form method="post" action="tiki-objectpermissions.php{if !empty($filegals_manager)}?filegals_manager={$filegals_manager|escape}{/if}">
		{if empty($filegals_manager)}
			{if !empty($page_perms) or !empty($categ_perms)}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}These permissions override any global permissions or category permissions affecting this object.{/tr}<br />
					{if $tiki_p_admin eq 'y'}{tr}To edit global permissions <a class="rbox-link" href="tiki-admingroups.php">click here</a>.{/tr}{/if}
				{/remarksbox}
			{else}
				{remarksbox type="note" title="{tr}Note{/tr}"}
					{tr}No permissions yet applied to this object. Global permissions currently set below.{/tr}
				{/remarksbox}
			{/if}
		{/if}
	
	<hr />

		<h2>{tr}Assign permissions to this object{/tr}</h2>

		<input type="hidden" name="referer" value="{$referer|escape}" />
		<input type="hidden" name="objectName" value="{$objectName|escape}" />
		<input type="hidden" name="objectType" value="{$objectType|escape}" />
		<input type="hidden" name="objectId" value="{$objectId|escape}" />
		<input type="hidden" name="permType" value="{$permType|escape}" />

		<div class="input_submit_container" style="text-align: center">
			<input type="submit" name="assign" value="{tr}Assign{/tr}" />
		</div>
		
		<h3>{tr}Permissions{/tr}</h3>
		{treetable _data=$perms _checkbox=$permGroups _checkboxTitles=$groupNames _checkboxColumnIndex=$permGroupCols _columns='"permName"="Permission Name", "permDesc"="Description"' _sortColumn='type'}
		
		<div class="input_submit_container" style="text-align: center">
			<input type="submit" name="assign" value="{tr}Assign{/tr}" />
		</div>
		{if ($objectType eq 'wiki' or $objectType eq 'wiki page') and !empty($inStructure)}
			{tr}and also to all pages of the sub-structure:{/tr} <input name="assignstructure" type="checkbox" />
		{/if}
	</form>
	{/tab}

	{* Quickperms *}

	{if $prefs.feature_quick_object_perms eq 'y'}
	<form name="allperms" method="post" action="tiki-objectpermissions.php{if !empty($filegals_manager)}?filegals_manager={$filegals_manager|escape}{/if}">
		<input type="hidden" name="quick_perms" value="true"/>

		{tab name='{tr}Quick Permissions{/tr}'}

			{if $prefs.feature_tabs neq 'y'}
		<h2>{tr}Quick Permissions{/tr}</h2>
			{/if}

			{if empty($filegals_manager)}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}These permissions override any global permissions or category permissions affecting this object.{/tr}<br />
					{if $tiki_p_admin eq 'y'}{tr}To edit global permissions <a class="rbox-link" href="tiki-admingroups.php">click here</a>.{/tr}{/if}
				{/remarksbox}
			{/if}

		<h2>{tr}Assign Quick-Permissions to this object{/tr}</h2>


		<table width="100%">
			<tr class="{cycle advance=true}">
				<th>Groups</th>
			{foreach item=permgroup from=$quickperms}
				<th>{$permgroup.name}</th>
			{/foreach}
				<th onmouseover="return overlib('A couple of userdefined permissions are currently assigned (See tab Assign Permissions)');" onmouseout="nd();">Advanced</th>
			</tr>
			{cycle print=false values="even,odd"}
			{section name=grp loop=$groups}
			<tr>
				<td>
				{$groups[grp].groupName|escape}
				</td>
				{foreach item=permgroup from=$quickperms}
				<td>
					<input type="radio" name="perm_{$groups[grp].groupName}" value="{$permgroup.name}" {if $groups[grp].groupSumm eq $permgroup.name}checked{/if} />
				</td>
				{/foreach}
				<td>
					<input type="radio" name="perm_{$groups[grp].groupName}" value="userdefined" {if $groups[grp].groupSumm eq 'userdefined'}checked{/if} disabled />
				</td>
			</tr>
			{/section}
		</table>
	
		<input type="hidden" name="referer" value="{$referer|escape}" />
		<input type="hidden" name="objectName" value="{$objectName|escape}" />
		<input type="hidden" name="objectType" value="{$objectType|escape}" />
		<input type="hidden" name="objectId" value="{$objectId|escape}" />
		<input type="hidden" name="permType" value="{$permType|escape}" />
		<div class="input_submit_container" style="text-align: center">
			<input type="submit" name="assign" value="{tr}Assign{/tr}" />
		</div>
		{/tab}
</form>
	{/if}

	{* Quickperms END *}

{/tabset}
