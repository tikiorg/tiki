{* $Id$ *}
	{capture name=actions}{strip}
		<div class='opaque'>
			<div class='box-title'><strong>{tr}Actions{/tr}</strong></div>
      			<div class='box-data'>
          			{if $tiki_p_view_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}" title="{tr}View{/tr}" class="iconmenu"><img alt="" src="img/icons/magnifier.png" class="icon">{tr}View{/tr}</a>
          			{/if}
          			{if $tiki_p_download_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="{$item->downloadUrl}" title="{tr}Download{/tr}" class="iconmenu"><img alt="" src="img/icons/application_put.png" class="icon">{tr}Download{/tr}</a>
          			{/if}
           			{if $tiki_p_edit_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=edit" class="iconmenu"><img alt="" src="img/icons/page_edit.png" class="icon">{tr}Change Details{/tr}</a>
          			{/if}
          			{if $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'} 			
           				{if $entryType eq "mix"} {*TODO: there must be a way to duplicate media clips as well since mixes are being deprecated in Kaltura*}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=dupl" class="iconmenu"><img alt="" src="img/icons/layers.png" class="icon">{tr}Duplicate{/tr}</a>
						{/if}
          			{/if}
          			{if $tiki_p_delete_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=delete" class ="iconmenu" ><img alt="" src="img/icons/cross.png" class="icon">{tr}Delete{/tr}</a>
           			{/if}
            		</div>
		</div>
	{/strip}{/capture}
