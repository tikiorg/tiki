<div class="object-selector">
<input
	type="text"
	id="{$object_selector.simpleid|escape}"
	{if $object_selector.simpleclass}class="{$object_selector.simpleclass|escape}"{/if}
	{if $object_selector.simplename}name="{$object_selector.simplename|escape}"{/if}
	{if $object_selector.simplevalue}value="{$object_selector.current_selection.id|escape}"{/if}
>
<input
	type="text"
	id="{$object_selector.id|escape}"
	{if $object_selector.name}name="{$object_selector.name|escape}"{/if}
	{if $object_selector.class}class="{$object_selector.class|escape}"{/if}
	{if $object_selector.current_selection}
		value="{$object_selector.current_selection|escape}"
		data-label="{$object_selector.current_selection.title|escape}"
	{/if}
	{if $object_selector.parent}data-parent="{$object_selector.parent|escape}"{/if}
	{if $object_selector.parentkey}data-parentkey="{$object_selector.parentkey|escape}"{/if}
	{if $object_selector.format}data-format="{$object_selector.format|escape}"{/if}
	data-filters="{$object_selector.filter|escape}"
	data-threshold="{$object_selector.threshold|default:$prefs.tiki_object_selector_threshold|escape}"
>
	<div class="basic-selector hidden">
		<select class="form-control">
			<option value="" class="protected">&mdash;</option>
			{if $object_selector.current_selection}
				<option value="{$object_selector.current_selection|escape}" selected="selected">{$object_selector.current_selection.title|escape}</option>
			{/if}
		</select>
	</div>

	<div class="panel panel-default hidden">
		<div class="panel-heading">
			<div class="input-group">
				<span class="input-group-addon">
					{icon name="search"}
				</span>
				<input type="text" placeholder="{$object_selector.placeholder|escape}..." value="" class="filter form-control" autocomplete="off">
				<div class="input-group-btn">
					<button class="btn btn-default search">{tr}Find{/tr}</button>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div class="results">
				<p class="too-many">{tr}Search and select what you are looking for from the options that appear.{/tr}</p>
				<div class="radio">
					<label>
						<input type="radio" {if ! $object_selector.current_selection} checked="checked" {/if} value="" name="{$object_selector.id|escape}_sel" class="protected">
						&mdash;
					</label>
				</div>
				{if $object_selector.current_selection}
					<div class="radio">
						<label>
							<input type="radio" checked="checked" value="{$object_selector.current_selection|escape}" data-label="{$object_selector.current_selection.title|escape}" name="{$object_selector.id|escape}_sel">
							{$object_selector.current_selection.title|escape}
						</label>
					</div>
				{/if}
			</div>
			<p class="no-results hidden">
				{tr}No matching results.{/tr}
			</p>
		</div>
	</div>
</div>

{jq}
$('#{{$object_selector.id|escape}}')
	.object_selector();
{/jq}
