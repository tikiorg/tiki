{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<ul class="media-list">
		{foreach from=$results item=row}
			<li class="media">
				<div class="media-object media-left">
					{if $row.filetype|truncate:6:'' eq 'image/'}
						<img src="{$row.object_id|sefurl:'thumbnail'}"/>
					{else}
						{$row.filename|iconify:$row.filetype}
					{/if}
				</div>
				<div class="media-body">
					<h4 class="media-heading">{object_link type=$row.object_type id=$row.object_id}</h4>
					<div>
						{$row.filename|escape}
					</div>
					<div>
						{$row.description|escape}
					</div>
					<div class="small">
						{tr _0=$row.modification_date|tiki_short_datetime}Last modification: %0{/tr}
					</div>
				</div>
			</li>
		{foreachelse}
			<li>
				{tr}No files found.{/tr}
			</li>
		{/foreach}
	</ul>
	{pagination_links resultset=$results}{service controller=file action=list_gallery galleryId=$galleryId plain=1 search=$search type=$typeFilter}{/pagination_links}
{/block}
