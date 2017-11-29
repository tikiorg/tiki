{* $Id$ *}
<h2 class="panel-title">Occurences of string in database</h2>
<input type="text" id="string_in_db_search" name="string_in_db_search" size="60" value="{$searchStringAgain|escape}" /> <input type="submit" class="btn btn-default btn-sm" value="Search" onClick="document.getElementById('redirect').value='0';"/>
<input type="hidden" id="redirect" name="redirect" value="1">

<hr/>
{if isset($errorMsg)}
	<span id="error">{$errorMsg}</span>
{else}
	{if isset($searchString)}
		{remarksbox}{tr}Results for {/tr}<b>{$searchString|escape}</b> {tr}in all tables:{/tr}{/remarksbox}
		<p>

		<input type="hidden" name="query" value="{$searchString}">
		<input type="hidden" id="table" name="table" value="">
		<input type="hidden" id="column" name="column" value="">

		<table class="string_in_db_search table normal">
		<tr>
		<th>{tr}Table{/tr}</th>
		<th>{tr}Column{/tr}</th>
		<th>{tr}Occurrences{/tr}</th>
		</tr>
		{$last = ''}
		{foreach from=$searchResult item=res}
			{$table = $res['table']}
			<tr>
			{if $last eq '' || $last neq $table}
				{$span = $tableCount["$table"]}
				<td rowspan="{$span}">{$table|escape}</td>
			{/if}
			<td><input type="submit" class="btn btn-link" value="{$res['column']|escape}" title="{tr}View occurrences{/tr}" onClick="document.getElementById('table').value='{$res['table']}'; document.getElementById('column').value='{$res['column']}'; document.getElementById('redirect').value='0'; document.getElementById('string_in_db_search').value='';"></td>
			<td>{$res['occurrences']|escape}</td>
			</tr>
			{$last = $table}
		{/foreach}
		</table>
		</p>
	{/if}

	{if isset($tableHeaders)}
	{remarksbox}{tr}Results for {/tr}<b>{$searchStringAgain|escape}</b> {tr}in table {/tr} <b>{$tableName}</b>, {tr}column{/tr} <b>{$columnName}</b>:{/remarksbox}
	<table class="table">
		<tr>
		{foreach from=$tableHeaders item=hdr}
			<th>{if $hdr eq $columnName}<em>{$hdr}</em>{else}{$hdr}{/if}</th>
		{/foreach}
		</tr>

		{foreach from=$tableData item=row}
			<tr>
			{foreach from=$row key=column item=val}
				{$value = $val|truncate:30|escape}
				{if $tableName=='tiki_pages' && ($column=='pageName' || $column=='pageSlug' || $column=='data' || $column=='description') && $val}
					<td><a href=tiki-index.php?page={$row['pageName']|escape}  title="{tr}View page{/tr}" target="_blank">{$value}</a></td>
					<!-- TODO:<td>{object_link type='wiki page' id={$row['pageName']|escape} class="link tips" title="{$val|escape}:{tr}View page{/tr}"}</td> -->
				{elseif $tableName=='tiki_blog_posts' && ($column=='data' || $column=='title')}
					<td><a href=tiki-view_blog_post.php?postId={$row['postId']} class="link tips" title="{$row['title']|escape}:{tr}View blog post{/tr}" target="_blank">{$value}</a></td>
				{elseif $tableName=='tiki_files' && ($column=='name' || $column=='description' || $column=='filename')}
					<td><a href=tiki-download_file.php?fileId={$row['fileId']}&display class="link tips" title="{$row['name']|escape}:{tr}View file{/tr}" target="_blank">{$value}</a></td>
				{elseif $tableName=='tiki_file_galleries' && $column=='name'}
					<td><a href=tiki-list_file_gallery.php?galleryId={$row['galleryId']} class="link tips" title="{$val|escape}:{tr}View gallery{/tr}" target="_blank">{$value}</a></td>
				{elseif $tableName=='tiki_categories' && ($column=='name'|| $column=='description')}
					<td><a href=tiki-admin_categories.php?parentId={$row['parentId']}&categId={$row['categId']} class="link tips" title="{$row['name']|escape}:{tr}View category{/tr}" target="_blank">{$value}</a></td>
				{elseif $tableName=='tiki_articles' && ($column=='title'|| $column=='heading')}
					<td><a href=tiki-read_article.php?articleId={$row['articleId']} class="link tips" title="{$row['title']|escape}:{tr}View article{/tr}" target="_blank">{$value}</a></td>
				{elseif $tableName=='tiki_forums' && ($column=='name'|| $column=='description')}
					<td><a href=tiki-view_forum.php?forumId={$row['forumId']} class="link tips" title="{$row['name']|escape}:{tr}View forum{/tr}" target="_blank">{$value}</a></td>
				{elseif $tableName=='tiki_calendars' && ($column=='name'|| $column=='description')}
					<td><a href=tiki-calendar.php?calIds[]={$row['calendarId']} class="link tips" title="{$row['name']|escape}:{tr}View calendar{/tr}" target="_blank">{$value}</a></td>
				{elseif $tableName=='tiki_calendar_items' && ($column=='name'|| $column=='description')}
					<td><a href=tiki-calendar_edit_item.php?viewcalitemId={$row['calitemId']} class="link tips" title="{$row['name']|escape}:{tr}View calendar item{/tr}" target="_blank">{$value}</a></td>
				{elseif $tableName=='tiki_trackers' && ($column=='name'|| $column=='description')}
					<td><a href=tiki-view_tracker.php?trackerId={$row['trackerId']} class="link tips" title="{$row['name']|escape}:{tr}View tracker{/tr}" target="_blank">{$value}</a></td>
				{elseif $tableName=='tiki_tracker_item_fields' && $column=='value'}
					<td><a href=tiki-view_tracker_item.php?itemId={$row['itemId']} class="link tips" title="{$row['value']|escape}:{tr}View tracker item{/tr}" target="_blank">{$value}</a></td>
				{elseif $tableName=='tiki_comments'}
					{if $row['objectType']=='blog post'}
						{if ($column=='objectType' || $column=='data')}
							<td><a href=tiki-view_blog_post.php?postId={$row['object']} class="link tips" title="{$row['data']|escape}:{tr}View blog post{/tr}" target="_blank">{$value}</a></td>
						{else}
							<td>{$value}</td>
						{/if}
					{elseif $row['objectType']=='forum'}
						{if ($column=='objectType' || $column=='data' || $column=='title')}
							{if $row['parentId']==0}
								<td><a href=tiki-view_forum_thread.php?forumId={$row['object']}&comments_parentId={$row['threadId']}#threadId{$row['threadId']} class="link tips" title="{$row['title']|escape:'htmlall'}:{tr}View forum comment{/tr}" target="_blank">{$value}</a></td>
							{else}
								<td><a href=tiki-view_forum_thread.php?comments_parentId={$row['parentId']}#threadId{$row['threadId']} class="link tips" title="{$row['title']|escape:'htmlall'}:{tr}View forum comment{/tr}" target="_blank">{$value}</a></td>
							{/if}
						{else}
							<td>{$value}</td>
						{/if}
					{elseif $row['objectType']=='article'}
						{if ($column=='objectType' || $column=='data')}
							<td><a href=tiki-read_article.php?articleId={$row['object']} class="link tips" title="{$row['data']|escape}:{tr}View article{/tr}" target="_blank">{$value}</a></td>
						{else}
							<td>{$value}</td>
						{/if}
					{elseif $row['objectType']=='wiki page'}
						{if ($column=='objectType' || $column=='data' || $column=='object')}
							<td><a href="tiki-index.php?page={$row['object']|escape}&threadId={$row['threadId']}&comzone=show#threadId{$row['threadId']}" class="link tips" title="{$row['data']|escape}:{tr}View page{/tr}" target="_blank">{$value}</a></td>
						{else}
							<td>{$value}</td>
						{/if}
					{else}
						<td>{$value}</td>
					{/if}
				{else}
					<td>{$value}</td>
				{/if}
			{/foreach}
			</tr>
		{/foreach}
	</table>
	{/if}
{/if}
