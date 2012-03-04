{* $Id$ *}
	{capture name=actions}{strip}
		<div class='opaque'>
			<div class='box-title'><strong>{tr}Actions{/tr}</strong></div>
      			<div class='box-data'>
          			{if $tiki_p_view_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="{$item->downloadUrl}" title="{tr}Download{/tr}" class="iconmenu"><img alt="" src="pics/icons/application_put.png" class="icon" />{tr}Download{/tr}</a>
          			{/if}
           			{if $tiki_p_edit_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=edit" class="iconmenu"><img alt="" src="pics/icons/page_edit.png" class="icon" />{tr}Change Details{/tr}</a>
          			{/if}
          			{if $tiki_p_remix_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=remix" class="iconmenu"><img alt="" src="pics/icons/layers.png" class="icon" />{tr}Remix Video{/tr}</a>
           				{if $entryType eq "mix"}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=dupl" class="iconmenu"><img alt="" src="pics/icons/layers.png" class="icon" />{tr}Duplicate{/tr}</a>
					{/if}
          			{/if}
          			{if $tiki_p_delete_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=delete" class ="iconmenu" ><img alt="" src="pics/icons/cross.png" class="icon"/>{tr}Delete{/tr}</a>
           			{/if}
            		</div>
		</div>
	{/strip}{/capture}
