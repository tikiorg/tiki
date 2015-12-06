{if $parentId gt 0 and $view neq 'page' and ($prefs.feature_use_fgal_for_user_files neq 'y' or $tiki_p_admin_file_galleries eq 'y' or $gal_info.type neq 'user')}
	<div style="float:left;width:100%">
		{self_link galleryId=$parentId}
			{icon name="previous"} {tr}Parent Gallery{/tr}
		{/self_link}
	</div>
{/if}
<div id="thumbnails" style="float:left">

	{section name=changes loop=$files}

		{* Checkboxes *}
		{if $files[changes].isgal eq 1}
			{assign var=checkname value=$subgal_checkbox_name|default:'subgal'}
		{else}
			{assign var=checkname value=$file_checkbox_name|default:'file'}
		{/if}
		{if $prefs.fgal_checked neq 'n' and isset($smarty.request.$checkname) and $smarty.request.$checkname
			and in_array($files[changes].id,$smarty.request.$checkname)}
			{assign var=is_checked value='y'}
		{else}
			{assign var=is_checked value='n'}
		{/if}

		{* show files and subgals in browsing view *}
		{if 1}

			{if ( $prefs.use_context_menu_icon eq 'y' or $prefs.use_context_menu_text eq 'y' )
				and (!isset($gal_info.show_action) or $gal_info.show_action neq 'n')}
				{capture name=over_actions}
					{strip}
						{include file='fgal_context_menu.tpl' menu_icon=$prefs.use_context_menu_icon menu_text=$prefs.use_context_menu_text changes=$smarty.section.changes.index}
					{/strip}
				{/capture}
			{/if}

			{assign var=nb_over_infos value=0}
			{if $view neq 'page'}
				{$capturename = 'over_infos'}
			{else}
				{$capturename = 'page_infos'}
			{/if}
			{capture name=$capturename}
				{strip}
					<div {if $view neq 'page'}class='opaque'{/if}>
						<div class='box-title'>
							{if $view neq 'page'}
								{tr}Properties{/tr}
							{/if}
						</div>
						<div class='box-data'>
							<table>
								{foreach item=prop key=propname from=$fgal_listing_conf}
									{if isset($item.key)}
										{assign var=propkey value=$item.key}
									{else}
										{assign var=propkey value="show_$propname"}
									{/if}
									{if isset($files[changes].$propname)}
										{if $propname == 'share' && isset($files[changes].share.data)}
											{$email = []}
											{foreach item=tmp_prop key=tmp_propname from=$files[changes].share.data}
												{$email[]=$tmp_prop.email}
											{/foreach}
											{assign var=propval value=$email|implode:','}
										{else}
											{assign var=propval value=$files[changes].$propname}
										{/if}
									{/if}
									{* Format property values *}
									{if $propname eq 'created' or $propname eq 'lastModif' or $propname eq 'lastDownload'}
										{assign var=propval value=$propval|tiki_long_date}
									{elseif $propname eq 'last_user' or $propname eq 'author' or $propname eq 'creator'}
										{assign var=propval value=$propval|username}
									{elseif $propname eq 'size'}
										{assign var=propval value=$propval|kbsize:true}
									{elseif $propname eq 'description'}
										{assign var=propval value=$propval|nl2br}
									{/if}

									{if isset($gal_info.$propkey)
										and $propval neq ''
										and ($propname neq 'name' or $view eq 'page')
										and ($gal_info.$propkey eq 'a' or $gal_info.$propkey eq 'o'
												or ($view eq 'page' and ($gal_info.$propkey neq 'n' or $propname eq 'name'))
											)
									}
										<tr>
											<td class="pull-right">
												<b>{$fgal_listing_conf.$propname.name}</b>
											</td>
											<td style="padding-left:5px">
												<span class="pull-left">{$propval}</span>
											</td>
										</tr>
										{assign var=nb_over_infos value=$nb_over_infos+1}
									{/if}
								{/foreach}
							</table>
						</div>
					</div>
				{/strip}
			{/capture}

			{if $nb_over_infos gt 0 and !empty($smarty.capture.over_infos)}
				{assign var=over_infos value=$smarty.capture.over_infos}
			{else}
				{assign var=over_infos value=''}
			{/if}

			{* build link *}
			{capture assign=link}
				{strip}
					{if $files[changes].isgal eq 1}
						href="tiki-list_file_gallery.php?galleryId={$files[changes].id}{if !empty($filegals_manager)}&amp;filegals_manager={$filegals_manager|escape}{/if}&amp;view=browse"
					{else}
						{if !empty($filegals_manager)}
							href="#" onclick="window.opener.insertAt('{$filegals_manager}','{$files[changes].wiki_syntax|escape}');checkClose();return false;" title="{tr}Click here to use the file{/tr}"
						{elseif $tiki_p_download_files eq 'y'}
							{if $gal_info.type eq 'podcast' or $gal_info.type eq 'vidcast'}
								href="{$prefs.fgal_podcast_dir}{$files[changes].path}"
							{else}
								href="{if $prefs.javascript_enabled eq 'y' && $files[changes].type|truncate:5:'':true eq 'image'}
												{$files[changes].id|sefurl:preview}
											{elseif $files[changes].type neq 'application/x-shockwave-flash'}
												{$files[changes].id|sefurl:file}
											{else}
												{$files[changes].id|sefurl:display}
											{/if}"
							{/if}
						{/if}
					{/if}
				{/strip}
			{/capture}

			{math equation="x + 6" x=$thumbnail_size assign=thumbnailcontener_size}

		{* thumbnail actions wrench *}
		{capture name="thumbactions"}
			{if ($prefs.fgal_show_thumbactions eq 'y' or $show_details eq 'y')}
					<div class="thumbactions">
				{if $prefs.fgal_checked neq 'n' and $tiki_p_admin_file_galleries eq 'y' and $view neq 'page'}
					<label style="float:left">
						<input type="checkbox" onclick="flip_thumbnail_status('{$checkname}_{$files[changes].id}')" name="{$checkname}[]" value="{$files[changes].id|escape}" {if $is_checked eq 'y'}checked="checked"{/if}>
						{if isset($checkbox_label)}
							{$checkbox_label}
						{/if}
					</label>
				{/if}
				{if !isset($gal_info.show_action) or $gal_info.show_action neq 'n'}
					{if ( $prefs.use_context_menu_icon eq 'y' or $prefs.use_context_menu_text eq 'y' )
					and $prefs.javascript_enabled eq 'y'}
						<a class="fgalname tips" title="{tr}Actions{/tr}" href="#" {popup delay="0|2000" fullhtml="1" text=$smarty.capture.over_actions|escape:"javascript"|escape:"html"}>
							{icon name='wrench' alt="{tr}Actions{/tr}"}
						</a>
						{else}
					{include file='fgal_context_menu.tpl'}
					{/if}
				{/if}
			</div> {* thumbactions *}
			{/if}
		{/capture}
			<div id="{$checkname}_{$files[changes].id}" class="clearfix thumbnailcontener{if $is_checked eq 'y'} thumbnailcontenerchecked{/if}{if $files[changes].isgal eq 1} subgallery{/if}" {if $view eq 'page'}style="float:left"{else}style="width:{$thumbnailcontener_size}px"{/if}>
				<div class="thumbnail" style="float:left; {if $view neq 'page'}width:{$thumbnailcontener_size}px{/if}">
					<div class="thumbnailframe" style="width:100%;height:{if $view != 'page'}{$thumbnailcontener_size}px{else}100%{/if}{if $show_infos neq 'y'};margin-bottom:4px{/if}">
						<div class="thumbimage">
							<div class="thumbimagesub">{assign var=key_type value=$files[changes].type}
								{$imagetypes = 'n'}
								{if $files[changes].isgal eq 1}
									<a {$link}>
										{if empty($files[changes].icon_fileId)}
											{icon name="admin_fgal" size=3}
										{else}
											<img src="{$files[changes].icon_fileId|sefurl:thumbnail}" alt="">
										{/if}
									</a>
								{else}
									{if $key_type eq 'image/png' or $key_type eq 'image/jpeg'
										or $key_type eq 'image/jpg' or $key_type eq 'image/gif'
										or $filetype eq 'image/x-ms-bmp'}
										{$imagetypes = 'y'}
									{/if}
									<a {$link}
										{if $prefs.feature_shadowbox eq 'y' && empty($filegals_manager)}
											{if $imagetypes eq 'y' }
													data-box="box[g]"
											{elseif $key_type eq 'text/html'}
													data-box="shadowbox[gallery];type=iframe"
											{elseif $key_type eq 'application/x-shockwave-flash'}
													data-box="shadowbox[gallery];type=flash"
											{/if}
										{/if}
										{if $over_infos neq ''}
											{popup fullhtml="1" text=$over_infos|escape:"javascript"|escape:"html"}
										{else}
											title="{if $files[changes].name neq ''}{$files[changes].name|escape}{/if}{if $files[changes].description neq ''} - {$files[changes].description|escape}{/if}"
										{/if}>
										{if $key_type neq 'image/svg' and $key_type neq 'image/svg+xml'}
											{if $imagetypes eq 'y' or $prefs.theme_iconset eq 'legacy'}
												{if $view eq 'page'}
													<img src="tiki-download_file.php?fileId={$files[changes].id}&preview" alt="" style="max-width:{$maxWidth}">
												{else}
													<img src="{$files[changes].id|sefurl:thumbnail}" alt="" style="max-height:{$thumbnailcontener_size}px">
												{/if}
											{else}
												{$files[changes].filename|iconify:$key_type:null:3}
											{/if}
										{else}
											<object data="{$files[changes].id|sefurl:thumbnail}" alt="" style="width:{$thumbnail_size}px;height:{$thumbnailcontener_size}px;" type="{$key_type}"></object>
										{/if}
									</a>
								{/if}
							</div>
						</div>
					</div>

					{if $show_infos eq 'y'}
						<div class="thumbinfos">
							{foreach from=$fgal_listing_conf item=item key=propname}
								{assign var=key_name_len value=$prefs.fgal_browse_name_max_length}
								{if isset($item.key)}
									{assign var=key_name value=$item.key}
								{else}
									{assign var=key_name value="show_$propname"}
								{/if}
								{if isset($gal_info.$key_name)
									and ( $gal_info.$key_name eq 'y'
										or $gal_info.$key_name eq 'a'
										or $gal_info.$key_name eq 'i'
										or $propname eq 'name'
									)
								}
									{if isset($files[changes].$propname)}
										{assign var=propval value=$files[changes].$propname|escape}
									{/if}
									{* Format property values *}
									{if $propname eq 'id' or $propname eq 'name'}
										{if $propname eq 'name' and $propval eq '' and $gal_info.show_name eq 'n'}
											{* show the filename if only name should be displayed but is empty *}
											{assign var=propval value=$files[changes].filename|truncate:$key_name_len}
											{assign var=propval value="<a class='fgalname namealias' $link>$propval</a>"}
										{else}
											{assign var=propval value="<a class='fgalname' $link>$propval</a>"}
										{/if}
									{elseif $propname eq 'created' or $propname eq 'lastModif'}
										{assign var=propval value=$propval|tiki_short_date}
									{elseif $propname eq 'last_user' or $propname eq 'author' or $propname eq 'creator'}
										{assign var=propval value=$propval|userlink}
									{elseif $propname eq 'size'}
										{assign var=propval value=$propval|kbsize:true}
									{elseif $propname eq 'description' and $gal_info.max_desc gt 0}
										{assign var=propval value=$propval|truncate:$gal_info.max_desc:"...":false|nl2br}
									{elseif $propname eq 'lockedby' and $propval neq ''}
										{assign var=propval value=$propval|userlink}
									{/if}

									{if $propname eq 'name' and $view neq 'page'}
										<div class="thumbnamecontener">
											<div class="thumbname">
												<div class="thumbnamesub" style="width:{$thumbnail_size}px; overflow: hidden;{if $view eq 'page'}text-align:center{/if}">
													{if $gal_info.show_name eq 'f' or ($gal_info.show_name eq 'a'
														and $files[changes].name eq '')}
														<a class="fgalname" {$link} title="{$files[changes].filename}" {if $view eq 'page'}style="text-align:center"{/if}>
															{$files[changes].filename|truncate:$key_name_len}
														</a>
													{else}
														{$propval}
													{/if}
												</div>
											</div>
										</div>
									{elseif $propval neq '' and $propname neq 'name' and $propname neq 'type' and $view neq 'page'}
										<div class="thumbinfo{if $propname eq 'description'} thumbdescription{/if}"{if $show_details eq 'n' and $propname neq 'description'} style="display:none"{/if}>
											{if $propname neq 'description'}
												<span class="thumbinfoname">
													{$item.name}:
												</span>
											{/if}
											<span class="thumbinfoval"{if $propname neq 'description'} style="white-space: nowrap"{/if}>
												{$propval}
											</span>
										</div>
									{/if}
								{/if}
							{/foreach}
							{if $view eq 'page'}
								{$smarty.capture.thumbactions}
							{/if}
						</div> {* thumbinfos *}
					{/if}
                    {if $view neq 'page'}
                        {$smarty.capture.thumbactions}
                    {/if}
				</div> {* thumbnail *}
				{* property table in page file view *}
				{if $view eq 'page'}
					<div style="float:left">
						{$smarty.capture.page_infos}<br>
					</div>
					<br clear="all">
					<div>
						{include file='tiki-upload_file_progress.tpl' fileId=$files[changes].id name=$files[changes].filename}
					</div>
					{if isset($metarray) and $metarray|count gt 0}
						<br>
						<div class="text-left">
                            {remarksbox type="tip" title="{tr}Metadata{/tr}"}
                                {include file='metadata/meta_view_tabs.tpl'}
                            {/remarksbox}
                        </div>
					{/if}
				{/if}
			</div> {* thumbnailcontener *}
		{/if} {* if 1*}
		{jq}
			adjustThumbnails()
		{/jq}

	{sectionelse}
		<div>
			<b>{tr}No records found{/tr}</b>
		</div>
	{/section}

</div>

<br clear="all" />
{if ($prefs.fgal_checked neq 'n' and $tiki_p_admin_file_galleries eq 'y'
	and ( !isset($show_selectall) or $show_selectall eq 'y') and $view neq 'page' )
			and ($prefs.fgal_show_thumbactions eq 'y' or $show_details eq 'y')}
	{select_all checkbox_names='file[],subgal[]' label="{tr}Select All{/tr}"}
{/if}
