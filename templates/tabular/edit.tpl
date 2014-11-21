{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="navigation"}
	<div class="form-group">
		<a class="btn btn-default" href="{service controller=tabular action=manage}">{icon name=list} {tr}Manage{/tr}</a>
		<a class="btn btn-default" href="{service controller=tabular action=create}">{icon name=create} {tr}New{/tr}</a>
	</div>
{/block}

{block name="content"}
	<form class="form-horizontal edit-tabular" method="post" action="{service controller=tabular action=edit tabularId=$tabularId}">
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Name{/tr}</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" name="name" value="{$name|escape}" required>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Fields{/tr}</label>
			<div class="col-sm-9">
				<table class="table">
					<thead>
						<tr>
							<th>{tr}Field{/tr}</th>
							<th>{tr}Mode{/tr}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr class="hidden">
							<td>{icon name=sort} <span class="field">Field Name</span></td>
							<td class="mode">Mode</td>
							<td class="text-right"><button class="remove">{icon name=remove}</button></td>
						</tr>
						{foreach $schema->getColumns() as $column}
							<tr>
								<td>{icon name=sort} <span class="field">{$column->getField()|escape}</span></td>
								<td class="mode">{$column->getMode()|escape}</td>
								<td class="text-right"><button class="remove">{icon name=remove}</button></td>
							</tr>
						{/foreach}
					</tbody>
					<tfoot>
						<tr>
							<td>
								<select class="selection">
									{foreach $schema->getAvailableFields() as $permName => $label}
										<option value="{$permName|escape}" {if $permName eq 'itemId'} selected {/if}>{$label|escape}</option>
									{/foreach}
								</select>
							</td>
							<td>
								<a href="{service controller=tabular action=select trackerId=$trackerId}" class="btn btn-default add-field">{tr}Select Mode{/tr}</a>
								<textarea name="fields" class="hidden">{$schema->getFormatDescriptor()|json_encode}</textarea>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div class="form-group submit">
			<div class="col-sm-9 col-sm-push-3">
				<input type="submit" class="btn btn-primary" value="{tr}Update{/tr}">
			</div>
		</div>
	</form>
{/block}
