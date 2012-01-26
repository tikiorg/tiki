{title}{tr}Organizer{/tr}{/title}

<div class="categbar">
	{button href="tiki-browse_categories.php?parentId=$parentId" _text="{tr}Browse Category{/tr}" _title="{tr}Browse the category system{/tr}"}
	{if $tiki_p_admin_categories eq 'y'}
		{button href="tiki-admin_categories.php?parentId=$parentId" _text="{tr}Admin Category{/tr}" _title="{tr}Admin the Category System{/tr}"}
	{/if}
</div>

<div class="category-browser">
	{$tree}
</div>
<div class="object-list">
	{if $objects and $objects.data}
		<ol>
			{foreach from=$objects.data item=object}
				<li{permission type=$object.type object=$object.itemId name="modify_object_categories"} class="available"{/permission}>
					<input type="checkbox" name="object[]" value="{$object.type|escape}:{$object.itemId|escape}"/>
					<a href="{$object.href|escape}">{$object.name|escape}</a>
				</li>
			{/foreach}
		</ol>
		<p>
			<a class="select-all" href="#selectall">{tr}Select all{/tr}</a>
			<a class="unselect-all" href="#unselectall">{tr}Unselect all{/tr}</a>
		</p>
	{/if}
</div>

{remarksbox title="{tr}Move objects between categories{/tr}"}
<ol>
	<li>{tr}Click on the category name you want to list. The list of objects in that category will become visible.{/tr}</li>
	<li>{tr}Select the objects you want to affect. Controls will appear in the category browser.{/tr}</li>
	<li>{tr}Use the plus and minus signs to add or remove the categories on selected objects.{/tr}</li>
</ol>
{/remarksbox}
{jq}
function perform_selection_action(action, row) {
	var objects = [], url = $(row).find('a').attr('href');

	$('.object-list :checked').each(function () {
		objects.push($(this).val());
	});

	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data: {
			action: action,
			objects: objects
		},
		success: function (data) {
			$('.object-count', row).text(data.count);
		}
	});
}

$('.categ-add')
	.click(function () {
		perform_selection_action('add', $(this).closest('li')[0]);
	})
	.addClass('ui-icon')
	.addClass('ui-icon-circle-plus');

$('.categ-remove')
	.click(function () {
		perform_selection_action('remove', $(this).closest('li')[0]);
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
	$('.object-list :unchecked').attr('checked', true).change();
	return false;
});
$('.unselect-all').click(function () {
	$('.object-list :checked').attr('checked', false).change();
	return false;
});
{/jq}
