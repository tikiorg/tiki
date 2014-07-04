{* $Id: itemlink.tpl 51847 2014-07-04 18:08:48Z lphuberdeau $ *}
<div class="item-link">
<input id="{$field.ins_id|escape}" type="hidden" name="{$field.ins_id|escape}" value="{$field.value|escape}">
{object_selector _id="`$field.ins_id`_selector" _value=$data.selector_value type="trackeritem" tracker_id=$field.options_map.trackerId tracker_status=$data.status}
{if $field.options_map.addItems}
	<a class="btn btn-default insert-tracker-item" href="{service controller=tracker action=insert_item trackerId=$field.options_map.trackerId}">{$field.options_map.addItems|escape}</a>
	{jq}
	$('#{{$field.ins_id|escape}}_selector')
		.change(function () {
			var val = $(this).val(), id = null;
			if (val) {
				id = val.split(':')[1];
			}

			$('#{{$field.ins_id|escape}}').val(id);
		})
		.closest('.item-link')
		.find('.insert-tracker-item')
		.clickModal({
			success: function (data) {
				$('#{{$field.ins_id|escape}}_selector')
					.object_selector('set', "trackeritem:" + data.itemId, data.itemTitle);
				$.closeModal({});
			}
		});
	{/jq}
{/if}
</div>
