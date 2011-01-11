{title help="notepad"}{tr}Notes{/tr}{/title}

	{include file='tiki-mytiki_bar.tpl'}

<div class="navbar">
	{button href="tiki-notepad_write.php" _text="{tr}Write a note{/tr}"}
</div>

<div style="text-align:center;">
	<div style="height:20px; width:200px; border:1px solid black; background-color:#666666; text-align:left; margin:0 auto;">
		<div style="background-color:red; height:100%; width:{$cellsize}px;"></div>
	</div>
	<small>{tr}quota{/tr}&nbsp;{$percentage}%</small>
</div>

{if count($channels) > 0 or $find ne ''}
	{include file='find.tpl'}
	<form action="tiki-notepad_list.php" method="post">
		<table class="normal">
			<tr>
				<th style="text-align:center;">
					<input type="submit" name="delete" value="{tr}x{/tr} " />
				</th>
				<th>
					<a href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
				</th>
				<th>
					<a href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'parse_mode_desc'}parse_mode_asc{else}parse_mode_desc{/if}">{tr}Type{/tr}</a>
				</th>
				<th>
					<a href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a>
				</th>
				<th>
					<a href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modified{/tr}</a>
				</th>
				<th style="text-align:right;">
					<a href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a>
				</th>
				<th style="text-align:center;">{tr}Actions{/tr}</th>
			</tr>
			{cycle values="odd,even" print=false}
			{section name=user loop=$channels}
				<tr class="{cycle}">
					<td style="text-align:center;">
						<input type="checkbox" name="note[{$channels[user].noteId}]" />
					</td>
					<td>
						<a class="link" href="tiki-notepad_read.php?noteId={$channels[user].noteId}">{$channels[user].name|escape}</a>
					</td>
					<td>{$channels[user].parse_mode}</td>
					<td>{$channels[user].created|tiki_short_datetime}</td>
					<td>{$channels[user].lastModif|tiki_short_datetime}</td>
					<td style="text-align:right;">{$channels[user].size|kbsize}</td>
					<td style="text-align:center;">
						<a href="tiki-notepad_get.php?noteId={$channels[user].noteId}" class="link">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>
						<a href="tiki-notepad_write.php?noteId={$channels[user].noteId}" class="link">{icon _id='page_edit'}</a>
						<a href="tiki-notepad_get.php?noteId={$channels[user].noteId}&amp;save=1" class="link">{icon _id='disk' alt="{tr}Save{/tr}"}</a>
						<a style="margin-left:10px;" class="link" href="tiki-notepad_read.php?noteId={$channels[user].noteId}&amp;remove=1">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
					</td>
				</tr>
			{sectionelse}
				<tr>
					<td colspan="4">{tr}No notes yet{/tr}</td>
				</tr>
			{/section}
			<tr>
				<td colspan="4">
					<input type="submit" name="merge" value="{tr}Merge selected notes into{/tr}" />
					<input type="text" name="merge_name" size="20" />
				</td>
			</tr>
		</table>
	</form>

	{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
{/if}

<h2>{tr}Upload file{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-notepad_list.php" method="post">
	<table class="formcolor">
		<tr>
			<td>{tr}Upload file:{/tr}</td>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" />
				<input size="16" name="userfile1" type="file" />
				<input style="font-size:9px;" type="submit" name="upload" value="{tr}Upload{/tr}" />
			</td>
		</tr>
	</table>
</form>
