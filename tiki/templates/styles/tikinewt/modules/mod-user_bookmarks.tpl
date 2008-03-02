{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-user_bookmarks.tpl,v 1.21 2007/10/14 17:51:02 mose *}

{if $prefs.feature_user_bookmarks eq 'y' and $user and $tiki_p_create_bookmarks eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="<a href=\"tiki-user_bookmarks.php\">{tr}Bookmarks{/tr}</a>"}{/if}
{tikimodule title=$tpl_module_title name="user_bookmarks" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<ul class="module">
    {section name=ix loop=$modb_folders}
	<li>
        <a href="{$ownurl}{$modb_sep}bookmarks_parent={$modb_folders[ix].folderId}"><img border="0" src="img/icons/folderin.gif" /></a>&nbsp;{$modb_folders[ix].name}
	</li>
    {/section}
</ul>
<ul class="module">
    {section name=ix loop=$modb_urls}
	<li>
	<a class="linkmodule" href="{$modb_urls[ix].url}">{$modb_urls[ix].name}</a>
	{if $tiki_p_cache_bookmarks eq 'y' and $urls[ix].datalen > 0}
	    (<a href="tiki-user_cached_bookmark.php?urlid={$modb_urls[ix].urlId}" class="linkmodule" target="_blank"><small>{tr}cache{/tr}</small></a>)
	{/if}
	(<a class="linkmodule" href="{$ownurl}{$modb_sep}bookmark_removeurl={$modb_urls[ix].urlId}"><small>x</small></a>)
	</li>
    {/section}
</ul>
    <form name="bookmarks" action="{$ownurl}" method="post">
    <input style="font-size: 9px;" type="submit" name="bookmark_mark" value="{tr}mark{/tr}" />
    <input style="font-size: 9px;" type="text" size="8" name="bookmark_urlname" />
    <input style="font-size: 9px;" type="submit" name="bookmark_create_folder" value="{tr}New{/tr}" />
    </form>
    {/tikimodule}
{/if}
