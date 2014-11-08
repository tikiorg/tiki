{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<ul class="media-list">
		{foreach from=$results item=row}
			<li class="media">
				<div class="media-object pull-left">
					{$row.filename|iconify:$row.filetype}
				</div>
				<div class="media-body">
					<h4 class="media-heading">{object_link type=$row.object_type id=$row.object_id}</h4>
					<div>
						{$row.filename|escape}
					</div>
					<div>
						{$row.description|escape}
					</div>
				</div>
			</li>
		{/foreach}
	</ul>
	{pagination_links resultset=$results}{service controller=file action=list_gallery galleryId=$galleryId plain=1}{/pagination_links}
{/block}
