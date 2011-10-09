{tabset}
	{tab name="{tr}Settings{/tr}"}
<form action="tiki-admin.php?page=areas" method="post">
				{preference name=feature_areas visible="always"}
		<div class="adminoptionboxchild" id="feature_areas_childcontainer">
				{preference name=areas_root visible="always"}
		</div>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="areas" value="{tr}Change preferences{/tr}" />
	</div>
</form>
	{/tab}
	{tab name="{tr}Areas Overview{/tr}"}	
	{if isset($error)}
	{remarksbox type="warning" title="{tr}Error{/tr}"}{$error} {tr}Nothing was updated.{/tr}{/remarksbox}
	{/if}
<form action="tiki-admin.php?page=areas" method="post">
		<table class="normal">
		<tr>
			<th colspan="2">{tr}Category{/tr}</th>
			<th>{tr}Perspectives{/tr}</th>
			<th>{tr}Description{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{if $no_area eq '0'}
		{foreach from=$areas item=area}
			<tr class="{cycle}">	
				<td>{$area.categId}</td>
				<td>{$area.categName}</td>
				<td>
				{foreach from=$area.perspectives item=persp}
					<a href="tiki-edit_perspective.php?action=edit&id={$persp.perspectiveId}" title="{tr}Edit perspective{/tr} {$persp.name}">{$persp.name}</a>,
				{/foreach}
				</td>
				<td>{$area.description}</td>
			</tr>
		{/foreach}
		{else}
		<th class="{cycle}" colspan="4">{tr}No category found in area{/tr}</th>
		{/if}
		</table>
		{remarksbox type="info" title="{tr}Hint{/tr}"}{tr}This tab shows you an overview of categories affected by the areas feature. The category with the smallest id should be the category set as areas root in the settings tab. If not so, update this overview with the button below.{/tr}{/remarksbox}
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="update_areas" value="{tr}Update areas{/tr}" />
	</div>
</form>
	{/tab}
{/tabset}
		{remarksbox type="info" title="{tr}How to setup{/tr}"}{tr}You have to enable the perspective and the categories feature. Then set a structure of categories with a common parent. Set that parent as areas root in the settings tab. Then go to {/tr}<a href="tiki-edit_perspective.php">{tr}perspectives{/tr}</a>.{tr}There you can create a set of perspectives to bind the children of areas root to. Change for every perspective you want the category jail to another child. It is designed that every child category is bind to one perspective only. If you do otherwise, the first found perspective is used.{/tr}{/remarksbox}
