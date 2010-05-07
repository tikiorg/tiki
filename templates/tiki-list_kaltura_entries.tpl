{title help="Kaltura" admpage="kaltura"}{if $entryType eq "mix"}{tr}Kaltura Remix Entries:{/tr}{else}{tr}Kaltura Media Entries:{/tr}{/if}{/title}

{capture name=other_sorts}{strip}
    <div class='opaque'>
    	<div class='box-title'><b>{tr}Other Sorts{/tr}</b></div>
    		<div class='box-data'>
 			<a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-views'}asc_views{else}desc_views{/if}">{tr}Loads{/tr}</a></th>
			<br />
 			<a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-plays'}asc_plays{else}desc_plays{/if}">{tr}Plays{/tr}</a></th>
		</div>
    </div>
{/strip}{/capture}
    
<form method="post" action="{$smarty.server.PHP_SELF}" class="findtable">
	<label class="findtitle">
	{tr}Find{/tr}
	<input type="text" name="find" value="{$find|escape}" />
	</label>
	<input type="hidden" name="list" value="{$entryType}">
	<label class="findsubmit">
	<input type="submit" name="search" value="{tr}Go{/tr}" />
	</label>
</form>
	
<br/>

{if $entryType eq "mix"}

	{if $view ne "browse"}
	<form action='tiki-list_kaltura_entries.php?list=mix' method="post">	
	{button _text="{tr}Media Entries{/tr}" href="tiki-list_kaltura_entries.php?list=media" }
	{button _text="{tr}Browse Entries{/tr}" href="tiki-list_kaltura_entries.php?list=mix&view=browse" } 
	<input type="submit" name="action" value="Delete"/>
	<br><br>
    {include file=tiki-list_kaltura_mix_entries.tpl}
    </form>
    {else}
    {include file="tiki-list_kaltura_browse_entries.tpl"}
    {/if}
{else}
	{if $view ne "browse"}
	<form action="tiki-list_kaltura_entries.php?list=media" method="post" class="normal">
	{button _text="{tr}Mix Entries{/tr}" href="tiki-list_kaltura_entries.php?list=mix" }
	{button _text="{tr}Browse Entries{/tr}" href="tiki-list_kaltura_entries.php?list=media&view=browse" }
	<input type="submit" name="action" value="Create Remix"/> 
	<input type="submit" name="action" value="Delete"/>
	<br><br>
	{include file=tiki-list_kaltura_media_entries.tpl}
	</form>
	{else}
	{include file="tiki-list_kaltura_browse_entries.tpl"}
	{/if}
{/if}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

