{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form role="form" method="post" action="{service controller=search_stored action=select}">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<label>
				<input type="radio" name="queryId" value="" checked>
				{tr}Create New{/tr}
			</label>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="label" class="control-label">{tr}Label{/tr}</label>
				<input type="text" class="form-control" name="label"/>
				<span class="help-block">{tr}This will help you recognize your stored queries if ever you want to modify or remove them.{/tr}</span>
			</div>
			<div class="form-group">
				<label for="priority" class="control-label">Priority</label>
				<select name="priority" class="form-control">
					{foreach $priorities as $key => $info}
						<option value="{$key|escape}">{$info.label|escape} - {$info.description|escape}</option>
					{/foreach}
				</select>
			</div>
			<div class="form-group">
				<label for="label" class="control-label">{tr}Description{/tr}</label>
				<textarea class="form-control" name="description" rows="5" data-codemirror="true" data-syntax="tiki">{$description|escape}</textarea>
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="{tr}Create{/tr}"/>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4>{tr}Use Existing{/tr}</h4>
		</div>
		<div class="panel-body">
		<table class="table">
			<thead>
				<tr>
					<th>{tr}Label{/tr}</th>
					<th>{tr}Last Modification{/tr}</th>
				</tr>
			</thead>
			<tbody>
				{foreach $queries as $query}
					<tr>
						<td>
							<label>
								<input class="" type="radio" name="queryId" value="{$query.queryId|escape}"> {$query.label|escape}
								<span class="label {$priorities[$query.priority].class|escape}">{$priorities[$query.priority].label|escape}</span>
							</label>
						</td>
						<td>
							{if $query.lastModif}
								{$query.lastModif|tiki_short_datetime}
							{else}
								{tr}Never{/tr}
							{/if}
						</td>
					</tr>
				{foreachelse}
					<tr>
						<td>
							{tr}No stored queries!{/tr}
						</td>
						<td>{tr}Never{/tr}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		<div class="form-group">
			<input type="submit" class="btn btn-default" value="{tr}Select{/tr}"/>
		</div>
		</div>
	</div>
</form>
{/block}
