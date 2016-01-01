{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form action="{service controller=language action=upload}" method="post" role="form" class="form" enctype="multipart/form-data">
		<div class="form-group">
			<label for="language" class="control-label">
				{tr}Language{/tr}
			</label>
			<select id="language" class="translation_action form-control" name="language">
				{section name=ix loop=$languages}
					<option value="{$languages[ix].value|escape}" {if $language eq $languages[ix].value}selected="selected"{/if}>{$languages[ix].name}</option>
				{/section}
			</select>
		</div>
		<div class="form-group">
			<label class="control-label" for="file_type">
				{tr}File Type{/tr}
			</label>
			<select name="file_type" class="translation_action form-control">
				{foreach from=$fileTypes key=type_key item=type_name}
					<option value="{$type_key|escape}">
						{$type_name}
					</option>
				{/foreach}
			</select>
		</div>
		<div class="form-group">
			<label class="control-label" for="language_file">
				{tr}File{/tr}
			</label>
			<input name="language_file" type="file" required="required">
		</div>
		<div class="submit text-center">
			<input type="hidden" name="confirm" value="1">
			<input type="submit" class="btn btn-primary" name="upload_language_file" value="{tr}Upload{/tr}">
		</div>
	</form>
{/block}