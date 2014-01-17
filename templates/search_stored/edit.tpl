{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form role="form" class="form-horizontal" method="post" action="{service controller=search_stored action=edit queryId=$queryId}">
	<div class="form-group">
		<label for="label" class="col-md-3 control-label">{tr}Label{/tr}</label>
		<div class="col-md-9">
			<input type="text" class="form-control" name="label" value="{$label|escape}"/>
		</div>
	</div>
	<div class="form-group">
		<label for="priority" class="col-md-3 control-label">Priority</label>
		<div class="col-md-9">
			<select name="priority" class="form-control">
				{foreach $priorities as $key => $info}
					<option value="{$key|escape}" {if $priority eq $key}selected{/if}>{$info.label|escape} - {$info.description|escape}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="label" class="col-md-3 control-label">{tr}Description{/tr}</label>
		<div class="col-md-9">
			<textarea class="form-control" name="description" rows="5" data-codemirror="true" data-syntax="tiki">{$description|escape}</textarea>
		</div>
	</div>
	<div class="form-group submit">
		<div class="col-md-9 col-md-offset-3">
			<input type="submit" class="btn btn-primary" value="{tr}Update{/tr}"/>
		</div>
	</div>
</form>
{/block}
