{* $Id$ *}
{title help="Kaltura" admpage="video"}{if $entryType eq "mix"}{tr}Remix Entries{/tr}{else}{if $view ne "browse"}{tr}List Media{/tr}{else}{tr}Browse Media{/tr}{/if}{/if}{/title}

<div class="row form-group">
	<form method="post" class="col-md-12 form-inline form-horizontal" role="form">
		<label class="control-label col-sm-2" for="find">{tr}Find{/tr}</label>
		<div class="input-group col-sm-8">
			<input type="text" name="find" class="form-control" id="find" value="{$find|escape}">
			<input type="hidden" name="list" value="{$entryType}">
		</div>
		<div class="col-sm-2">
			<input type="submit" class="btn btn-default btn-sm" name="search" value="{tr}Go{/tr}">
		</div>
	</form>
</div>

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

