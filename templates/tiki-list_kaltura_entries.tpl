{* $Id$ *}
{title help="Kaltura" admpage="video"}{if $entryType eq "mix"}{tr}Remix Entries{/tr}{else}{if $view ne "browse"}{tr}List Media{/tr}{else}{tr}Browse Media{/tr}{/if}{/if}{/title}

{capture name=other_sorts}{strip}
	<div class='opaque'>
		<div class='box-title'><strong>{tr}Other Sorts{/tr}</strong></div>
		<div class='box-data'>
 			<a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-views'}asc_views{else}desc_views{/if}">{tr}Loads{/tr}</a>
			<br>
 			<a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-plays'}asc_plays{else}desc_plays{/if}">{tr}Plays{/tr}</a>
		</div>
	</div>
{/strip}{/capture}
    
<form method="post" action="{$smarty.server.PHP_SELF}" class="findtable">
	<label class="findtitle">
	{tr}Find{/tr}
		<input type="text" name="find" value="{$find|escape}">
	</label>
	<input type="hidden" name="list" value="{$entryType}">
	<label class="findsubmit">
		<input type="submit" class="btn btn-default" name="search" value="{tr}Go{/tr}">
	</label>
</form>

{if $view ne "browse"}
<form action='tiki-list_kaltura_entries.php?list={if $entryType eq "mix"}mix{else}media{/if}' method="post"{if $entryType ne "mix"} id="videoAction"{/if}>	
	{capture assign=btnlink_list}{if $entryType eq "mix"}media{else}mix{/if}{/capture}
	{if $prefs.kaltura_legacyremix == 'y' || $entryType eq "mix"}
	{capture assign=btnlink_text}{if $entryType eq "mix"}{tr}Browse Media{/tr}{else}{tr}Browse Remixes{/tr}{/if}{/capture}
	{button _text=$btnlink_text href="tiki-list_kaltura_entries.php?list=$btnlink_list"}
	{/if}
	{if $tiki_p_upload_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}{button _text="{tr}Add New Media{/tr}" href="tiki-kaltura_upload.php"}{/if}
	{capture assign=btnlink_text}{if $entryType eq "mix"}{tr}Browse Remixes{/tr}{else}{tr}Browse Media{/tr}{/if}{/capture}
	{button _text=$btnlink_text href="tiki-list_kaltura_entries.php?list=$entryType&amp;view=browse"}
	{if $entryType eq "mix"}{include file='tiki-list_kaltura_mix_entries.tpl'}{else}{include file='tiki-list_kaltura_media_entries.tpl'}{/if}
</form>
{else}
	{include file="tiki-list_kaltura_browse_entries.tpl"}
{/if}
	
{if $entryType ne "mix"}
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
{/if}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

