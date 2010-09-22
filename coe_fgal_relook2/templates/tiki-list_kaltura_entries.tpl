{title help="Kaltura" admpage="video"}{if $entryType eq "mix"}{tr}Kaltura Remix Entries{/tr}{else}{tr}Kaltura Media Entries{/tr}{/if}{/title}

{capture name=other_sorts}{strip}
	<div class='opaque'>
		<div class='box-title'><b>{tr}Other Sorts{/tr}</b></div>
		<div class='box-data'>
 			<a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-views'}asc_views{else}desc_views{/if}">{tr}Loads{/tr}</a>
			<br />
 			<a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-plays'}asc_plays{else}desc_plays{/if}">{tr}Plays{/tr}</a>
		</div>
	</div>
{/strip}{/capture}
    
<form method="post" action="{$smarty.server.PHP_SELF}" class="findtable">
	<label class="findtitle">
	{tr}Find{/tr}
		<input type="text" name="find" value="{$find|escape}" />
	</label>
	<input type="hidden" name="list" value="{$entryType}" />
	<label class="findsubmit">
		<input type="submit" name="search" value="{tr}Go{/tr}" />
	</label>
</form>

{if $entryType eq "mix"}

	{if $view ne "browse"}
<form action='tiki-list_kaltura_entries.php?list=mix' method="post">	
	{button _text="{tr}Media Entries{/tr}" href="tiki-list_kaltura_entries.php?list=media" }
	{button _text="{tr}Browse{/tr}" href="tiki-list_kaltura_entries.php?list=mix&amp;view=browse" } 
		{if $tiki_p_delete_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}<input type="submit" name="action" value="{tr}Delete{/tr}" />{/if}

		{include file=tiki-list_kaltura_mix_entries.tpl}
</form>
    {else}
	{include file="tiki-list_kaltura_browse_entries.tpl"}
    {/if}
{else}
	{if $view ne "browse"}
<form action="tiki-list_kaltura_entries.php?list=media" method="post" class="normal" id="videoAction">
	{button _text="{tr}Mix Entries{/tr}" href="tiki-list_kaltura_entries.php?list=mix" }
	{button _text="{tr}Browse{/tr}" href="tiki-list_kaltura_entries.php?list=media&amp;view=browse" }
	{if $tiki_p_remix_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}<input type="submit" name="action" value="{tr}Create Remix{/tr}" />{/if} 
	{if $tiki_p_delete_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}<input type="submit" name="action" value="{tr}Delete{/tr}" />{/if}
	
		{include file=tiki-list_kaltura_media_entries.tpl}
</form>
	{jq}
$("#videoAction").submit(function () {
	if ($(this).find("input[name='mediaId[]']:checked").length === 0) {
		alert("{tr}Please select some media entries to use{/tr}");
		return false;
	} else {
		return true;
	}
});
	{/jq}
	{else}
	{include file="tiki-list_kaltura_browse_entries.tpl"}
	{/if}
{/if}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

