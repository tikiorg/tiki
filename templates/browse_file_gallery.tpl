{if $parentId gt 0 and ($prefs.feature_use_fgal_for_user_files neq 'y' or $tiki_p_admin_file_galleries eq 'y' or $gal_info.type neq 'user')}
	<div style="float:left;width:100%">
		{self_link galleryId=$parentId}
			{icon name="previous"} {tr}Parent Gallery{/tr}
		{/self_link}
	</div>
{/if}
{jq}
	// Make nice rows of thumbnails even when there is description or long titles
	$('.thumbnailcontener').height(
		Math.max.apply(null, $('.thumbnailcontener').map(function(index, el) { return $(el).height(); }).get())
	);
{/jq}
<div id="thumbnails"{* style="float:left"*}>

	{foreach $files as $file}

		{* Checkboxes *}
		{if $file.isgal eq 1}
			{assign var=checkname value=$subgal_checkbox_name|default:'subgal'}
		{else}
			{assign var=checkname value=$file_checkbox_name|default:'file'}
		{/if}
		{if $prefs.fgal_checked neq 'n' and isset($smarty.request.$checkname) and $smarty.request.$checkname
			and in_array($file.id,$smarty.request.$checkname)}
			{assign var=is_checked value='y'}
		{else}
			{assign var=is_checked value='n'}
		{/if}

		{* show files and subgals in browsing view *}
		{* build link *}
		{capture assign=link}
			{include 'fgal_file_link_attributes.tpl' log_tpl=false}
		{/capture}

		{math equation="x + 6" x=$thumbnail_size assign=thumbnailcontener_size}

		{* thumbnail actions wrench *}
		{capture name="thumbactions"}
			{if ($prefs.fgal_show_thumbactions eq 'y' or $show_details eq 'y')}
					<div class="thumbactions">
				{if $prefs.fgal_checked neq 'n' and $tiki_p_admin_file_galleries eq 'y'}
					<label style="float:left">
						<input type="checkbox" onclick="flip_thumbnail_status('{$checkname}_{$file.id}')" name="{$checkname}[]" value="{$file.id|escape}" {if $is_checked eq 'y'}checked="checked"{/if}>
						{if isset($checkbox_label)}
							{$checkbox_label}
						{/if}
					</label>
				{/if}
				{if !isset($gal_info.show_action) or $gal_info.show_action neq 'n'}
					{if ( $prefs.use_context_menu_icon eq 'y' or $prefs.use_context_menu_text eq 'y' )
					and $prefs.javascript_enabled eq 'y'}
						<a class="fgalname tips" title="{tr}Actions{/tr}" href="#" {popup fullhtml="1" text={include file='fgal_context_menu.tpl' menu_icon=$prefs.use_context_menu_icon menu_text=$prefs.use_context_menu_text changes=$smarty.section.changes.index}}>
							{icon name='wrench' alt="{tr}Actions{/tr}"}
						</a>
						{else}
					{include file='fgal_context_menu.tpl'}
					{/if}
				{/if}
			</div> {* thumbactions *}
			{/if}
		{/capture}
		<div id="{$checkname}_{$file.id}" class="clearfix thumbnailcontener{if $is_checked eq 'y'} thumbnailcontenerchecked{/if}{if $file.isgal eq 1} subgallery{/if}" style="{if $view eq 'browse'}float:left;{/if}width:{$thumbnailcontener_size}px">
			<div class="thumbnail" style="float:left; width:{$thumbnailcontener_size}px">
				{include file='fgal_thumbnailframe.tpl'}
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
								{if isset($file.$propname)}
									{assign var=propval value=$file.$propname|escape}
								{/if}
								{* Format property values *}
								{if $propname eq 'id' or $propname eq 'name'}
									{if $propname eq 'name' and $propval eq '' and $gal_info.show_name eq 'n'}
										{* show the filename if only name should be displayed but is empty *}
										{assign var=propval value=$file.filename|truncate:$key_name_len}
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

								{if $propname eq 'name'}
									<div class="thumbnamecontener">
										<div class="thumbname">
											<div class="thumbnamesub" style="width:{$thumbnail_size}px; overflow: hidden;{if $view eq 'page'}text-align:center{/if}">
												{if $gal_info.show_name eq 'f' or ($gal_info.show_name eq 'a'
													and $file.name eq '')}
													<a class="fgalname" {$link} title="{$file.filename}" {if $view eq 'page'}style="text-align:center"{/if}>
														{$file.filename|truncate:$key_name_len}
													</a>
												{else}
													{$propval}
												{/if}
											</div>
										</div>
									</div>
								{elseif $propval neq '' and $propname neq 'type'}
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
					</div> {* thumbinfos *}
				{/if}
				{$smarty.capture.thumbactions}
			</div> {* thumbnail *}
		</div> {* thumbnailcontener *}
		{jq}
			adjustThumbnails()
		{/jq}

	{foreachelse}
		<div>
			<b>{tr}No records found{/tr}</b>
		</div>
	{/foreach}
</div>

<br clear="all" />
{if ($prefs.fgal_checked neq 'n' and $tiki_p_admin_file_galleries eq 'y'
	and ( !isset($show_selectall) or $show_selectall eq 'y') )
			and ($prefs.fgal_show_thumbactions eq 'y' or $show_details eq 'y')}
	{select_all checkbox_names='file[],subgal[]' label="{tr}Select All{/tr}"}
{/if}
