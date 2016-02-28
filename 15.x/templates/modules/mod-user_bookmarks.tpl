{* $Id$ *}

{if $user and $tiki_p_create_bookmarks eq 'y'}
	{tikimodule error=$module_params.error title=$tpl_module_title name="user_bookmark" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}

		{if !isset($tpl_module_title)}{assign var=tpl_module_title value="<a href=\"tiki-user_bookmarks.php\">{tr}Bookmarks{/tr}</a>"}{/if}

		<ul>
			{section name=ix loop=$modb_folders}
				<li>
						<a href="{$ownurl}{$modb_sep}bookmarks_directory={$modb_folders[ix].folderId}">{icon name="attach"}</a>&nbsp;{$modb_folders[ix].name|escape}
			</li>
				{/section}
		</ul>
		<ul>
			{section name=ix loop=$modb_urls}
				<li>
					<a class="linkmodule" href="{$modb_urls[ix].url}">{$modb_urls[ix].name|escape}</a>
					{if $tiki_p_cache_bookmarks eq 'y' and $modb_urls[ix].datalen > 0}
						(<a href="tiki-user_cached_bookmark.php?urlid={$modb_urls[ix].urlId}" class="linkmodule" target="_blank"><small>{tr}Cache{/tr}</small></a>)
					{/if}
					(<a class="linkmodule" href="{$ownurl}{$modb_sep}bookmark_removeurl={$modb_urls[ix].urlId}"><small>x</small></a>)
				</li>
			{/section}
		</ul>
		<form name="bookmarks" action="{$ownurl}" method="post">
			<input style="font-size: 9px;" type="text" size="8" name="modb_name" />
			<input style="font-size: 9px;" type="submit" class="btn btn-default" name="bookmark_mark" value="{tr}Create Bookmark{/tr}" />
			<input style="font-size: 9px;" type="submit" class="btn btn-default btn-sm" name="bookmark_create_folder" value="{tr}New Folder{/tr}" />
		</form>
	{/tikimodule}
{/if}
