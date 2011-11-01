{if ! $itemId}
	<form class="simple" method="post" action="{service controller=tracker action=insert_item}">
		{foreach from=$fields item=field}
			<label>
				{$field.name|escape}
				{if $field.isMandatory eq 'y'}
					<span class="mandatory_star">*</span>
				{/if}
				{trackerinput field=$field}
				<div class="description">
					{$field.description|escape}
				</div>
			</label>
		{/foreach}
		<div class="submit">
			<input type="hidden" name="trackerId" value="{$trackerId|escape}"/>
			<input type="submit" value="{tr}Create{/tr}"/>
			{foreach from=$forced key=permName item=value}
				<input type="hidden" name="forced~{$permName|escape}" value="{$value|escape}"/>
			{/foreach}
		</div>
	</form>
{else}
	{object_link type=trackeritem id=$itemId}
{/if}
