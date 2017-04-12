{strip}
{foreach from=$colorboxFiles.data item=file name=files}
	{capture name=url}{$colorboxUrl}{$file.$colorboxColumn}{/capture}
	<a class="tips" href="{$smarty.capture.url}{if $colorboxColumn eq "id"}&display{/if}" data-box="shadowbox[colorbox{$iColorbox}];type=img" title=":{$file.elTitle|escape}">
	{if $smarty.foreach.files.first or $params.showallthumbs eq 'y'}
		<img src="{$smarty.capture.url}{if !empty($colorboxThumb)}&{$colorboxThumb}{/if}">
	{/if}
	</a>
{/foreach}
{/strip}
