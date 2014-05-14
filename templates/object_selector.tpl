<div class="object-selector">
<input
	type="text"
	id="{$object_selector.id|escape}"
	{if $object_selector.name}name="{$object_selector.name|escape}"{/if}
	{if $object_selector.class}class="{$object_selector.class|escape}"{/if}
	{if $object_selector.value}value="{$object_selector.value|escape}"{/if}
	{if $object_selector.title}data-title="{$object_selector.title|escape}"{/if}
	data-filters="{$object_selector.filter|escape}"
	data-threshold="{$prefs.maxRecords|escape}"
>
	<div class="basic-selector hidden">
		<select class="form-control">
		</select>
	</div>

	<div class="panel panel-default hidden">
		<div class="panel-heading">
			<div class="input-group">
				<span class="input-group-addon">
					<span class="glyphicon glyphicon-search"></span>
				</span>
				<input type="text" placeholder="{tr}Title...{/tr}" value="" class="filter form-control" autocomplete="off">
				<div class="input-group-btn">
					<button class="btn btn-default search">{tr}Find{/tr}</button>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div class="results">
				<p>{tr}Too many options to display, filter your results.{/tr}</p>
			</div>
			<p class="no-results hidden">
				{tr}No matching results.{/tr}
			</p>
		</div>
		<div class="panel-footer">
			{tr}Current selection:{/tr}
			<span class="selection"></span>
		</div>
	</div>
</div>

{jq}
$('#{{$object_selector.id|escape}}')
	.object_selector();
{/jq}
