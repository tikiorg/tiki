<fieldset class="table">
	<legend>{tr}Wiki attachments{/tr}</legend>
	<form action="tiki-admin.php?page=wikiatt" method="post">
		<input type="hidden" name="ticket" value="{$ticket|escape}">
		<input type="text" name="find" value="{$find|escape}" />
		<input type="submit" class="btn btn-default btn-sm" name="action" value="{tr}Find{/tr}"/>
	</form>


	<div class="table-responsive">
		<table class="table">
			<tr>
				<th>{self_link _sort_arg='sort_mode' _sort_field='user'}{tr}User{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='page'}{tr}Page{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='filename'}{tr}Name{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='filesize'}{tr}Size{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='filetype'}{tr}Type{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='path'}{tr}Storage{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='hits'}{tr}Hits{/tr}{/self_link}</th>
				<th>&nbsp;</th>
			</tr>

			{section name=x loop=$attachements}
				<tr class={cycle}>
					<td>{$attachements[x].user}</td>
					<td><a href="tiki-index.php?page={$attachements[x].page}">{$attachements[x].page}</a></td>
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
				{norecords _colspan=9}
			{/section}
		</table>
	</div>

	{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

	<table>
		<tr>
			<td>
				<form action="tiki-admin.php?page=wikiatt" method="post">
					<input type="hidden" name="ticket" value="{$ticket|escape}">
					<input type="hidden" name="all2db" value="1" />
					<input type="submit" class="btn btn-default btn-sm" name="action" value="{tr}Change all to db{/tr}"/>
				</form>
			</td>
			<td>
				<form action="tiki-admin.php?page=wikiatt" method="post">
					<input type="hidden" name="ticket" value="{$ticket|escape}">
					<input type="hidden" name="all2file" value="1" />
					<input type="submit" class="btn btn-default btn-sm" name="action" value="{tr}Change all to file{/tr}"/>
				</form>
			</td>
		</tr>
	</table>
</fieldset>
