{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<form method="post" action="{service controller=workspace action=create}" role="form" class="form">
		<div class="form-group">
			<label for="template" class="control-label">
				{tr}Template{/tr}
			</label>
			<select name="template" class="form-control">
				{foreach from=$templates item=template}
					<option value="{$template.templateId|escape}">{$template.name|escape}</option>
				{/foreach}
			</select>
		</div>
		<div class="form-group">
			<label for="name" class="control-label">
				{tr}Workspace Name{/tr}
			</label>
			<input type="text" name="name" value="" class="form-control"/>
		</div>
		<div class="submit">
			<input type="submit" class="btn btn-primary" value="{tr}Create{/tr}"/>
		</div>
	</form>
{/block}
