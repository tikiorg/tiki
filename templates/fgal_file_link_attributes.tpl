{strip}
	{if $file.isgal eq 1}
		href="tiki-list_file_gallery.php?galleryId={$file.id}{if !empty($filegals_manager)}&amp;filegals_manager={$filegals_manager|escape}{/if}&amp;view=browse"
	{else}
		{if !empty($filegals_manager)}
			href="#" onclick="window.opener.insertAt('{$filegals_manager}','{$file.wiki_syntax|escape}');checkClose();return false;" title="{tr}Click here to use the file{/tr}"
		{elseif $tiki_p_download_files eq 'y'}
			{if $gal_info.type eq 'podcast' or $gal_info.type eq 'vidcast'}
				href="{$prefs.fgal_podcast_dir}{$file.path}"
			{else}
				href="{if $prefs.javascript_enabled eq 'y' && $file.type|truncate:5:'':true eq 'image'}
								{$file.id|sefurl:preview}
							{elseif $file.type neq 'application/x-shockwave-flash'}
								{$file.id|sefurl:file}
							{else}
								{$file.id|sefurl:display}
							{/if}"
			{/if}
		{/if}
	{/if}
{/strip}