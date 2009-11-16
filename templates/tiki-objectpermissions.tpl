{* $Id$ *}

{title help="Permission"}{tr}Assign permissions to {/tr}{tr}{$objectType|escape}{/tr}: {$objectName|escape}{/title}

<div class="navbar">
{if !empty($referer)}{button href="$referer" _text="{tr}Back{/tr}"}{/if}
{button href="tiki-list_object_permissions.php" _text="{tr}Object Permissions List{/tr}"}
</div>

{tabset name='tabs_objectpermissions'}

	{tab name="{tr}Assign Permissions{/tr}"}

		{if $prefs.feature_tabs neq 'y'}
	<h2>{tr}Edit Permissions{/tr}</h2>
		{/if}
	<form method="post" action="{$smarty.server.PHP_SELF}?{query}">
		{if empty($filegals_manager)}
			{if $objectType eq 'global'}
				{remarksbox type="note" title="{tr}Note{/tr}"}
					{tr}Currently editing Global permissions.{/tr}
				{/remarksbox}
			{elseif $permissions_displayed eq 'direct'}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{if $objectType neq 'category'}{tr}This object's direct permissions override any global permissions or category permissions affecting this object.{/tr}{else}{tr}This category's direct permissions override any global permissions affecting objects in it.{/tr}{/if}<br />
					{if $tiki_p_admin eq 'y'}{tr}To edit global permissions{/tr} {self_link objectType='global' objectId='' objectName='' permType=$permType}{tr}click here{/tr}{/self_link}.{/if}
				{/remarksbox}
			{elseif  $permissions_displayed eq 'category'}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{tr}No permissions yet applied to this object but category permissions affect this object and are displayed below.{/tr}<br />
					{if $tiki_p_admin eq 'y'}{tr}To edit category permissions{/tr} {self_link _script='tiki-admin_categories.php'}{tr}click here{/tr}{/self_link}.{/if}
				{/remarksbox}
			{elseif $permissions_displayed eq 'parent'}
				{remarksbox type="note" title="{tr}Note{/tr}"}
					{tr}No direct permissions yet applied to this object. Global permissions apply.{/tr}<br />
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
		<input type="hidden" name="show_disabled_features" value="{$show_disabled_features}" />
		
		<div class="input_submit_container" style="text-align: center">
			<input type="submit" name="assign" value="{tr}Assign{/tr}" />
			{if $permissions_displayed eq 'direct' and $objectType neq 'global'}
				<input type="submit" name="remove" value="{tr}Reset to Global Perms{/tr}" class="tips" title="{tr}Reset Perms{/tr}|{if $objectType neq 'category'}{tr}This will remove all the settings here and permissions will be reset to inherit any category permissions that are set, or the global sitewide permissions.{/tr}{else}{tr}This will remove all the settings here and permissions will be reset to inherit the global sitewide permissions.{/tr}{/if}"/>
			{/if}
			<input type="submit" name="copy" value="{tr}Copy{/tr}" class="tips" title="{tr}Permissions Clipboard{/tr}|{tr}Copy the permissions set here{/tr}" />
			{if !empty($perms_clipboard_source)}<input type="submit" name="paste" value="{tr}Paste{/tr}" class="tips" title="{tr}Permissions Clipboard{/tr}|{tr}Paste copied permissions from {/tr}<em>{$perms_clipboard_source}</em>" />{/if}
		</div>
		
		{if $objectType eq 'category'}
			<p>
				<input type="checkbox" id="propagate_category" name="propagate_category" value="1"/>
				<label for="propagate_category">{tr}Assign or remove permissions on <em>all</em> child categories{/tr}</label>
			</p>
			{jq}$jq("input[name='assign'],input[name='remove']").click(function(){
if ($jq("#propagate_category").attr("checked")) {
	return confirm("{tr}Are you sure you want to effect all child categories?\nThere is no undo.{/tr}");
} }); {/jq}
		{/if}
		
		{if ($objectType eq 'wiki' or $objectType eq 'wiki page') and !empty($inStructure)}
			<input name="assignstructure" id="assignstructure" type="checkbox" />
			<label for="assignstructure">{tr}Assign or remove permissions on all pages of the sub-structure{/tr}</label>
			{jq}$jq("input[name='assign'],input[name='remove']").click(function(){
if ($jq("#assignstructure").attr("checked")) {
	return confirm("{tr}Are you sure you want to effect all pages in this sub-structure?\nThere is no undo.{/tr}");
} }); {/jq}
		{/if}
		
		<h3>{tr}Permissions{/tr}</h3>

		{treetable _data=$perms _checkbox=$permGroups _checkboxTitles=$groupNames _checkboxColumnIndex=$permGroupCols _valueColumnIndex="permName" _columns="\"label\"=\"{tr}Permission{/tr}\"" _sortColumn='type' _openall='y' _columnsContainHtml='y'}

		<div class="input_submit_container" style="text-align: center">
			<input type="submit" name="assign" value="{tr}Assign{/tr}" />
			{if $permissions_displayed eq 'direct' and $objectType neq 'global'}
				<input type="submit" name="remove" value="{tr}Reset to Global Perms{/tr}" class="tips" title="{tr}Reset Perms{/tr}|{tr}This will remove all the settings here and permissions will be reset to inherit the global sitewide permissions.{/tr}"/>
			{/if}
		</div>
	</form>
	{/tab}

	{tab name="{tr}Select groups{/tr}"}
		<form method="post" action="{$smarty.server.PHP_SELF}?{query}">
			{if isset($groupsFiltered)}
				{remarksbox type="warning" title="{tr}Note{/tr}"}
					{tr}Some of your groups have been automatically hidden.<br /> Select the groups below to assign permissions for.{/tr}
				{/remarksbox}
			{/if}

			<h2>{tr}Groups{/tr}</h2>
			
			{treetable _data=$groups _checkbox="group_filter" _checkboxTitles="{tr}Select all{/tr}" _checkboxColumnIndex="in_group_filter" _valueColumnIndex="id" _columns='"groupName"="{tr}Group name{/tr}","groupDesc"="{tr}Description{/tr}"' _sortColumn='parents' _collapseMaxSections=20 _sortColumnDelimiter=','}
			
			<div class="input_submit_container" style="text-align: center">
				<input type="submit" name="group_select" value="{tr}Select{/tr}" />
			</div>
		</form>
	{/tab}
	
	{tab name="{tr}Select features{/tr}"}
		<form method="post" action="{$smarty.server.PHP_SELF}?{query}">
			{if isset($featuresFiltered)}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{tr}Some of your features have been automatically hidden.<br /> Select the features below to assign permissions for.{/tr}
				{/remarksbox}
			{/if}

			<label for="show_disabled_features2">{tr}Show permissions for disabled features{/tr}</label>
			<input type="checkbox" name="show_disabled_features" id="show_disabled_features2" {if isset($show_disabled_features) and $show_disabled_features eq 'y'}checked="checked"{/if} onchange="this.form.submit();" />

			<h2>{tr}Features{/tr}</h2>
			
			{treetable _data=$features _checkbox="feature_filter" _checkboxTitles="{tr}Select all{/tr}" _checkboxColumnIndex="in_feature_filter" _valueColumnIndex="featureName" _columns='"featureName"="{tr}Feature name{/tr}"' _sortColumn="featureName" _sortColumnDelimiter='*' _collapseMaxSections=20 _listFilter='n'}
			
			<div class="input_submit_container" style="text-align: center">
				<input type="submit" name="feature_select" value="{tr}Select{/tr}" />
			</div>
		</form>
	{/tab}
	
	{* Quickperms *}

	{if $prefs.feature_quick_object_perms eq 'y'}
	<form name="allperms" method="post" action="{$smarty.server.PHP_SELF}?{query}">
		<input type="hidden" name="quick_perms" value="true"/>

		{tab name="{tr}Quick Permissions{/tr}"}

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
			<tr>
				<th>Groups</th>
			{foreach item=permgroup from=$quickperms}
				<th>{$permgroup.name}</th>
			{/foreach}
				<th class="tips" title="{tr}A couple of userdefined permissions are currently assigned (See tab Assign Permissions){/tr}">Advanced</th>
			</tr>
			{cycle print=false values="even,odd"}
			{section name=grp loop=$groups}
			<tr class="{cycle}">
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
		<input type="hidden" name="show_disabled_features" value="{$show_disabled_features}" />
		<div class="input_submit_container" style="text-align: center">
			<input type="submit" name="assign" value="{tr}Assign{/tr}" />
		</div>
		
	{/tab}
</form>
	{/if}

	{* Quickperms END *}

{/tabset}
