{* $Id $ *}
	{capture name=actions}{strip}
		<div class='opaque'>
			<div class='box-title'><b>{tr}Actions{/tr}</b></div>
      			<div class='box-data'>
          			{if $tiki_p_view_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}" class="iconmenu"><img src="pics/icons/application_form_magnify.png" class="icon" />View</a>
          			{/if}
           			{if $tiki_p_edit_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=edit" class="iconmenu"><img src="pics/icons/page_edit.png" class="icon" />Change Details</a>
          			{/if}
          			{if $tiki_p_remix_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=remix" class="iconmenu"><img src="pics/icons/layers.png" class="icon" />Remix Video</a>
           				{if $entryType eq "mix"}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=dupl" class="iconmenu"><img src="pics/icons/layers.png" class="icon" />Duplicate</a>
					{/if}
          			{/if}
          			{if $tiki_p_delete_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=delete" class ="iconmenu" ><img src="pics/icons/cross.png" class="icon"/>Delete</a>
           			{/if}          			
            		</div>
		</div>
	{/strip}{/capture}
