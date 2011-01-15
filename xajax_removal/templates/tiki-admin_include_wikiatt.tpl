<fieldset class="admin">
	<legend>{tr}Wiki attachments{/tr}</legend>
			<form action="tiki-admin.php?page=wikiatt" method="post">
				<input type="text" name="find" value="{$find|escape}" />
				<input type="submit" name="action" value="{tr}Find{/tr}"/>
			</form>

			{cycle values="odd,even" print=false}
			<table class="normal">
				<tr>
					<th>
						<a href="tiki-admin.php?page=wikiatt&amp;sort_mode=user_{if $sort_mode eq 'user'}asc{else}desc{/if}">{tr}User{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=wikiatt&amp;sort_mode=page_{if $sort_mode eq 'page'}asc{else}desc{/if}">{tr}Page{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=wikiatt&amp;sort_mode=filename_{if $sort_mode eq 'filename'}asc{else}desc{/if}">{tr}Name{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=wikiatt&amp;sort_mode=filesize_{if $sort_mode eq 'filesize'}asc{else}desc{/if}">{tr}Size{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=wikiatt&amp;sort_mode=filetype_{if $sort_mode eq 'filetype'}asc{else}desc{/if}">{tr}Type{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=wikiatt&amp;sort_mode=path_{if $sort_mode eq 'path'}asc{else}desc{/if}">{tr}Storage{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=wikiatt&amp;sort_mode=created_{if $sort_mode eq 'created'}asc{else}desc{/if}">{tr}Created{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=wikiatt&amp;sort_mode=hits_{if $sort_mode eq 'hits'}asc{else}desc{/if}">&gt;</a>
					</th>
					<th>&nbsp;</th>
				</tr>

				{section name=x loop=$attachements}
					<tr class={cycle}>
						<td>{$attachements[x].user}</td>
						<td><a href="tiki-index?page={$attachements[x].page}">{$attachements[x].page}</a></td>
						<td>
							<a href="tiki-download_wiki_attachment.php?attId={$attachements[x].attId}">{$attachements[x].filename}</a>
						</td>
						<td>{$attachements[x].filesize|kbsize}</td>
						<td>{$attachements[x].filetype}</td>
						<td>{if $attachements[x].path}file{else}db{/if}</td>
						<td>{$attachements[x].created|tiki_short_date}</td>
						<td>{$attachements[x].hits}</td>
						<td>
							<a href="tiki-admin.php?page=wikiatt&amp;attId={$attachements[x].attId}&amp;action={if $attachements[x].path}move2db{else}move2file{/if}">{tr}Change{/tr}</a>
						</td>
					</tr>
				{sectionelse}
					{norecords _colspan="9"}
				{/section}
			</table>

			{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
		
		<table>
			<tr>
				<td>
					<form action="tiki-admin.php?page=wikiatt" method="post">
						<input type="hidden" name="all2db" value="1" />
						<input type="submit" name="action" value="{tr}Change all to db{/tr}"/>
					</form>
				</td>
				<td>
					<form action="tiki-admin.php?page=wikiatt" method="post">
						<input type="hidden" name="all2file" value="1" />
						<input type="submit" name="action" value="{tr}Change all to file{/tr}"/>
					</form>
				</td>
			</tr>
		</table>
</fieldset>
