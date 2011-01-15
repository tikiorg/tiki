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
<form action="tiki-admin_content_templates.php" method="post">
	<input type="hidden" name="templateId" value="{$templateId|escape}" />
	<table class="formcolor">
		<tr>
			<td><label for="name">{tr}Name:{/tr} (*)</label></td>
			<td>
				<input type="text" maxlength="255" size="40" id="name" name="name" value="{$info.name|escape}" /> 
				{if $emptyname}
					<span class="attention">{tr}Name field is mandatory{/tr}</span>
				{/if}
			</td>
		</tr>
		<tr>
			<td>{tr}Use in:{/tr}</td>
			<td>
				{if $prefs.feature_cms_templates eq 'y'}
					<input type="checkbox" name="section_cms" {if $info.section_cms eq 'y'}checked="checked"{/if} /> 
					{tr}CMS{/tr} ({tr}Articles{/tr})
					<br />
				{/if}
				{if $prefs.feature_wiki_templates eq 'y'}
					<label><input type="checkbox" name="section_wiki" {if $info.section_wiki eq 'y'}checked="checked"{/if} />
					{tr}Wiki{/tr}</label>
					<br />
				{/if}
				{if $prefs.feature_file_galleries_templates eq 'y'}
					<label><input type="checkbox" name="section_file_galleries" {if $info.section_file_galleries eq 'y'}checked="checked"{/if} />
					{tr}File Galleries{/tr}</label>
					<br />
				{/if}
				{if $prefs.feature_newsletters eq 'y'}
					<label><input type="checkbox" name="section_newsletters" {if $info.section_newsletters eq 'y'}checked="checked"{/if} />
					{tr}Newsletters{/tr}</label>
					<br />
				{/if}
				{if $prefs.feature_events eq 'y'}
					<label><input type="checkbox" name="section_events" {if $info.section_events eq 'y'}checked="checked"{/if} />
					{tr}Events{/tr}</label>
					<br />
				{/if}
				{if $prefs.feature_html_pages eq 'y'}
					<label><input type="checkbox" name="section_html" {if $info.section_html eq 'y'}checked="checked"{/if} />
					{tr}HTML Pages{/tr}</label>
					<br />
				{/if}
				{if ($prefs.feature_cms_templates ne 'y') and ($prefs.feature_wiki_templates ne 'y') and ($prefs.feature_file_galleries_templates ne 'y') and ($prefs.feature_newsletters ne 'y') and ($prefs.feature_events ne 'y') and ($prefs.feature_html_pages ne 'y')}
					{tr}No features are configured to use templates.{/tr}
				{/if}
			</td>
		</tr>

		<tr>
			<td>{tr}Template Type:{/tr}</td>
			<td>
				<select name="template_type" class="type-selector">
					<option value="static"{if $info.template_type eq 'static'} selected="selected"{/if}>{tr}Text area{/tr}</option>
					<option value="page"{if $info.template_type eq 'page'} selected="selected"{/if}>{tr}Wiki Page{/tr}</option>
				</select>
			</td>
		</tr>
		
		<tr class="type-cond for-page">
			<td>{tr}Page Name:{/tr}</td>
			<td>
				<input type="text" name="page_name" value="{$info.page_name}"/>
			</td>
		</tr>

		<tr class="type-cond for-static">
			<td colspan="2">
				<label for="editwiki">{tr}Template:{/tr}</label>
			</td>
		</tr>
		<tr class="type-cond for-static">
			<td colspan="2">
				{textarea id="editwiki" name="content" switcheditor="y"}{$info.content}{/textarea}
			</td>
		</tr>

		<tr>
			<td/>
			<td>
				<input type="submit" name="save" value="{tr}Save{/tr}" onclick="needToConfirm=false;" />
				<input type="submit" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;" />
			</td>
		</tr>
	</table>
	{jq}
		$('.type-selector').change( function( e ) {
			$('.type-cond').hide();
			var val = $('.type-selector').val();
			$('.for-' + val).show();
		} ).trigger('change');
		window.editorDirty = false;
	{/jq}
</form>

<hr />

<h2>{tr}Templates{/tr}</h2>
{if $channels or ($find ne '')}
	{include file='find.tpl'}
{/if}

<table class="normal">
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
		<tr class="{cycle}">
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
		{norecords _colspan="4"}
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
