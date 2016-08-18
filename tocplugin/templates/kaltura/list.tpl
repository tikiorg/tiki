{* $Id$ *}
{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<div class="kaltura-media-list">
	{foreach $entries as $item}
		<div class="media" data-id="{$item->id}" data-name="{$item->name}">
			<div class="media-left">
				<img class="athumb media-object" src="{$item->thumbnailUrl}" alt="{$item->description}" height="80" width="120">
			</div>
			<div class="media-body">
				<h4 class="media-heading">{$item->name}</h4>
			</div>
		</div>
	{/foreach}
</div>

{jq}
$(".media", ".kaltura-media-list").click(function () {
	var hidden = $('<input type="hidden">')
		.attr('name', '{{$targetName}}')
		.attr('value', $(this).data('id'))
		;
	$('#{{$formId}}').append(hidden);

	$("a[data-target-name='{{$targetName}}']").parent().find("ol").append($('<li>')
		.text($(this).data('name')));

	$(this).parents(".ui-dialog-content").data("ui-dialog").close();
}).css("cursor", "pointer");
{/jq}
{/block}
