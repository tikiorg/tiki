{* $Id$ *}
<div class="item-link" id="il{$field.ins_id}">
	{if $data.selectMultipleValues}
		<input type="hidden" name="{$field.ins_id}_old" value="{$field.value|escape}" />
	{/if}
	{if $data.displayFieldsListType === 'table'}
		{wikiplugin _name=trackerlist _compactArguments_=$data.trackerListOptions}{/wikiplugin}
		{if $field.options_map.addItems and $data.createTrackerItems}
			{if $data.predefined}
				<div class="form-group">
					<div class="input-group col-sm-6">
						<select name="addaction" class="form-control">
							<option value="">{$field.options_map.addItems|escape}</option>
							{foreach key=itemId item=label from=$data.predefined}
								<option value="{$itemId}">{$label|escape}</option>
							{/foreach}
						</select>
						<span class="input-group-btn">
							<a class="btn btn-default insert-tracker-item" href="{service controller=tracker action=insert_item trackerId=$field.options_map.trackerId next=$data.next|escape}">{tr}OK{/tr}</a>
						</span>
					</div>
				</div>
			{else}
				<a class="btn btn-default insert-tracker-item" href="{service controller=tracker action=insert_item trackerId=$field.options_map.trackerId next=$data.next|escape}">{$field.options_map.addItems|escape}</a>
			{/if}
			{if $field.options_map.preSelectFieldThere}
				<a class="btn btn-default update-tracker-links" href="{service controller=tracker action=link_items trackerId=$field.options_map.trackerId next=$data.next|escape}">{tr}Update{/tr}</a>
			{jq}
				var preselectedValue = function() {
					var preselectedEl = $("#il{{$field.ins_id}}").closest('form').find('[name=ins_{{$field.options_map.preSelectFieldHere}}]');
					return preselectedEl.length > 0 ? preselectedEl.val() : $("#il{{$field.ins_id}}").closest('form').find('#trackerinput_{{$field.options_map.preSelectFieldHere}}').text();
				}
				$("#il{{$field.ins_id}}").find('.insert-tracker-item').on('click', function() {
					var itemId = $('#il{{$field.ins_id}} select[name=addaction]').val();
					if( itemId ) {
						$(this).attr('href', "tiki-ajax_services.php?controller=tracker&action=clone_item&trackerId={{$field.options_map.trackerId}}&next={{$data.next|escape}}&itemId="+itemId+'&ins_{{$field.options_map.preSelectFieldThere}}='+tiki_encodeURIComponent(preselectedValue()));
					} else {
						$(this).attr('href', "tiki-ajax_services.php?controller=tracker&action=insert_item&trackerId={{$field.options_map.trackerId}}&next={{$data.next|escape}}&ins_{{$field.options_map.preSelectFieldThere}}="+tiki_encodeURIComponent(preselectedValue()));
					}
				});
				$("#il{{$field.ins_id}}").find('.update-tracker-links').on('click', function(e) {
					e.preventDefault();
					$.ajax({
						url: this.href,
						data: {
							items: $('input[name="{{$field.ins_id}}[]"]:checked').map(function(i, el){ return $(el).val(); }).toArray(),
							linkField: {{$field.options_map.preSelectFieldThere|json_encode}},
							linkValue: preselectedValue(),
							trackerlistParams: {{$data.trackerListOptions|json_encode}}
						},
						success: function(data) {
							$('#il{{$field.ins_id}} .ts-wrapperdiv').replaceWith(JSON.parse(data));
							$("#il{{$field.ins_id}} a[data-type=trackeritem]").clickModal({});
						}
					})
				});
			{/jq}
			{/if}
			{jq}
				$("#il{{$field.ins_id}}")
					.find('.insert-tracker-item')
					.clickModal({
						success: function (data) {
							var displayed = {{$data.list|json_encode}};
							var row = '<tr>';
							if( {{$data.trackerListOptions.checkbox|json_encode}} ) {
								row += '<td><input type="checkbox" class="{{$field.ins_id}}-checkbox" name="{{$field.ins_id}}[]" value="'+( data.created ? data.created : data.itemId )+'" checked /></td>';
							}
							$.each(displayed, function(fieldId, permName) {
								if( $('#il{{$field.ins_id}} th').filter(function(i, el){ return $(el).hasClass('field'+fieldId); }).length > 0 ) {
									row += '<td>'+data.processedFields[permName]+'</td>';
								}
							});
							row += '</tr>';
							$row = $(row);
							$('#il{{$field.ins_id}} table')
								.find('tbody').append($row)
								.trigger('addRows', [$row, true]);
							$.closeModal();
						}
					});
				$("#il{{$field.ins_id}} a[data-type=trackeritem]").clickModal({});
			{/jq}
		{/if}
	{else}
		<select name="{$field.ins_id}{if $data.selectMultipleValues}[]{/if}" {if $data.preselection and $data.crossSelect neq 'y'}disabled="disabled"{/if} {if $data.selectMultipleValues}multiple="multiple"{/if} class="form-control">
			{if $field.isMandatory ne 'y' || empty($field.value)}
				<option value=""></option>
			{/if}
			{foreach key=id item=label from=$data.list}
				<option value="{$id|escape}" {if $data.preselection and !$field.value and $data.preselection eq $id or (($data.selectMultipleValues and is_array($field.value) and in_array($id, $field.value) or $field.value eq $id))}selected="selected"{/if}>
					{$label|escape}
				</option>
			{/foreach}
		</select>
		{if $field.options_map.addItems and $data.createTrackerItems}
			<a class="btn btn-default insert-tracker-item" href="{service controller=tracker action=insert_item trackerId=$field.options_map.trackerId next=$data.next|escape}" data-href="{service controller=tracker action=insert_item trackerId=$field.options_map.trackerId next=$data.next|escape}">{$field.options_map.addItems|escape}</a>
			{if $field.options_map.preSelectFieldThere}
			{jq}
				$("#il{{$field.ins_id}}").find('.insert-tracker-item').on('click', function() {
					$(this).attr('href', $(this).data('href')+'&ins_{{$field.options_map.preSelectFieldThere}}='+$('#ins_{{$field.options_map.preSelectFieldHere}}').val());
				});
			{/jq}
			{/if}
			{jq}
				$("select[name={{$field.ins_id}}]").next().clickModal({
					success: function (data) {
						$('<option>')
							.attr('value', data.itemId)
							.text(data.itemTitle)
							.appendTo($(this).prev());
						$(this).prev().val(data.itemId);
						$(this).prev().trigger("change");
						$.closeModal();
					}
				});
			{/jq}
		{/if}
	{/if}
</div>
