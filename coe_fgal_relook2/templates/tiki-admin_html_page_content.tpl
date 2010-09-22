{title}{tr}Admin HTML Page Dynamic Zones{/tr}{/title}

<h2>{tr}Page{/tr}: {$pageName}</h2>

<div class="navbar">
	{button href="tiki-admin_html_pages.php" _text="{tr}Admin HTML pages{/tr}"}
	{assign var='pname' value=$pageName|escape:"url"}
	{button href="tiki-admin_html_pages.php?pageName=$pname" _text="{tr}Edit this page{/tr}"}
	{button href="tiki-page.php?pageName=$pname" _text="{tr}View page{/tr}"}
</div>

{if $zone}
	<h2>{tr}Edit zone{/tr}</h2>
	<form action="tiki-admin_html_page_content.php" method="post">
		<input type="hidden" name="pageName" value="{$pageName|escape}" />
		<input type="hidden" name="zone" value="{$zone|escape}" />
		<table class="formcolor">
			<tr>
				<td>{tr}Zone{/tr}:</td>
				<td>{$zone}</td>
			</tr>
			<tr>
				<td>{tr}Content{/tr}:</td>
				<td>
					{if $type eq 'ta'}
						<textarea rows="5" cols="60" name="content">{$content|escape}</textarea>
					{else}
						<input type="text" name="content" value="{$content|escape}" />
					{/if}
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
			</tr>
		</table>
	</form>
{/if}

<h2>{tr}Dynamic zones{/tr}</h2>

{include file='find.tpl'}

<form action="tiki-admin_html_page_content.php" method="post">
	<input type="hidden" name="pageName" value="{$pageName|escape}" />
	<input type="hidden" name="zone" value="{$zone|escape}" />
	<table class="normal">
		<tr>
			<th>
				<a href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'zone_desc'}zone_asc{else}zone_desc{/if}">{tr}zone{/tr}</a>
			</th>
			<th>
				<a href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'content_desc'}content_asc{else}content_desc{/if}">{tr}content{/tr}</a>
			</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=user loop=$channels}
			<tr class="{cycle}">
				<td>{$channels[user].zone}</td>
				<td>
					{if $channels[user].type eq 'ta'}
						<textarea name="{$channels[user].zone|escape}" cols="20" rows="4">{$channels[user].content|escape}</textarea>
					{else}
						<input type="text" name="{$channels[user].zone|escape}" value="{$channels[user].content|escape}" />
					{/if}
				</td>
				<td>
					<a title="{tr}Edit{/tr}" class="link" href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;zone={$channels[user].zone}">{icon _id='page_edit'}</a>
				</td>
			</tr>
		{/section}
	</table>

	<div align="center">
		<input type="submit" name="editmany" value="{tr}Mass update{/tr}" />
	</div>
</form>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
