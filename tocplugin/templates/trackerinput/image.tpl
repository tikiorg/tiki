<input type="file" name="{$field.ins_id}"{if isset($input_err)} value="{$field.value}"{/if}>
{if $field.value ne ''}
	<br>
	{$data.image_tag}
	{if $field.isMandatory ne 'y'}
		<a href="#" class="trkRemoveImage tips" title="{tr}Remove image{/tr}">{icon name='delete'}</a>
		{jq}
			$(".trkRemoveImage").click(function(e){
			    e.preventDefault();
				if (confirm("{tr}Are you sure you want to delete this image?{/tr}")) {
					$(this).parent().find('input[type=file]').attr('type', 'hidden').val('blank').trigger('change');
				}
			});
		{/jq}
	{/if}
{/if}
