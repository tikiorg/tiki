{asssign var=colcnt value='0'}
<table border="0" cellspacing="0" cellpadding="0" class="fg-gallery-view">
<tr>
{section name=changes loop=$files}
	{if $colcnt eq '3'}
		</tr>
		<tr>
		{assign var=colcnt value='0'}
	{/if}
	{assign var=colcnt value=$colcnt+1}
	{if $files[changes].isgal eq 1}
		{assign var=checkname value=$subgal_checkbox_name|default:'subgal'}
	{else}
		{assign var=checkname value=$file_checkbox_name|default:'file'}
 	{/if}
	{if $gal_info.show_checked neq 'n' and $smarty.request.$checkname and in_array($files[changes].id,$smarty.request.$checkname)}
		{assign var=is_checked value='y'}
 	{else}
		{assign var=is_checked value='n'}
	{/if}
<!--href="javascript:if (typeof window.opener.SetMyUrl != 'undefined') window.opener.SetMyUrl('{$filegals_manager|escape}','{$seturl}'); else window.opener.SetUrl('{$tikiroot}{$seturl}'); checkClose();" title="{tr}Click Here to Insert in Wiki Syntax{/tr}"-->
	{capture assign=link}{strip}
		{if $files[changes].isgal eq 1}
			href="tiki-list_file_gallery.php?galleryId={$files[changes].id}{if $filegals_manager neq ''}&amp;filegals_manager={$filegals_manager|escape}{/if}&amp;view=browse"
		{else}
			{if $filegals_manager neq ''}
				{assign var=seturl value=$files[changes].id|sefurl:display}
				{* Note: When using this code inside FCKeditor, SetMyUrl function is not defined and we use FCKeditor SetUrl native function *}
				onclick="javascript:FileGallery.upload.insert('{$files[changes].id}');return false;" title="{tr}Click Here to Insert in Wiki Syntax{/tr}"
			{elseif $tiki_p_download_files eq 'y'}
				{if $gal_info.type eq 'podcast' or $gal_info.type eq 'vidcast'}
					href="{$prefs.fgal_podcast_dir}{$files[changes].path}"
				{else}
					href="{if $prefs.javascript_enabled eq 'y'}{$files[changes].id|sefurl:preview}{else}{$files[changes].id|sefurl:display}{/if}"
				{/if}
			{/if}
		{/if}
	{/strip}{/capture}
	{math equation="x + 6" x=$thumbnail_size assign=thumbnailcontener_size}
	<td id="colid-{$colcnt}">
		<div class="fg-gallery-view-tools">
			<input type="checkbox"/><br/>
			<img src="img/icon-file-tools.gif" border="0"/>
		</div>
		<div class="fg-gallery-view-entry">
			<div class="fg-gallery-view-image" onmouseover="this.className='fg-gallery-view-image fg-hover'" onmouseout="this.className='fg-gallery-view-image'">
				{assign var=key_type value=$files[changes].type|truncate:9:'':true}
				<a {$link}{if $prefs.feature_shadowbox eq 'y' && $filegals_manager eq ''} rel="shadowbox[gallery];type={if $key_type eq 'image/png' or $key_type eq 'image/jpe' or $key_type eq 'image/gif'}img{else}iframe{/if}"{/if}{if $over_infos neq ''} {popup fullhtml="1" text=$over_infos|escape:"javascript"|escape:"html"}{else} title="{if $files[changes].name neq ''}{$files[changes].name|escape}{/if}{if $files[changes].description neq ''} ({$files[changes].description|escape}){/if}"{/if}>
					<img src="{$files[changes].id|sefurl:thumbnail}" alt="" />
				</a>
				<div class="fg-gallery-view-info">
					<h3>Properties</h3>
						{foreach from=$fgal_listing_conf item=item key=propname}
							{assign var=key_name_len value=16}
							{if isset($item.key)}
								{assign var=key_name value=$item.key}
							{else}
								{assign var=key_name value="show_$propname"}
							{/if}
							{if true || isset($gal_info.$key_name) and ( $gal_info.$key_name eq 'y' or $gal_info.$key_name eq 'a' or $gal_info.$key_name eq 'i' or $propname eq 'name' ) }
								{assign var=propval value=$files[changes].$propname|truncate:$key_name_len|escape}
								{if $propname eq 'id' or $propname eq 'name'}
									{if $propname eq 'name' and $propval eq '' and $gal_info.show_name eq 'n'}
										{* show the filename if only name should be displayed but is empty *}
										{assign var=propval value=$files[changes].filename|truncate:$key_name_len}
										{assign var=propval value="<a class='fgalname namealias' $link>$propval</a>"}
									{else}
										{assign var=propval value="<a class='fgalname' $link>$propval</a>"}
									{/if}
								{elseif $propname eq 'created' or $propname eq 'lastmodif'}
									{assign var=propval value=$propval|tiki_short_date}
								{elseif $propname eq 'last_user' or $propname eq 'author' or $propname eq 'creator'}
									{assign var=propval value=$propval|userlink}
								{elseif $propname eq 'size'}
									{assign var=propval value=$propval|kbsize:true}
								{elseif $propname eq 'description' and $gal_info.max_desc gt 0}
									{assign var=propval value=$propval|truncate:$gal_info.max_desc:"...":false}
								{elseif $propname eq 'lockedby' and $propval neq ''}
									{assign var=propval value=$propval|userlink}
								{/if}
								{if $propval neq '' and $propname neq 'name' and $propname neq 'type'}
									{$item.name}: <b>{$propval}</b><br/>
								{/if}
							{/if}
						{/foreach}
				</div>
			</div>
			<div class="fg-gallery-view-name">
				{foreach from=$fgal_listing_conf item=item key=propname}
					{assign var=propval value=$files[changes].$propname|truncate:$key_name_len|escape}
					{if $propname eq 'name'}{$propval}{/if}
				{/foreach}
			</div>
		</div>
	</td>
{/section}
</tr>
</table>

