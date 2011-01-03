<h1><a class="pagetitle" href="tiki-admin_keywords.php">{tr}Admin keywords{/tr}</a></h1>

{if $keywords_updated}
	<div class="simplebox highlight">
		{if $keywords_updated == 'y'}{tr}Keywords have been updated{/tr} 
		{else}{tr}Updating keywords has failed. Page probably doesn't exist.{/tr}{/if}
		{if $keywords_updated_on} ({$keywords_updated_on|escape}){/if}
	</div>
{/if}
{if $edit_on}
	<div id="current_keywords">
		<h2>{tr}Edit page keywords{/tr} ({$edit_keywords_page|escape})</h2>
		<form action="tiki-admin_keywords.php" method="post">
			<input name="page" value="{$edit_keywords_page|escape}" type="hidden">
			<table class="formcolor">
				<tbody>
					<tr>
						<td style="padding-right: 25px;">{tr}Keywords:{/tr}</td>
						<td><input name="new_keywords" size="65" value="{$edit_keywords|escape}"/></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" name="save_keywords" value="{tr}Save{/tr}"/></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
{/if}
<br class="clear"/>
<h2>{tr}Current Page Keywords{/tr}</h2>
<form method="get" action="tiki-admin_keywords.php">
	<label for="q">{tr}Search by page:{/tr}</label>
	<input type="text" name="q" value="{if $smarty.request.q}{$smarty.request.q|escape}{/if}"/>
	<input type="submit" name="search" value="{tr}Go{/tr}"/>
</form>
{if $search_on}
	<div style="font-weight:bold;">{$search_cant|escape} {tr}results found!{/tr}</div>
{/if}
<br class="clear"/>
{if $existing_keywords}
	<table class="normal" style="width:100%;">
		<tbody>	
			<tr class="{cycle}">
				<td><h3>{tr}Page{/tr}</h3></td>
				<td><h3>{tr}Keywords{/tr}</h3></td>
				<td><h3>{tr}Actions{/tr}</h3></td>	
			</tr>	
			{cycle values="even,odd" print=false}
			{section name=i loop=$existing_keywords}
				<tr class="{cycle}">
					<td><a href="{$existing_keywords[i].page|sefurl}">{$existing_keywords[i].page|escape}</a></td>
					<td>{$existing_keywords[i].keywords|escape}</td>
					<td><a class="link" href="tiki-admin_keywords.php?page={$existing_keywords[i].page|escape:"url"}">{icon _id=page_edit}</a> <a class="link" href="tiki-admin_keywords.php?page={$existing_keywords[i].page|escape:"url"}&amp;remove_keywords=1">{icon _id=cross}</a></td>	
				</tr>
			{/section}
		</tbody>
	</table>
{else}
	<h2>{tr}No pages found{/tr}</h2>
{/if}
<br class="clear" />
{pagination_links cant=$pages_cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
<br class="clear" />
