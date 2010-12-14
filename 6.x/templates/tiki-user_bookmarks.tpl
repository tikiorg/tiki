{title help="User+Bookmarks"}{tr}User Bookmarks{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

{if $parentId>0}[<a class="link" href="tiki-user_bookmarks.php">{tr}top{/tr}</a>] {/if}{tr}Current folder:{/tr} {$path}<br />
<br />
<h2>{tr}Folders{/tr}</h2>
	<table class="normal">
		<tr>
			<th>{tr}Name{/tr}</th>
			<th>{tr}Action{/tr}</th>
		</tr>
{cycle values="odd,even" print=false}
		{section name=ix loop=$folders}
			<tr class="{cycle}">
				<td><a href="tiki-user_bookmarks.php?parentId={$folders[ix].folderId}">
					{icon _id='folder' alt="{tr}Folder in{/tr}"}</a>&nbsp;{$folders[ix].name|escape} ({$folders[ix].urls})
				</td>
				<td>
					<a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editfolder={$folders[ix].folderId}">
						{icon _id='page_edit'}
					</a> &nbsp;
					<a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;removefolder={$folders[ix].folderId}">
						{icon _id='cross' alt="{tr}Remove{/tr}" title="{tr}Remove Folder{/tr}"}
					</a>
				</td>
			</tr>
		{sectionelse}
			<tr><td colspan="2" class="odd">{tr}No records found.{/tr}</td></tr>
		{/section}
	</table>

<br />
<h2>{tr}Bookmarks{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}Name{/tr}</th>
		<th>{tr}Url{/tr}</th>
		<th>{tr}Action{/tr}</th>
	</tr>
{cycle values="odd,even" print=false}
	{section name=ix loop=$urls}
		<tr class="{cycle}">
			<td><a class="link" target="_blank" href="{$urls[ix].url}">{$urls[ix].name|escape}</a>
				{if $tiki_p_cache_bookmarks eq 'y' and $urls[ix].datalen > 0}
					(<a href="tiki-user_cached_bookmark.php?urlid={$urls[ix].urlId}" class="link" target="_blank">{tr}Cache{/tr}</a>)
				{/if}
			</td>
			<td>{textformat wrap="60" wrap_cut=true wrap_char="<br />"}{$urls[ix].url}{/textformat}</td>
			<td>
				<a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editurl={$urls[ix].urlId}">{icon _id='page_edit'}</a>
				{if $tiki_p_cache_bookmarks eq 'y' and $urls[ix].datalen > 0}
					<a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;refreshurl={$urls[ix].urlId}">
						{icon _id='arrow_refresh' alt="{tr}Refresh Cache{/tr}"}
					</a>
				{/if}
				&nbsp; <a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;removeurl={$urls[ix].urlId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
			</td>
		</tr>
	{sectionelse}
		<tr><td colspan="3" class="odd">{tr}No records found.{/tr}</td></tr>
	{/section}
</table>
<br />
<h2>{tr}Admin folders and bookmarks{/tr}</h2>
<table class="normal" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<h3>{if $editfolder}{tr}Edit{/tr}{else}{tr}Add{/tr}{/if} {tr}a folder{/tr}</h3>
			{if $editfolder}
				<a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editfolder=0">{tr}New{/tr}</a>
			{/if}
			{* form to add a category *}
			<table class="formcolor">
				<form action="tiki-user_bookmarks.php" method="post">
					<input type="hidden" name="editfolder" value="{$editfolder|escape}" />
					<input type="hidden" name="parentId" value="{$parentId|escape}" />
					<tr>
						<td>{tr}Name:{/tr}</td>
						<td><input type="text" size = "40" name="foldername" value="{$foldername|escape}" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<input type="submit" name="addfolder"  value="{tr}Add{/tr}" /></td>
					</tr>
				</form>
			</table>
		</td>
		<td>
			{* form to add a url *}
			<h3>{if $urlname}{tr}Edit{/tr}{else}{tr}Add{/tr}{/if} {tr}a bookmark{/tr}</h3>
			{if $urlname}
				<a class="link" href="tiki-user_bookmarks.php?parentId={$parentId}&amp;editurl=0">{tr}New{/tr}</a>
			{/if}
			<table class="formcolor">
				<form action="tiki-user_bookmarks.php" method="post">
					<input type="hidden" name="editurl" value="{$editurl|escape}" />
					<input type="hidden" name="parentId" value="{$parentId|escape}" />
   				<tr>
						<td>{tr}Name:{/tr}</td>
						<td><input type="text" size = "40"  name="urlname" value="{$urlname|escape}" /></td>
					</tr>
					<tr>
						<td>{tr}URL:{/tr}</td>
						<td><input type="text" size = "40" name="urlurl" value="{$urlurl|escape}" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" name="addurl" value="{tr}Add{/tr}" /></td>
					</tr>
				</form>
			</table>
		</td>
	</tr>
</table>
