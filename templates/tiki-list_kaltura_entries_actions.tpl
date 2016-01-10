{* $Id$ *}
{capture name=actions}{strip}
	{if $tiki_p_view_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
		<a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}" title="{tr}View{/tr}">
			{icon name="view" _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
		</a>
	{/if}
	{if $tiki_p_download_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
		<a href="{$item->downloadUrl}" title="{tr}Download{/tr}">
			{icon name="download" _menu_text='y' _menu_icon='y' alt="{tr}Download{/tr}"}
		</a>
	{/if}
	{if $tiki_p_edit_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
		<a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=edit">
			{icon name="edit" _menu_text='y' _menu_icon='y' alt="{tr}Change Details{/tr}"}
		</a>
	{/if}
	{if $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
		{if $entryType eq "mix"} {*TODO: there must be a way to duplicate media clips as well since mixes are being deprecated in Kaltura*}
			<a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=dupl">
				{icon name="copy" _menu_text='y' _menu_icon='y' alt="{tr}Duplicate{/tr}"}
			</a>
		{/if}
	{/if}
	{if $tiki_p_delete_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
		<a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=delete" class ="iconmenu" >
			{icon name="delete" _menu_text='y' _menu_icon='y' alt="{tr}Delete{/tr}"}
		</a>
	{/if}
{/strip}{/capture}
