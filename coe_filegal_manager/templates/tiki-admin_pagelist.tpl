{title}{tr}Admin page lists{/tr}{/title}

{if $lists}
<div class='tabcontent'>
	<h2><a href='tiki-admin_pagelist.php'>{tr}Current lists{/tr}</a></h2> 
	<table class='normal item-list'>
		<tbody>
			<tr>
				<td class='heading'>{tr}Name{/tr}</td>
				<td class='heading'>{tr}Title{/tr}</td>
				<td class='heading'>{tr}Description{/tr}</td>
				<td class='heading'>{tr}Action{/tr}</td>
			</tr>
			{foreach from=$lists item=list_info}
			<tr>
				<td>
					<a href='tiki-admin_pagelist.php?edit=list&amp;action=edit&amp;name={$list_info.name|escape:'url'}' title='{tr}Edit this list{/tr}'>{$list_info.name|escape}</a>
				</td>
				<td>{$list_info.title|escape}</td>
				<td>{$list_info.description|escape}</td>
				<td>
					<a href='tiki-admin_pagelist.php?edit=list&amp;action=edit&amp;name={$list_info.name|escape:'url'}' title='{tr}Edit this list{/tr}'>{tr}edit{/tr}</a>
					/
					<a href='tiki-admin_pagelist.php?edit=list&amp;action=remove&amp;name={$list_info.name|escape:'url'}' title='{tr}Remove this list{/tr}'>{tr}remove{/tr}</a>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>
<br class='clear'/>
{/if}
<div class='cbox'>
<div class='cbox-title'>{if $edit_type}<a href='tiki-admin_pagelist.php?edit=list&amp;action=edit&amp;name={$list.name|escape}'>{tr}Edit list{/tr}</a>{else}{tr}Add list{/tr}{/if}</div>
	<div class='cbox-data'>
		<form action='tiki-admin_pagelist.php' method='post'>
			<input type='hidden' name='edit' value='list' />
			<input type='hidden' name='action' value='add' />
			{if $edit_type}<input type='hidden' name='old_list_name' value='{$list.name|escape}'/>{/if} 
		<table>
			<tbody>
				<tr>
					<td class='form'>{tr}Name:{/tr}</td>
					<td class='form'><input type="text" maxlength="255" size="40" name="new_list_name" value="{$list.name|escape}"/></td>
				</tr>
				<tr>
					<td class='form'>{tr}Title:{/tr}</td>
					<td class='form'><input type="text" maxlength="255" size="40" name="new_list_title" value="{$list.title|escape}"/></td>
				</tr>
				<tr>
					<td class='form'>{tr}Description:{/tr}</td>
					<td class='form'><textarea name='new_list_description' cols='40' rows='2' />{$list.description|escape}</textarea></td>
				</tr>
				{if !$edit_type and $prefs.feature_categories eq 'y'}
				<tr>
					<td class='form'>{tr}Seed with pages from a category:{/tr}</td>
					<td class='form'>
						<div class='checkbox-scroll'>
						{foreach from=$categories item=cat}
							<span><input type='checkbox' name='new_list_cats[]' value='{$cat.categId|escape}' />{$cat.name}</span>
							<br />
						{/foreach}
						</div>
					</td>
				</tr>
				{/if}
				<tr>
					<td></td>
					<td class='form'><input type='submit' name='submit' value='{if $edit_type}{tr}Update{/tr}{else}{tr}Add{/tr}{/if}' /></td>
				</tr>
			</tbody>
		</table>
		</form>
	</div>
</div>
{if list_items && is_array($list_items)}
<div class='cbox'>
	<div class='cbox-title'><a href='tiki-admin_pagelist.php?edit=list&amp;action=edit&amp;name={$list.name|escape}'>{tr}Add page{/tr}</a></div> 
	<div class='cbox-data'>
	<form action='tiki-admin_pagelist.php?edit=list&amp;action=page&amp;name={$list.name|escape}&amp;offset={$pagination_params.offset|escape:'url'}&amp;limit={$pagination_params.limit|escape:'url'}&amp;order={$pagination_params.order|escape:'url'}' method='post'>
	<table class='normal item-list'>
		<tbody>
			<tr>
				<td class='form'>{tr}Page name:{/tr}</td>
				<td class='form'><input type='text' name='new_item_page' value='' maxlength='255' size='40' /></td>
			</tr>
			<tr>
				<td class='form'>{tr}Page priority:{/tr}</td>
				<td class='form'><input type='text' name='new_item_priority' value='' maxlength='10' size='10' /></td>
			</tr>
			<tr>
				<td class='form'>{tr}Page score:{/tr}</td>
				<td class='form'><input type='text' name='new_item_score' value='' maxlength='10' size='10' /></td>
			</tr>
			<tr>
				<td></td>
				<td class='form'><input type='submit' name='add_page' value='{tr}Add{/tr}' /></td>
			</tr>
		</tbody>
	</form>
	</table>
	</div>
</div>
<div class='cbox'>
	<div class='cbox-title'><a href='tiki-admin_pagelist.php?edit=list&amp;action=edit&amp;name={$list.name|escape}'>{tr}Edit {$list.name|escape}{/tr}</a></div> 
	<div class='cbox-data'>
	<table class='normal item-list'>
	<form action='tiki-admin_pagelist.php?edit=list&amp;action=update&amp;name={$list.name|escape}&amp;offset={$pagination_params.offset|escape:'url'}&amp;limit={$pagination_params.limit|escape:'url'}&amp;order={$pagination_params.order|escape:'url'}' method='post'>
		<tbody>
			<tr>
				<td class='heading'>
					<a href='tiki-admin_pagelist.php?edit=list&amp;action=edit&amp;name={$list.name|escape}&amp;offset={$pagination_params.offset|escape:'url'}&amp;limit={$pagination_params.limit|escape:'url'}&amp;order={if $pagination_params.order == 'page_name_asc'}page_name_desc{else}page_name_asc{/if}'>
						{tr}Page{/tr}
					</a>
				</td>
				<td class='heading'>
					<a href='tiki-admin_pagelist.php?edit=list&amp;action=edit&amp;name={$list.name|escape}&amp;offset={$pagination_params.offset|escape:'url'}&amp;limit={$pagination_params.limit|escape:'url'}&amp;order={if $pagination_params.order == 'priority_asc'}priority_desc{else}priority_asc{/if}'>
						{tr}Priority{/tr}
					</a>
				</td>
				<td class='heading'><a href='tiki-admin_pagelist.php?edit=list&amp;action=edit&amp;name={$list.name|escape}&amp;offset={$pagination_params.offset|escape:'url'}&amp;limit={$pagination_params.limit|escape:'url'}&amp;order={if $pagination_params.order == 'score_asc'}score_desc{else}score_asc{/if}'>
					{tr}Score{/tr}
					</a>
				</td>
				<td class='heading'>{tr}Remove{/tr}</td>
			</tr>
			<tr>
			</tr>
			{foreach from=$list_items item=item name=pages}
			<tr>
				<td>
					<a href='/kb/{$item.page_name|escape}'>{$item.page_name|escape}</a>
					<input type='hidden' name='pages[{$smarty.foreach.pages.index}][]' value='{$item.page_name|escape}' />
				</td>
				<td>
					<input type='text' maxlength='5' size='8' name='pages[{$smarty.foreach.pages.index}][]' value='{$item.priority|escape}' />
				</td>
				<td>
					<input type='text' maxlength='5' size='8' name='pages[{$smarty.foreach.pages.index}][]' value='{$item.score|escape}' />
				</td>
				<td>
					<input type='checkbox' name='pages[{$smarty.foreach.pages.index}][]' />
				</td>	
			</tr>
			{/foreach}
			<tr>
				<td colspan='4' align='center'>
					<br class='clear'/>
					<input type='submit' name='update_pages' value='{tr}Update{/tr}' />
				</td>
			</tr>
		</tbody>
	</table>
	{if $cant_pages > 1}
	<br />
	<div class="mini">
		{if $prev_offset >= 0}
			[<a class="prevnext" href="tiki-admin_pagelist.php?edit=list&amp;action=edit&amp;name={$list.name|escape}&amp;offset={$prev_offset|escape:'url'}&amp;limit={$pagination_params.limit|escape:'url'}&amp;order={$pagination_params.order|escape:'url'}">{tr}Prev{/tr}</a>]&nbsp;
		{/if}
		{tr}Page{/tr}: {$actual_page}/{$cant_pages}
		{if $next_offset >= 0}
			&nbsp;[<a class="prevnext" href="tiki-admin_pagelist.php?edit=list&amp;action=edit&amp;name={$list.name|escape}&amp;offset={$next_offset|escape:'url'}&amp;limit={$pagination_params.limit|escape:'url'}&amp;order={$pagination_params.order|escape:'url'}">{tr}Next{/tr}</a>]
		{/if}
	</div>
	{/if}
	</div>
	</form>
</div>
{/if}		
