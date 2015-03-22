{title help="Html+Pages"}{tr}Admin HTML pages{/tr}{/title}

{if $pageName ne ''}
	<div class="navt_bar">
		{button _icon_name="create" _text="{tr}Create{/tr}"}
	</div>
{/if}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use {literal}{ed id=name}{/literal} or {literal}{ted id=name}{/literal} to insert dynamic zones{/tr}{/remarksbox}

{if $preview eq 'y'}
	<h2>{tr}Preview{/tr}</h2>
	<div class="wikitext">{$parsed}</div>
{/if}

{if $pageName eq ''}
	<h2>{tr}Create new HTML page{/tr}</h2>
{else}
	<h2>{tr}Edit this HTML page:{/tr} {$pageName}</h2>
{/if}

<form action="tiki-admin_html_pages.php" method="post" id='editpageform'>
	<input type="hidden" name="pageName" value="{$pageName|escape}">
	<table class="formcolor">
		<tr>
			<td style="width:150px;">{tr}Page name:{/tr}</td>
			<td>
				<input type="text" maxlength="255" size="40" name="pageName" value="{$info.pageName|escape}">
			</td>
		</tr>

		{if $tiki_p_use_content_templates eq 'y'}
			<tr>
				<td>{tr}Apply template:{/tr}</td>
				<td>
					<select name="templateId"{if !$templates} disabled="disabled"{/if} onchange="javascript:document.getElementById('editpageform').submit();">
						<option value="0">{tr}none{/tr}</option>
						{section name=ix loop=$templates}
							<option value="{$templates[ix].templateId|escape}">{tr}{$templates[ix].name}{/tr}</option>
						{/section}
					</select>
				</td>
			</tr>
		{/if}

		<tr>
			<td>{tr}Type:{/tr}</td>
			<td>
				<select name="type">
					<option value='d'{if $info.type eq 'd'} selected="selected"{/if}>{tr}Dynamic{/tr}</option>
					<option value='s'{if $info.type eq 's'} selected="selected"{/if}>{tr}Static{/tr}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{tr}Refresh rate (if dynamic):{/tr}</td>
			<td>
				<input type="text" size="5" name="refresh" value="{$info.refresh|escape}"> {tr}seconds{/tr}
			</td>
		</tr>

		<tr>
			<td>
				{tr}Content:{/tr}
			</td>
			<td>
				<textarea name="content" id="htmlcode" rows="25" style="width:95%;">{$info.content|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="btn btn-default btn-sm" name="preview" value="{tr}Preview{/tr}">
				<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
			</td>
		</tr>
	</table>
</form>

<br>
<h2>{tr}HTML pages{/tr}</h2>
{if $channels}
	{include file='find.tpl'}
{/if}
<div class="table-responsive">
<table class="table normal table-striped table-hover">
	<tr>
		<th>
			<a href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Last Modif{/tr}</a>
		</th>
		<th style="width:100px;"></th>
	</tr>


	{section name=user loop=$channels}
		<tr>
			<td class="text">{$channels[user].pageName}</td>
			<td class="text">{$channels[user].type} {if $channels[user].type eq 'd'}({$channels[user].refresh} secs){/if}</td>
			<td class="date">{$channels[user].created|tiki_short_datetime}</td>
			<td class="action">
				{capture name=html_actions}
					{strip}
						<a href="tiki-page.php?pageName={$channels[user].pageName|escape:"url"}" title="View">
							{icon name='view' _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
						</a>
						<a href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;pageName={$channels[user].pageName|escape:"url"}">
							{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
						</a>
						<a href="tiki-admin_html_page_content.php?pageName={$channels[user].pageName|escape:"url"}" title="{tr}Admin dynamic zones{/tr}">
							{icon name='cog' _menu_text='y' _menu_icon='y' alt="{tr}Admin dynamic zones{/tr}"}
						</a>
						<a href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].pageName|escape:"url"}">
							{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
						</a>
					{/strip}
				{/capture}
				<a class="tips"
				   title="{tr}Actions{/tr}"
				   href="#" {popup trigger="click" fullhtml="1" center=true text=$smarty.capture.html_actions|escape:"javascript"|escape:"html"}
				   style="padding:0; margin:0; border:0"
						>
					{icon name='wrench'}
				</a>
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=4}
	{/section}
</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
