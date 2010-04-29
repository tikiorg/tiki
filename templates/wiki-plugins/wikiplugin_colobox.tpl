{strip}
{foreach from=$colorboxFiles.data item=file name=files}
	{capture name=url}{$colorboxUrl}{$file.$colorboxColumn}{/capture}
	<a href="{$smarty.capture.url}" rel="shadowbox[colorbox{$iColorbox}];type=img" title="{if $params.showtitle eq 'y' && !empty($file.name)}{$file.name|escape}{if !empty($file.description) or ($params.showfilename eq 'y' and !empty($file.filename))}<br />{/if}{/if}{if $params.showfilename eq 'y' and !empty($file.filename)}{$file.filename|escape}{if !empty($file.description)}<br />{/if}{/if}{$file.description}">
	{if $smarty.foreach.files.first}
		<img border="0" src="{$smarty.capture.url}{if !empty($colorboxThumb)}&{$colorboxThumb}{/if}" />
	{/if}
	</a>
{/foreach}
{/strip}