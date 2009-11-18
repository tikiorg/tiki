{foreach from=$colorboxFiles.data item=file name=files}
	{capture name=url}{$colorboxUrl}{$file.$colorboxColumn}{/capture}
	<a href="{$smarty.capture.url}" rel="shadowbox[colorbox{$iColorbox}];type=img">
	{if $smarty.foreach.files.first}
		<img border="0" src="{$smarty.capture.url}&{$colorboxThumb}" />
	{/if}
	</a>
{/foreach}