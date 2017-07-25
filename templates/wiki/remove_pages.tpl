{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	{include file='access/include_items.tpl'}
	{$iname = ''}
	{if $extra.version === 'last'}
		{$iname = 'all'}
		{$idesc = 'all versions'}
	{elseif $extra.version === 'all'}
		{$iname = 'last'}
		{$idesc = 'last version only'}
	{/if}
	<form id='confirm-action' class='confirm-action' action="{service controller="$confirmController" action="$confirmAction"}" method="post">
		{$div_checkbox_redirect_display = 'block'}
		{if !empty($iname) && !$extra.one}
			<div class="checkbox">
				<label>
					<input type="checkbox" name="{$iname}" onclick="$('#div_checkbox_redirect').toggle(); if (!this.checked) $('#div_redirect').hide(); return true;"> {tr}Remove {$idesc}{/tr}
				</label>
			</div>
			{$div_checkbox_redirect_display = 'none'}
		{/if}
		{include file='access/include_hidden.tpl'}
		{if $prefs.feature_semantic eq 'y' and $prefs.feature_wiki_1like_redirection eq 'y' and $prefs.feature_wiki_pagealias eq 'y'}
			<div class="checkbox" id="div_checkbox_redirect" style="display:{$div_checkbox_redirect_display};">
					<label>
						<input type='checkbox' id='create_redirect' name='create_redirect' value='y' onclick="$('#div_redirect').toggle();return true;" > {tr}Create redirect{/tr}
						<a tabindex="0" target="_blank" data-toggle="popover" data-trigger="hover" title="{tr}Create a 301 Redirect (\"moved permanently\") to specified page. An SEO-friendly, automatic redirect from the page being deleted to the designated new page (ex.: for search engines or users that may have bookmarked the page being deleted){/tr}">
							{icon name='information'}
						</a>
					</label>
			</div>
			<div id="div_redirect" class="form-group" style="display:none;">
				<div class="col-sm-2">
					<label for="destpage" class="col-sm-2">{tr}Redirect to:{/tr}</label>
				</div>
				<div class="col-sm-10">
					<input type='text' id='destpage' name='destpage' class="form-control" value=''>
				</div>
			</div>
		{/if}
	</form><br><br>
	{include file='access/include_footer.tpl'}
{/block}
