{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<div class="row file-browser">
		<div class="col-md-9 gallery-list">
			{service_inline controller=file action=list_gallery galleryId=$galleryId plain=1}
		</div>
		<div class="col-md-3 selection hidden">
			<form method="post" action="{service controller=file action=browse galleryId=$galleryId}" data-gallery-id="{$galleryId|escape}" data-limit="{$limit|escape}">
				<h4>{tr}Selection{/tr}</h4>
				<ul class="nav nav-pills nav-stacked">
				</ul>
				<div class="help-block">
					{tr}Click to remove{/tr}
				</div>
				<div class="submit">
					<input type="submit" class="btn btn-primary" value="{tr}Select{/tr}">
				</div>
			</form>
		</div>
	</div>
{/block}
