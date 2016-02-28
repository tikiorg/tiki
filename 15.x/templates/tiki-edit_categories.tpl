{* $Id$ *}
{title}{tr}Organizer{/tr}{/title}

<div class="form-group">
	{button href="tiki-browse_categories.php?parentId=$parentId" _class="btn-link" _icon_name="view" _text="{tr}Browse Categories{/tr}" _title="{tr}Browse the category system{/tr}"}
	{if $tiki_p_admin_categories eq 'y'}
		{button href="tiki-admin_categories.php?parentId=$parentId" _class="btn-link" _icon_name="administer" _text="{tr}Admin Categories{/tr}" _title="{tr}Admin the Category System{/tr}"}
	{/if}
</div>

{remarksbox title="{tr}Move objects between categories{/tr}"}
	<ol>
		<li>{tr}Click on the category name to display the list of objects in that category.{/tr}</li>
		<li>{tr}Select the objects to affect. Controls will appear in the category browser.{/tr}</li>
		<li>{tr}Use the plus and minus signs to add or remove the categories on selected objects.{/tr}</li>
	</ol>
{/remarksbox}

<div class="category-browser">
	{$tree}
</div>
{filter action="tiki-edit_categories.php" filter=$filter}{/filter}
<div class="object-list">
	{if $result && count($result)}
		<ol>
			{foreach from=$result item=object}
				<li{permission type=$object.type object=$object.object_id name="modify_object_categories"} class="available"{/permission}>
					<input type="checkbox" name="object[]" value="{$object.object_type|escape}:{$object.object_id|escape}">
					{object_link type=$object.object_type id=$object.object_id}
				</li>
			{/foreach}
		</ol>
		{if $result->hasMore()}
			<p>{tr}More results are available. Please refine the search criteria.{/tr}</p>
		{/if}
		<p>
			<a class="select-all" href="#selectall">{tr}Select all{/tr}</a>
			<a class="unselect-all" href="#unselectall">{tr}Unselect all{/tr}</a>
		</p>
	{/if}
</div>

{jq}
function perform_selection_action(action, row) {
	var objects = [], categId = $(row).find('a').data('categ');

	$('.object-list :checked').each(function () {
		objects.push($(this).val());
	});

	$('.control', row).fadeTo(10, .20);

	$.ajax({
		type: 'POST',
		url: $.service('category', action),
		dataType: 'json',
		data: {
			categId: categId,
			objects: objects,
			confirm: 1
		},
		success: function (data) {
			$('.object-count', row).text(data.count);
		},
		complete: function () {
			$('.control', row).fadeTo(10, 1);
		}
	});
}

$('.categ-add')
	.click(function () {
		perform_selection_action('categorize', $(this).closest('li')[0]);
	})
	.addClass('ui-icon')
	.addClass('ui-icon-circle-plus');

$('.categ-remove')
	.click(function () {
		perform_selection_action('uncategorize', $(this).closest('li')[0]);
	})
	.addClass('ui-icon')
	.addClass('ui-icon-circle-minus');

$('.control')
	.css('float', 'right')
	.css('cursor', 'pointer')
	.hide();

$('.object-list :checkbox').change(function () {
	$('.control').toggle($('.object-list :checkbox:checked').length > 0);
});

$('.object-list li:not(.available) :checkbox').attr('disabled', true);

$('.select-all').click(function () {
	$('.object-list :unchecked').prop('checked', true).change();
	return false;
});
$('.unselect-all').click(function () {
	$('.object-list :checked').prop('checked', false).change();
	return false;
});
{/jq}
