	{capture name=actions}{strip}
		<div class='opaque'>
			<div class='box-title'><b>{tr}Actions{/tr}</b>
			</div>
      			<div class='box-data'>

          			{if $tiki_p_view_kaltura_entry eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}"><div class ="iconmenu" ><img src="pics/icons/application_form_magnify.png" class="icon" />View</div></a>
          			{/if}
           			{if $tiki_p_edit_kaltura_entry eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=edit"><div class ="iconmenu" ><img src="pics/icons/page_edit.png" class="icon"/>Change Details</div></a>
          			{/if}
          			{if $tiki_p_remix_kaltura_entry eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=remix"><div class ="iconmenu" ><img src="pics/icons/layers.png" class="icon"/>Remix Video</div></a>
           				{if $entryType eq "mix"}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=dupl"><div class ="iconmenu" ><img src="pics/icons/layers.png" class="icon"/>Duplicate</div></a>
						{/if}
          			{/if}
          			{if $tiki_p_delete_videos eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin eq 'y'}
           				 <a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}&action=delete"><div class ="iconmenu" ><img src="pics/icons/cross.png" class="icon"/>Delete</div></a>
           			{/if}
          			
            	</div>
		</div>
	{/strip}{/capture}
