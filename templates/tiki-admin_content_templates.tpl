{title help='Content+Templates' url='tiki-admin_content_templates.php'}{tr}Content templates{/tr}{/title}


{tabset}
	{tab name="{tr}Templates{/tr}"}
		<h2>{tr}Templates{/tr}</h2>
		{if $channels or ($find ne '')}
			{include file='find.tpl'}
		{/if}
		{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
		{if $prefs.javascript_enabled !== 'y'}
			{$js = 'n'}
			{$libeg = '<li>'}
			{$liend = '</li>'}
		{else}
			{$js = 'y'}
			{$libeg = ''}
			{$liend = ''}
		{/if}

		<table class="table table-striped table-hover">
			<tr>
				<th>{tr}Id{/tr}</th>
				<th>
					<a href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
				</th>
				<th>
					<a href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Last Modified{/tr}</a>
				</th>
				<th>{tr}Sections{/tr}</th>
				<th>{tr}Categories{/tr}</th>
				{if $prefs.lock_content_templates eq 'y'}
					<th></th>
				{/if}
				<th></th>
			</tr>
			{cycle values="odd,even" print=false advance=false}
			{section name=user loop=$channels}
				<tr>
					<td class="text">{$channels[user].templateId}</td>
					<td class="text">
						{if $channels[user].edit}
							<a href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;templateId={$channels[user].templateId}&cookietab=2">
								{$channels[user].name|escape}
							</a>
						{else}
							{$channels[user].name|escape}
						{/if}
					</td>
					<td class="date">{$channels[user].created|tiki_short_datetime}</td>
					<td class="text">
						{if count($channels[user].sections) == 0}{tr}Visible in no sections{/tr}{/if}
						{section name=ix loop=$channels[user].sections}
							{$channels[user].sections[ix]}
							{if $channels[user].edit}
								<a class="tips" title=":{tr}Delete{/tr}" class="link" href="tiki-admin_content_templates.php?removesection={$channels[user].sections[ix]}&amp;rtemplateId={$channels[user].templateId}" >
									{icon name='remove' alt="{tr}Remove section{/tr}"}
								</a>
							{/if}
						{/section}
					</td>
					<td class="text">
						{if count($channels[user].categories) == 0}{tr}Uncategorized{/tr}{/if}
						{foreach $channels[user].categories as $categId => $catName}
							<a title="{tr}Browse{/tr}" class="link" href="{$categId|sefurl:'category'}" >
								{tr}{$catName}{/tr}
							</a>
						{/foreach}
					</td>
					{if $prefs.lock_content_templates eq 'y'}
						<td class="action">
							{lock type='template' object=$channels[user].templateId}
						</td>
					{/if}
					<td class="action">
						{if $channels[user].edit or $channels[user].remove}
							{capture name=template_actions}
								{strip}
									{if $channels[user].edit}
										{$libeg}<a href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;templateId={$channels[user].templateId}&cookietab=2">
											{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
										</a>{$liend}
									{/if}
									{if $channels[user].remove}
										{$libeg}<a href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].templateId}" >
											{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
										</a>{$liend}
									{/if}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.template_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.template_actions}</ul></li></ul>
							{/if}
						{/if}
					</td>
				</tr>
			{sectionelse}
				{norecords _colspan=4}
			{/section}
		</table>
		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

	{/tab}
	{if $canEdit}
		{if $templateId}
			{$tabtitle="{tr}Edit template:{/tr} {$info.name|escape}"}
		{else}
			{$tabtitle="{tr}Create template{/tr}"}
		{/if}
		{tab name=$tabtitle}

			{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use the Administration page of each enabled feature to allow the use of content templates.{/tr}{/remarksbox}

			{if $preview eq 'y'}
				<h2>{tr}Preview{/tr}</h2>
				<div class="wikitext">{$parsed}</div>
			{/if}

			<h2>{$tabtitle}</h2>
			{if $templateId > 0 and $tiki_p_admin_content_templates eq 'y'}
				{button href="tiki-admin_content_templates.php" cookietab="2" _icon_name="create" _text="{tr}Create{/tr}"}
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
					<label class="col-sm-3 control-label" for="section_css">{tr}Sections{/tr}</label>
					<div class="col-sm-9">
						{$toolbar_section='admin'}
						{if $prefs.feature_cms_templates eq 'y'}
							<div class="col-sm-3 checkbox-inline">
								<label for="section_cms">
									<input type="checkbox" name="section_cms" id="section_cms" {if $info.section_cms eq 'y'}checked="checked"{/if}>
									{if $info.section_cms eq 'y'}{$toolbar_section='cms'}{/if}
									{tr}Articles{/tr}
								</label>
							</div>
						{/if}
						{if $prefs.feature_wiki_templates eq 'y'}
							<div class="col-sm-3 checkbox-inline">
								<label for="section_wiki">
									<input type="checkbox" name="section_wiki" id="section_wiki" {if $info.section_wiki eq 'y'}checked="checked"{/if}>
									{if $info.section_wiki eq 'y'}{$toolbar_section='wiki page'}{/if}
									{tr}Wiki{/tr}
								</label>
							</div>
						{/if}
						{if $prefs.feature_file_galleries_templates eq 'y'}
							<div class="col-sm-3 checkbox-inline">
								<label for="section_file_galleries">
									<input type="checkbox" name="section_file_galleries" id="section_file_galleries" {if $info.section_file_galleries eq 'y'}checked="checked"{/if}>
									{if $info.section_file_galleries eq 'y'}{$toolbar_section='admin'}{/if}
									{tr}File Galleries{/tr}
								</label>
							</div>
						{/if}
						{if $prefs.feature_newsletters eq 'y'}
							<div class="col-sm-3 checkbox-inline">
								<label for="section_newsletters" >
									<input type="checkbox" name="section_newsletters" id="section_newsletters" {if $info.section_newsletters eq 'y'}checked="checked"{/if}>
									{if $info.section_newsletters eq 'y'}{$toolbar_section='newsletters'}{/if}
									{tr}Newsletters{/tr}
								</label>
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
					<label class="col-sm-3 control-label" for="is_html">{tr}HTML{/tr}</label>
					<div class="col-sm-9">
						<input type="checkbox" name="section_wiki_html" id="is_html" class="form=control" {if $info.section_wiki_html eq 'y'}checked="checked"{/if}>
					</div>
				</div>
				{if $prefs.lock_content_templates eq 'y'}
					<div class="form-group">
						<label class="col-sm-3 control-label">{tr}Lock{/tr}</label>
						<div class="col-sm-9">
							{lock type='template' object=$templateId}
						</div>
					</div>
				{/if}
				<div class="form-group type-cond for-page">
					<label class="col-sm-3 control-label" for="page_name">{tr}Page Name{/tr}</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="page_name" id="page_name" value="{$info.page_name}" placeholder="{tr}Find{/tr}...">
						{autocomplete element='input[name=page_name]' type='pagename'}
					</div>
				</div>

				{include file='categorize.tpl'}

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
					<input type="submit" name="save" class="btn btn-primary" value="{tr}Save{/tr}" onclick="needToConfirm=false;">
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
		{/tab}
	{/if}
{/tabset}
