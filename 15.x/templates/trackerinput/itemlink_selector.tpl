{* $Id: itemlink.tpl 51847 2014-07-04 18:08:48Z lphuberdeau $ *}
<div class="item-link">
	{object_selector _id=$field.ins_id _simplevalue=$field.value _simplename=$field.ins_id _placeholder=$data.placeholder type="trackeritem" tracker_id=$field.options_map.trackerId tracker_status=$data.status _format=$data.format}
	{if $field.options_map.addItems}
		<a class="btn btn-default insert-tracker-item" href="{service controller=tracker action=insert_item trackerId=$field.options_map.trackerId}">{$field.options_map.addItems|escape}</a>
		{jq}
		$('#{{$field.ins_id|escape}}')
			.closest('.item-link')
			.find('.insert-tracker-item')
			.clickModal({
				success: function (data) {
					$('#{{$field.ins_id|escape}}')
						.object_selector('set', "trackeritem:" + data.itemId, data.itemTitle);
					$.closeModal();
				}
			});
		{/jq}
	{/if}
</div>
