{title help="Content+Templates"}{tr}Admin templates{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use the Administration page of each enabled feature to allow the use of content templates.{/tr}{/remarksbox}

{if $preview eq 'y'}
	<h2>{tr}Preview{/tr}</h2>
	<div class="wikitext">{$parsed}</div>
{/if}

{if $templateId > 0}
	<h2>{tr}Edit this template:{/tr} {$info.name|escape}</h2>
	{button href="tiki-admin_content_templates.php" _text="{tr}Create new template{/tr}"}
{else}
	<h2>{tr}Create new template{/tr}</h2>
{/if}
<form action="tiki-admin_content_templates.php" method="post" class="form-horizontal" role="form">
	<input type="hidden" name="templateId" value="{$templateId|escape}">
	<div class="form-group">
		<label class="col-sm-3 control-label" for="name">{tr}Name{/tr} *</label>
		<div class="col-sm-9">
			<input type="text" maxlength="255" class="form-control" id="name" name="name" value="{$info.name|escape}">
			{if $emptyname}
				<span class="attention alert-warning">{tr}Name field is mandatory{/tr}</span>
			{/if}
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="section_css">{tr}Use in{/tr}</label>
		<div class="col-sm-9">
			{$toolbar_section='admin'}
			{if $prefs.feature_cms_templates eq 'y'}
				<div class="col-sm-3 checkbox-inline">
					<label for="section_cms">{tr}CMS{/tr} ({tr}Articles{/tr})</label>
					<input type="checkbox" name="section_cms" id="section_cms" {if $info.section_cms eq 'y'}checked="checked"{/if}>
					{if $info.section_cms eq 'y'}{$toolbar_section='cms'}{/if}
				</div>
			{/if}
			{if $prefs.feature_wiki_templates eq 'y'}
				<div class="col-sm-3 checkbox-inline">
					<label for="section_wiki">{tr}Wiki{/tr}</label>
					<input type="checkbox" name="section_wiki" id="section_wiki" {if $info.section_wiki eq 'y'}checked="checked"{/if}>
					{if $info.section_wiki eq 'y'}{$toolbar_section='wiki page'}{/if}
				</div>
			{/if}
			{if $prefs.feature_file_galleries_templates eq 'y'}
				<div class="col-sm-3 checkbox-inline">
					<label for="section_file_galleries">{tr}File Galleries{/tr}</label>
					<input type="checkbox" name="section_file_galleries" id="section_file_galleries" {if $info.section_file_galleries eq 'y'}checked="checked"{/if}>
					{if $info.section_file_galleries eq 'y'}{$toolbar_section='admin'}{/if}
				</div>
			{/if}
			{if $prefs.feature_newsletters eq 'y'}
				<div class="col-sm-3 checkbox-inline">
					<label for="section_newsletters" >{tr}Newsletters{/tr}</label><input type="checkbox" name="section_newsletters" id="section_newsletters" {if $info.section_newsletters eq 'y'}checked="checked"{/if}>
					{if $info.section_newsletters eq 'y'}{$toolbar_section='newsletters'}{/if}
				</div>
			{/if}
			{if $prefs.feature_events eq 'y'}
				<div class="col-sm-3 checkbox-inline">
					<label for="section_events">{tr}Events{/tr}</label><input type="checkbox" name="section_events" id="section_events" {if $info.section_events eq 'y'}checked="checked"{/if}>
					{if $info.section_events eq 'y'}{$toolbar_section='calendar'}{/if}
				</div>
			{/if}
			{if $prefs.feature_html_pages eq 'y'}
				<div class="col-sm-3 checkbox-inline">
					<label for="section_html">{tr}HTML Pages{/tr}</label><input type="checkbox" name="section_html" id="section_html" {if $info.section_html eq 'y'}checked="checked"{/if}>
					{if $info.section_html eq 'y'}{$toolbar_section='wiki page'}{/if}
				</div>
			{/if}
			{if ($prefs.feature_cms_templates ne 'y') and ($prefs.feature_wiki_templates ne 'y') and ($prefs.feature_file_galleries_templates ne 'y') and ($prefs.feature_newsletters ne 'y') and ($prefs.feature_events ne 'y') and ($prefs.feature_html_pages ne 'y')}
				{tr}No features are configured to use templates.{/tr}
			{/if}
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="type-selector">{tr}Template Type{/tr}</label>
		<div class="col-sm-9">
			<select name="template_type" id="type-selector" class="form-control">
				<option value="static"{if $info.template_type eq 'static'} selected="selected"{/if}>{tr}Text area{/tr}</option>
				<option value="page"{if $info.template_type eq 'page'} selected="selected"{/if}>{tr}Wiki Page{/tr}</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="is_html">{tr}Is HTML{/tr}</label>
		<div class="col-sm-9 checkbox-inline">
			<input type="checkbox" name="section_wiki_html" id="is_html" class="form=control" {if $info.section_wiki_html eq 'y'}checked="checked"{/if}>
		</div>
	</div>
	<div class="form-group type-cond for-page">
		<label class="col-sm-3 control-label" for="page_name">{tr}Page Name{/tr}</label>
		<div class="col-sm-9">
			<input type="text" name="page_name" id="page_name" value="{$info.page_name}">
			{autocomplete element='input[name=page_name]' type='pagename'}
		</div>
	</div>

	<div class="form-group type-cond for-static">
		<label class="col-sm-12" for="editwiki">{tr}Template{/tr}</label>
		<div class="col-sm-12">
			{if $prefs.feature_wysiwyg eq 'y' and $info.section_wiki_html eq 'y'}
				{$use_wysiwyg='y'}
				<input type="hidden" name="allowhtml" value="on">
				{if $prefs.wysiwyg_htmltowiki eq 'y'}{$is_html = 'y'}{else}{$is_html = 'n'}{/if}
			{else}
				{$use_wysiwyg='n'}
				{$is_html = 'n'}
			{/if}
			{textarea id="editwiki" name="content" switcheditor="y" _class="form-control" _wysiwyg=$use_wysiwyg _is_html=$is_html section=$toolbar_section}{$info.content}{/textarea}
		</div>
	</div>
	<div class="form-group text-center">
		<input type="submit" name="preview" class="btn btn-default" value="{tr}Preview{/tr}" onclick="needToConfirm=false;">
		<input type="submit" name="save" class="btn btn-default" value="{tr}Save{/tr}" onclick="needToConfirm=false;">
	</div>

	{jq}
		$('#type-selector').change( function( e ) {
			$('.type-cond').hide();
			var val = $('#type-selector').val();
			$('.for-' + val).show();
		} ).trigger('change');
		needToConfirm = false;
	{/jq}
</form>

<hr>

<h2>{tr}Templates{/tr}</h2>
{if $channels or ($find ne '')}
	{include file='find.tpl'}
{/if}

<table class="table normal">
	<tr>
		<th>
			<a href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Last Modified{/tr}</a>
		</th>
		<th>{tr}Sections{/tr}</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false advance=false}
	{section name=user loop=$channels}
		<tr>
			<td class="text">{$channels[user].name|escape}</td>
			<td class="date">{$channels[user].created|tiki_short_datetime}</td>
			<td class="text">
				{if count($channels[user].sections) == 0}{tr}Visible in no sections{/tr}{/if}
				{section name=ix loop=$channels[user].sections}
					{$channels[user].sections[ix]}
					<a title="{tr}Delete{/tr}" class="link" href="tiki-admin_content_templates.php?removesection={$channels[user].sections[ix]}&amp;rtemplateId={$channels[user].templateId}" >
						{icon _id='cross' alt="{tr}Remove section{/tr}"}
					</a>
				{/section}
			</td>
			<td class="action">
				<a title="{tr}Edit{/tr}" class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;templateId={$channels[user].templateId}">
					{icon _id='page_edit'}
				</a>
				<a title="{tr}Delete{/tr}" class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].templateId}" >
					{icon _id='cross' alt="{tr}Delete{/tr}"}
				</a>
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=4}
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
