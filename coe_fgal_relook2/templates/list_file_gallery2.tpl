{* $Id: list_file_gallery.tpl 26930 2010-05-05 23:16:15Z nyloth $ *}

<iframe src="about:blank" width="1" height="1" frameborder="0" style="visibility:hidden;position:absolute;" name="fgiframe"></iframe>
<form name="fgalformid" id="fgalform" method="post" action="{$smarty.server.PHP_SELF}{if $filegals_manager neq ''}?filegals_manager={$filegals_manager|escape}{/if}" onsubmit="return FileGallery.open(this.action, this.id);">
	<input type="hidden" name="galleryId" value="{$gal_info.galleryId|escape}" />
	<input type="hidden" name="find" value="{$find|escape}" />
	{if $prefs.fgal_asynchronous_indexing eq 'y'}<input type="hidden" name="fast" value="y" />{/if} 
	{if !empty($sort_mode)}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
	{if isset($file_info)}<input type="hidden" name="fileId" value="{$file_info.fileId|escape}" />{/if}
	{if isset($page)}<input type="hidden" name="page" value="{$page|escape}" />{/if}
	{if isset($view)}<input type="hidden" name="view" value="{$view|escape}" />{/if}
	{assign var=nbCols value=0}
	{assign var=other_columns value=''}
	{assign var=other_columns_selected value=''}
	

	<div class="fg-files-list">
		{if $view eq 'browse'}
			{assign var=show_infos value='y'}
			{include file='browse_file_gallery.tpl'}
		{else}
			{assign var=show_infos value='n'}
			{include file='list_file_gallery_content.tpl'}
		{/if}

		<div class="fg-pager">
			<!-- if $maxRecords > 20 and $cant>$maxRecords -->
			{if $cant>$maxRecords}
				<span style="float:left;display:block;padding-right:5px">{tr}page{/tr}</span>
				{pagination_links cant=$cant step=$maxRecords offset=$offset next='n' prev='n' template='tiki-empty.tpl' htmlelement='fg-jquery-dialog'}{/pagination_links}
			{/if}
		</div>
		
	</div>
</form>

{if ( isset($tree) and count($tree) gt 0 && $tiki_p_list_file_galleries != 'n' && $fgal_options.show_explorer.value eq 'y' && $tiki_p_view_fgal_explorer eq 'y' ) or ( $gallery_path neq '' && $fgal_options.show_path.value eq 'y' && $tiki_p_view_fgal_path eq 'y' ) }
<!--div class="fgal_top_bar" style="height:16px; vertical-align:middle">

{if $gallery_path neq '' && $fgal_options.show_path.value eq 'y' && $tiki_p_view_fgal_path eq 'y'}
  <div class="gallerypath" style="vertical-align:middle">&nbsp;&nbsp;{$gallery_path}</div>
{/if}

</div-->
{/if}



{reindex_file_pixel id=$reindex_file_id}<br />
