{title help="Html+Pages"}{tr}Admin HTML pages{/tr}{/title}

{if $pageName ne ''}
	<div class="navbar">
		{button _text="{tr}Create new HTML page{/tr}"}
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
	<input type="hidden" name="pageName" value="{$pageName|escape}" />
	<table class="formcolor">
		<tr>
			<td style="width:150px;">{tr}Page name:{/tr}</td>
			<td>
				<input type="text" maxlength="255" size="40" name="pageName" value="{$info.pageName|escape}" />
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
				<input type="text" size="5" name="refresh" value="{$info.refresh|escape}" /> {tr}seconds{/tr}
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
				<input type="submit" name="preview" value="{tr}Preview{/tr}" /> 
				<input type="submit" name="save" value="{tr}Save{/tr}" />
			</td>
		</tr>
	</table>
</form>

<br />
<h2>{tr}HTML pages{/tr}</h2>
{if $channels}
	{include file='find.tpl'}
{/if}
<table class="normal">
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
		<th style="width:100px;">{tr}Action{/tr}</th>
	</tr>

	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		<tr class="{cycle}">
			<td>{$channels[user].pageName}</td>
			<td>{$channels[user].type} {if $channels[user].type eq 'd'}({$channels[user].refresh} secs){/if}</td>
			<td>{$channels[user].created|tiki_short_datetime}</td>
			<td>
				<a class="link" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;pageName={$channels[user].pageName|escape:"url"}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>

				<a class="link" href="tiki-page.php?pageName={$channels[user].pageName|escape:"url"}" title="View">{icon _id='monitor' alt="{tr}View{/tr}"}</a>

				<a class="link" href="tiki-admin_html_page_content.php?pageName={$channels[user].pageName|escape:"url"}" title="{tr}Admin dynamic zones{/tr}">{icon _id='page_gear' alt="{tr}Admin dynamic zones{/tr}"}</a> 

				<a class="link" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].pageName|escape:"url"}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
			</td>
		</tr>
	{sectionelse}
	<tr>
		<td colspan="4" class="odd">{tr}No records found{/tr}</td>
	</tr>
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
