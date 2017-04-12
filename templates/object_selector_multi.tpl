<div class="object-selector-multi">
{if $object_selector_multi.separator}
	<input
		data-separator="{$object_selector_multi.separator|escape}"
		type="text"
		id="{$object_selector_multi.simpleid|escape}"
		{if $object_selector_multi.simpleclass}class="{$object_selector_multi.simpleclass|escape}"{/if}
		{if $object_selector_multi.simplename}name="{$object_selector_multi.simplename|escape}"{/if}
		value="{$object_selector_multi.separator|implode:$object_selector_multi.current_selection_simple|escape}"
	>
{/if}
<textarea
	id="{$object_selector_multi.id|escape}"
	{if $object_selector_multi.name}name="{$object_selector_multi.name|escape}"{/if}
	{if $object_selector_multi.class}class="{$object_selector_multi.class|escape}"{/if}
	{if $object_selector_multi.title}data-label="{$object_selector_multi.title|escape}"{/if}
	{if $object_selector_multi.parent}data-parent="{$object_selector_multi.parent|escape}"{/if}
	{if $object_selector_multi.parentkey}data-parentkey="{$object_selector_multi.parentkey|escape}"{/if}
	{if $object_selector_multi.format}data-format="{$object_selector_multi.format|escape}"{/if}
	{if $object_selector_multi.sort}data-sort="{$object_selector_multi.sort|escape}"{/if}
	data-filters="{$object_selector_multi.filter|escape}"
	data-threshold="{$object_selector_multi.threshold|default:$prefs.tiki_object_selector_threshold|escape}"
>{"\n"|implode:$object_selector_multi.current_selection}</textarea>
	<div class="basic-selector hidden">
		<select class="form-control" multiple>
			{foreach $object_selector_multi.current_selection as $object}
				<option value="{$object|escape}" selected="selected">{$object.title|escape}</option>
			{/foreach}
		</select>
	</div>

	<div class="panel panel-default hidden">
		<div class="panel-heading">
			<div class="input-group">
				<span class="input-group-addon">
					{icon name=search}
				</span>
				<input type="text" placeholder="{$object_selector_multi.placeholder|escape}..." value="" class="filter form-control" autocomplete="off">
				<div class="input-group-btn">
					<button class="btn btn-default search">{tr}Find{/tr}</button>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<p class="too-many">{tr}Search and select what you are looking for from the options that appear..{/tr}</p>
			<div class="results">
				{foreach $object_selector_multi.current_selection as $object}
					<div class="checkbox">
						<label>
							<input type="checkbox" value="{$object|escape}" checked>
							{$object.title|escape}
						</label>
					</div>
				{/foreach}
			</div>
			<p class="no-results hidden">
				{tr}No matching results.{/tr}
			</p>
		</div>
	</div>
</div>

{jq}
$('#{{$object_selector_multi.id|escape}}')
	.object_selector_multi();
{/jq}
