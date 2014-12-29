{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<div class="row">
		{foreach $results as $key => $row}
			{if $key % 3 == 0 && $key > 0}
				</div>
				<div class="row">
			{/if}
			<div class="col-md-4 text-center">
				<div class="panel panel-default">
					<div class="panel-heading">
						{$row.title|escape}
					</div>
					<div class="panel-body">
						<p>
							<a href="{$row.object_id|sefurl:'file'}" data-type="file" data-object="{$row.object_id|escape}"><img src="{$row.object_id|sefurl:'thumbnail'}"/><span class="sr-only">{$row.title|escape}</span></a>
						</p>
						<p class="small">
							{$row.modification_date|tiki_short_datetime}
						</p>
					</div>
				</div>
			</div>
		{foreachelse}
			<div class="col-md-12">
				{tr}No files found.{/tr}
			</div>
		{/foreach}
	</div>
	{pagination_links resultset=$results}{service controller=file action=thumbnail_gallery galleryId=$galleryId plain=1 search=$search type=$typeFilter}{/pagination_links}
{/block}
