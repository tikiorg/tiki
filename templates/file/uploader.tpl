{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<form class="file-uploader" enctype="multipart/form-data" method="post" action="{service controller=file action=upload galleryId=$galleryId}" data-gallery-id="{$galleryId|escape}">
		<div class="progress hidden">
			<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
				<span class="sr-only"><span class="count">0</span>% Complete</span>
			</div>
		</div>
		<input type="file" name="file[]" {if $limit gt 0}multiple{/if} />
		<p class="drop-message text-center">
			{tr}Or drop files here from your file manager.{/tr}
		</p>
	</form>
	<form class="file-uploader-result" method="post" action="{service controller=file action=uploader galleryId=$galleryId}">
		<ul class="list-unstyled">
		</ul>

		<div class="submit">
			<input type="submit" class="btn btn-primary" value="{tr}Select{/tr}">
		</div>
	</form>
{/block}
