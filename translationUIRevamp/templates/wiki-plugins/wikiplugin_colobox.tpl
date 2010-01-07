{strip}
{foreach from=$colorboxFiles.data item=file name=files}
	{capture name=url}{$colorboxUrl}{$file.$colorboxColumn}{/capture}
	<a href="{$smarty.capture.url}" rel="shadowbox[colorbox{$iColorbox}];type=img" title="{$file.description}">
	{if $smarty.foreach.files.first}
		<img border="0" src="{$smarty.capture.url}{if !empty($colorboxThumb)}&{$colorboxThumb}{/if}" />
	{/if}
	</a>
{/foreach}
{/strip}