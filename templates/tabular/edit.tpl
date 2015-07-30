{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="navigation"}
	<div class="navbar">
		{permission name=admin_trackers}
			<a class="btn btn-default" href="{service controller=tabular action=manage}">{icon name=list} {tr}Manage{/tr}</a>
			<a class="btn btn-default" href="{service controller=tabular action=create}">{icon name=create} {tr}New{/tr}</a>
		{/permission}
	</div>
{/block}

{block name="content"}
	<div class="table-responsive">
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
					<table class="table fields">
						<thead>
							<tr>
								<th>{tr}Field{/tr}</th>
								<th>{tr}Mode{/tr}</th>
								<th><abbr title="{tr}Primary Key{/tr}">{tr}PK{/tr}</abbr></th>
								<th><abbr title="{tr}Read-Only{/tr}">{tr}RO{/tr}</abbr></th>
								<th><abbr title="{tr}Export-Only{/tr}">{tr}EO{/tr}</abbr></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr class="hidden">
								<td>
									<div class="input-group input-group-sm">
										<span class="input-group-addon">{icon name=sort}</span>
										<input type="text" class="field-label form-control" />
										<div class="input-group-btn">
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
												<span class="align">{tr}Left{/tr}</span>
												<span class="caret"></span>
												<input class="display-align" type="hidden" value="left">
											</button>
											<ul class="dropdown-menu dropdown-menu-right" role="menu">
												<li><a class="align-option" href="#left">{tr}Left{/tr}</a></li>
												<li><a class="align-option" href="#center">{tr}Center{/tr}</a></li>
												<li><a class="align-option" href="#right">{tr}Right{/tr}</a></li>
												<li><a class="align-option" href="#justify">{tr}Justify{/tr}</a></li>
											</ul>
										</div>
									</div>
								</td>
								<td><span class="field">Field Name</span>:<span class="mode">Mode</span></td>
								<td><input class="primary" type="radio" name="pk" /></td>
								<td><input class="read-only" type="checkbox" /></td>
								<td><input class="export-only" type="checkbox" /></td>
								<td class="text-right"><button class="remove">{icon name=remove}</button></td>
							</tr>
							{foreach $schema->getColumns() as $column}
								<tr>
									<td>
										<div class="input-group input-group-sm">
											<span class="input-group-addon">{icon name=sort}</span>
											<input type="text" class="field-label form-control" style="width: auto" value="{$column->getLabel()|escape}" />
											<div class="input-group-btn">
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
													<span class="align">{$column->getDisplayAlign()|ucfirst|tra}</span>
													<span class="caret"></span>
													<input class="display-align" type="hidden" value="{$column->getDisplayAlign()|escape}">
												</button>
												<ul class="dropdown-menu dropdown-menu-right" role="menu">
													<li><a class="align-option" href="#left">{tr}Left{/tr}</a></li>
													<li><a class="align-option" href="#center">{tr}Center{/tr}</a></li>
													<li><a class="align-option" href="#right">{tr}Right{/tr}</a></li>
													<li><a class="align-option" href="#justify">{tr}Justify{/tr}</a></li>
												</ul>
											</div>
										</div>
									</td>
									<td>
										<a href="{service controller=tabular action=select trackerId=$trackerId permName=$column->getField() columnIndex=$column@index mode=$column->getMode()}" class="btn btn-link add-field">
											<span class="field">{$column->getField()|escape}</span>:
											<span class="mode">{$column->getMode()|escape}</span>
										</a>
									</td>
									<td><input class="primary" type="radio" name="pk" {if $column->isPrimaryKey()} checked {/if} /></td>
									<td><input class="read-only" type="checkbox" {if $column->isReadOnly()} checked {/if} /></td>
									<td><input class="export-only" type="checkbox" {if $column->isExportOnly()} checked {/if} /></td>
									<td class="text-right"><button class="remove">{icon name=remove}</button></td>
								</tr>
							{/foreach}
						</tbody>
						<tfoot>
							<tr>
								<td>
									<select class="selection form-control">
										<option disabled="disabled" selected="selected">{tr}Select a field...{/tr}</option>
										{foreach $schema->getAvailableFields() as $permName => $label}
											<option value="{$permName|escape}">{$label|escape}</option>
										{/foreach}
									</select>
								</td>
								<td>
									<a href="{service controller=tabular action=select trackerId=$trackerId}" class="btn btn-default add-field">{tr}Select Mode{/tr}</a>
									<textarea name="fields" class="hidden">{$schema->getFormatDescriptor()|json_encode}</textarea>
								</td>
								<td colspan="3">
									<div class="radio">
										<label>
											<input class="primary" type="radio" name="pk" {if ! $schema->getPrimaryKey()} checked {/if} />
											{tr}No primary key{/tr}
										</label>
									</div>
								</td>
							</tr>
						</tfoot>
					</table>
					<div class="help-block">
						<p><strong>{tr}Primary Key:{/tr}</strong> {tr}Required to import data. Can be any field as long as it is unique.{/tr}</p>
						<p><strong>{tr}Read-only:{/tr}</strong> {tr}When importing a file, read-only fields will be skipped, preventing them from being modified, but also speeding-up the process.{/tr}</p>
						<p>{tr}When two fields affecting the same value are included in the format, such as the ID and the text value for an Item Link field, one of the two fields must be marked as read-only to prevent a conflict.{/tr}</p>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">{tr}Filters{/tr}</label>
				<div class="col-sm-9">
					<table class="table filters">
						<thead>
							<tr>
								<th>{tr}Field{/tr}</th>
								<th>{tr}Mode{/tr}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr class="hidden">
								<td>
									<div class="input-group input-group-sm">
										<span class="input-group-addon">{icon name=sort}</span>
										<input type="text" class="filter-label form-control" value="Label" />
										<div class="input-group-btn">
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
												<span class="position-label">{tr}Default{/tr}</span>
												<span class="caret"></span>
												<input class="position" type="hidden" value="default">
											</button>
											<ul class="dropdown-menu dropdown-menu-right" role="menu">
												<li><a class="position-option" href="#default">{tr}Default{/tr}</a></li>
												<li><a class="position-option" href="#primary">{tr}Primary{/tr}</a></li>
												<li><a class="position-option" href="#side">{tr}Side{/tr}</a></li>
											</ul>
										</div>
									</div>
								</td>
								<td><span class="field">Field Name</span>:<span class="mode">Mode</span></td>
								<td class="text-right"><button class="remove">{icon name=remove}</button></td>
							</tr>
							{foreach $filterCollection->getFilters() as $filter}
								<tr>
									<td>
										<div class="input-group input-group-sm">
											<span class="input-group-addon">{icon name=sort}</span>
											<input type="text" class="field-label form-control" value="{$filter->getLabel()|escape}" />
											<div class="input-group-btn">
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
													<span class="position-label">{$filter->getPosition()|ucfirst|tra}</span>
													<span class="caret"></span>
													<input class="position" type="hidden" value="{$filter->getPosition()|escape}">
												</button>
												<ul class="dropdown-menu dropdown-menu-right" role="menu">
													<li><a class="position-option" href="#default">{tr}Default{/tr}</a></li>
													<li><a class="position-option" href="#primary">{tr}Primary{/tr}</a></li>
													<li><a class="position-option" href="#side">{tr}Side{/tr}</a></li>
												</ul>
											</div>
										</div>
									</td>
									<td><span class="field">{$filter->getField()|escape}</span>:<span class="mode">{$filter->getMode()|escape}</td>
									<td class="text-right"><button class="remove">{icon name=remove}</button></td>
								</tr>
							{/foreach}
						</tbody>
						<tfoot>
							<tr>
								<td>
									<select class="selection form-control">
										<option disabled="disabled" selected="selected">{tr}Select a field...{/tr}</option>
										{foreach $filterCollection->getAvailableFields() as $permName => $label}
											<option value="{$permName|escape}">{$label|escape}</option>
										{/foreach}
									</select>
								</td>
								<td>
									<a href="{service controller=tabular action=select_filter trackerId=$trackerId}" class="btn btn-default add-filter">{tr}Select Mode{/tr}</a>
									<textarea name="filters" class="hidden">{$filterCollection->getFilterDescriptor()|json_encode}</textarea>
								</td>
							</tr>
						</tfoot>
					</table>
					<div class="help-block">
						<p>{tr}Filters will be available in parial export menus.{/tr}</p>
					</div>
				</div>
			</div>
			<div class="form-group submit">
				<div class="col-sm-9 col-sm-push-3">
					<input type="submit" class="btn btn-primary" value="{tr}Update{/tr}">
				</div>
			</div>
		</form>
	</div>
{/block}
