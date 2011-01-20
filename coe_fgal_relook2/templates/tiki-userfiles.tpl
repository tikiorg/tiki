{*Smarty template*}

{title help="User+Files"}{tr}User Files{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

<div style="text-align:center;">
	<div style="height:20px; width:200px; border:1px solid black; background-color:#666666; text-align:left; margin:0 auto;">
		<div style="background-color:red; height:100%; width:{$cellsize}px;"></div>
	</div>
	{if $user neq 'admin'}
		<small>{tr}Used space:{/tr} {$percentage}% {tr}up to{/tr} {$limitmb} Mb</small>
	{else}
		<small>{tr}Used space:{/tr} {tr}no limit for admin{/tr}</small>
	{/if}
</div>
<form action="tiki-userfiles.php" method="post">
	<table class="normal">
		<tr>
			<th style="text-align:center;">&nbsp;</th>
			<th><a href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}Name{/tr}</a></th>
			<th><a href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></th>
			<th style="text-align:right;">
				<a href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">
					{tr}Size{/tr}
				</a>
			</th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=user loop=$channels}
			<tr class="{cycle}">
				<td style="text-align:center;">
					<input type="checkbox" name="userfile[{$channels[user].fileId}]" />
				</td>
				<td>{$channels[user].filename|iconify}
					<a class="link" href="tiki-download_userfile.php?fileId={$channels[user].fileId}">
						{$channels[user].filename}
					</a>
				</td>
				<td>{$channels[user].created|tiki_short_datetime}</td>
				<td style="text-align:right;">{$channels[user].filesize|kbsize}</td>
			</tr>
		{sectionelse}
			{norecords _colspan=4}
		{/section}
	</table>
	{if $channels|@count ge '1'}
		{tr}Perform action with checked:{/tr} <input type="submit" name="delete" value="{tr}Delete{/tr}" />
	{/if}
</form>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

<h2>{tr}Upload file{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-userfiles.php" method="post">
	<table class="formcolor">
		<tr>
			<td>{tr}Upload file:{/tr}</td>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="60" name="userfile1" type="file" /><br />
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="60" name="userfile2" type="file" /><br />
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="60" name="userfile3" type="file" /><br />
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="60" name="userfile4" type="file" /><br />
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="60" name="userfile5" type="file" /><br />
				<input style="font-size:9px;" type="submit" name="upload" value="{tr}Upload{/tr}" />
			</td>
		</tr>
	</table>
</form>
