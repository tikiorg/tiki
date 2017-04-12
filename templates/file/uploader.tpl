{extends "layout_view.tpl"}

{block name="title"}
	{if $uploadInModal}{title}{$title}{/title}{/if}
{/block}

{block name="content"}
	{if $uploadInModal}

		<form class="file-uploader" enctype="multipart/form-data" method="post" action="{service controller=file action=upload galleryId=$galleryId image_max_size_x=$image_max_size_x  image_max_size_y=$image_max_size_y}" data-gallery-id="{$galleryId|escape}" data-image_max_size_x="{$image_max_size_x|escape}" data-image_max_size_y="{$image_max_size_y|escape}"}" data-gallery-id="{$galleryId|escape}">
			<div class="progress hidden">
				<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
					<span class="sr-only"><span class="count">0</span>% Complete</span>
				</div>
			</div>
               {if $image_max_size_x || $image_max_size_y }{remarksbox type="note" title="{tr}Note{/tr}"}{tr}Images will be resized to {if $image_max_size_x} {$image_max_size_x}px in width{/if}{if $image_max_size_y}  and {$image_max_size_y}px in height{/if} {/tr}{/remarksbox}{else}{remarksbox type="note" title="{tr}Note{/tr}"}{tr}Images will not be resized, for resizing edit this tracker field and set image max width and height in "Options for files" section.{/tr}{/remarksbox}{/if}
			<input type="file" name="file[]" {if $limit gt 1}multiple{/if} {if $typeFilter}accept="{$typeFilter|escape}"{/if} />
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

	{else}

			<div class="file-uploader inline" data-action="{service controller=file action=upload galleryId=$galleryId image_max_size_x=$image_max_size_x  image_max_size_y=$image_max_size_y}" data-gallery-id="{$galleryId|escape}" data-image_max_size_x="{$image_max_size_x|escape}" data-image_max_size_y="{$image_max_size_y|escape}">
			<div class="progress hidden">
				<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
					<span class="sr-only"><span class="count">0</span>% Complete</span>
				</div>
			</div>
             {if $image_max_size_x || $image_max_size_y }{remarksbox type="note" title="{tr}Note{/tr}"}{tr}Images will be resized to {if $image_max_size_x} {$image_max_size_x}px in width{/if}{if $image_max_size_y}  and {$image_max_size_y}px in height{/if} {/tr}{/remarksbox}{else}{remarksbox type="note" title="{tr}Note{/tr}"}{tr}Images will not be resized, for resizing edit this tracker field and set image max width and height in "Options for files" section.{/tr}{/remarksbox}{/if}
			<input type="file" name="file[]" {if $limit gt 1}multiple{/if} {if $typeFilter}accept="{$typeFilter|escape}"{/if} />
			<p class="drop-message text-center">
				{tr}Or drop files here from your file manager.{/tr}
			</p>
		</div>

	{/if}
{/block}
