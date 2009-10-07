{* $Id$ *}

{title help="Permission"}{tr}Assign permissions to {/tr}{tr}{$objectType|escape}{/tr}: {$objectName|escape}{/title}

<div class="navbar">
{if !empty($referer)}{button href="$referer" _text="{tr}Back{/tr}"}{/if}
{button href="tiki-list_object_permissions.php" _text="{tr}Object Permissions List{/tr}"}
</div>

{tabset name='tabs_objectpermissions'}

	{tab name='{tr}Assign Permissions{/tr}'}

		{if $prefs.feature_tabs neq 'y'}
	<h2>{tr}Edit Permissions{/tr}</h2>
		{/if}
	<form method="post" action="{$smarty.server.PHP_SELF}?{query}">
		{if empty($filegals_manager)}
			{if $page_perms_flag}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{tr}These permissions override any global permissions or category permissions affecting this object.{/tr}<br />
					{if $tiki_p_admin eq 'y'}{tr}To edit global permissions{/tr} {self_link objectType='global' permType=$permType}{tr}click here{/tr}{/self_link}.{/if}
				{/remarksbox}
			{elseif $categ_perms_flag}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{tr}No permissions yet applied to this object but category permissions affect this object.{/tr}<br />
					{if $tiki_p_admin eq 'y'}{tr}To edit category permissions{/tr} {self_link _script='tiki-admin_categories.php'}{tr}click here{/tr}{/self_link}.{/if}
				{/remarksbox}
			{elseif $objectType eq 'global'}
				{remarksbox type="note" title="{tr}Note{/tr}"}
					{tr}Currently editing Global permissions.{/tr}
				{/remarksbox}
			{elseif $permissions_displayed eq 'parent'}
				{remarksbox type="note" title="{tr}Note{/tr}"}
					{tr}No permissions yet applied to this object. Global permissions apply.{/tr}<br />
					{if $tiki_p_admin eq 'y'}{tr}To edit global permissions{/tr} {self_link objectType='global' permType=$permType}{tr}click here{/tr}{/self_link}.{/if}
					<br /><br />
					{tr}Currently inherited permissions displayed.{/tr}
				{/remarksbox}
			{/if}
		{/if}
	
	<hr />
		<h2>{if $objectType eq 'global'}{tr}Assign global permissions{/tr}{else}{tr}Assign permissions to this object{/tr}{/if} {icon _id="img/loading-light.gif" id="perms_busy" style="vertical-align:top; display:none;"}</h2>

		<input type="hidden" name="referer" value="{$referer|escape}" />
		<input type="hidden" name="objectName" value="{$objectName|escape}" />
		<input type="hidden" name="objectType" value="{$objectType|escape}" />
		<input type="hidden" name="objectId" value="{$objectId|escape}" />
		<input type="hidden" name="permType" value="{$permType|escape}" />
		
		<label for="show_disabled_features">{tr}Show permissions for disabled features{/tr}</label>
		<input type="checkbox" name="show_disabled_features" id="show_disabled_features" {if isset($show_disabled_features) and $show_disabled_features}checked="checked"{/if} onchange="this.form.submit();" />

		<div class="input_submit_container" style="text-align: center">
			<input type="submit" name="assign" value="{tr}Assign{/tr}" />
		</div>
		
		<h3>{tr}Permissions{/tr}</h3>

		{if $objectType eq 'category'}
			<p>
				<input type="checkbox" id="propagate" name="propagate_category" value="1"/>
				<label for="propagate">{tr}Copy permissions to child categories{/tr}</label>
			</p>
		{/if}
		
		{if isset($groupsHidden)}
			{remarksbox type="tip" title="{tr}Note{/tr}"}
				{tr}Some of your groups have been automatically hidden. Click 'Show/hide columns' to select the group columns to display{/tr}
			{/remarksbox}
		{/if}

		{popup_link block="column_switches_div" class="button"}{tr}Show/hide Group List{/tr}{/popup_link}
		
		<div id="column_switches_div" style="display: none">
			<h3>{tr}Show/hide columns{/tr}</h3>
			<ul id="column_switches" class="column_switcher"><li></li></ul>
		</div>
		{treetable _data=$perms _checkbox=$permGroups _checkboxTitles=$groupNames _checkboxColumnIndex=$permGroupCols _valueColumnIndex="permName" _columns='"label"="Permission"' _sortColumn='type' _openall='y'}

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
	<form name="allperms" method="post" action="{$smarty.server.PHP_SELF}?{query}">
		<input type="hidden" name="quick_perms" value="true"/>

		{tab name='{tr}Quick Permissions{/tr}'}

			{if $prefs.feature_tabs neq 'y'}
		<h2>{tr}Quick Permissions{/tr}</h2>
			{/if}

			{if empty($filegals_manager)}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}These permissions override any global permissions or category permissions affecting this object.{/tr}<br />
					{if $tiki_p_admin eq 'y'}{tr}To edit global permissions <a class="rbox-link" href="tiki-objectpermissions.php?objectType=global">click here</a>.{/tr}{/if}
				{/remarksbox}
			{/if}

		<h2>{tr}Assign Quick-Permissions to this object{/tr}</h2>


		<table width="100%">
			<tr class="{cycle advance=true}">
				<th>Groups</th>
			{foreach item=permgroup from=$quickperms}
				<th>{$permgroup.name}</th>
			{/foreach}
				<th class="tips" title="{tr}A couple of userdefined permissions are currently assigned (See tab Assign Permissions){/tr}">Advanced</th>
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
