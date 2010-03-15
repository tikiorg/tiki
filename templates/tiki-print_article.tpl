{* $Id$ *}
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html id="print" xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>
{* Index we display a wiki page here *}

<div id="tiki-clean">
	<div class="articletitle">
		<h2>{$title|escape}</h2>
		<span class="titleb">{tr}By:{/tr} {$authorName|escape} {tr}on:{/tr} {$publishDate|tiki_short_datetime} ({$reads} {tr}Reads{/tr})</span>
		<br />
	</div>
	
	<div class="articleheading">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">
					{if $useImage eq 'y'}
						{if $hasImage eq 'y'}
							<img alt="{tr}Article image{/tr}" src="article_image.php?image_type=article&amp;id={$articleId}"{if $image_x lt 0} width="{$image_x}"{/if}{if $image_y gt 0} height="{$image_y}"{/if} />
						{else}
							<img alt="{tr}Topic image{/tr}" src="article_image.php?image_type=topic&amp;id={$topicId}" />
						{/if}
					{else}
						<img alt="{tr}Topic image{/tr}" src="article_image.php?image_type=topic&amp;id={$topicId}" />
					{/if}
				</td>
				<td valign="top">
					<span class="articleheading">{$parsed_heading}</span>
				</td>
			</tr>
		</table>
	</div>
	
	<div class="articletrailer">
		{if $show_size eq 'y'}
			({$size} {tr}bytes{/tr})
		{/if}
	</div>
	
	<div class="articlebody">
		{if $tiki_p_read_article eq 'y'}
			{$parsed_body}
		{else}
			<div class="error simplebox">
				{tr}Permission denied. You do not have permission to read complete articles.{/tr}
			</div>
		{/if}
	</div>
</div>

{include file='footer.tpl'}
	</body>
</html>
