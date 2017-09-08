{* Id *}
<div class="thumbnailframe" style="width:100%;height:{if $view != 'page'}{$thumbnailcontener_size}px{else}100%{/if}{if $show_infos neq 'y'};margin-bottom:4px{/if}">
	<div class="thumbimage">
		<div class="thumbimagesub">{assign var=key_type value=$file.type}
			{$imagetypes = 'n'}
			{if $file.isgal eq 1}
				<a {$link}>
					{if empty($file.icon_fileId)}
						{icon name="admin_fgal" size=3}
					{else}
						<img src="{$file.icon_fileId|sefurl:thumbnail}">
					{/if}
				</a>
			{else}
				{if in_array($key_type, ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/x-ms-bmp'])}
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
					{capture assign='popupContents'}
						<div class='opaque'>
							<div class='box-title'>
								{tr}Properties{/tr}
							</div>
							<div class='box-data'>
								{include file='file_properties_table.tpl'}
							</div>
						</div>
					{/capture}
					{if $popupContents neq ''}
						{popup fullhtml="1" text=$popupContents}
					{else}
						title="{if $file.name neq ''}{$file.name|escape}{/if}{if $file.description neq ''} - {$file.description|escape}{/if}"
					{/if}>
					{if $key_type neq 'image/svg' and $key_type neq 'image/svg+xml'}
						{if $imagetypes eq 'y' or $prefs.theme_iconset eq 'legacy'}
							{if $view eq 'page'}
								<img src="tiki-download_file.php?fileId={$file.id}&preview" style="width:{$maxWidth};max-width: 100%;">
							{else}
								<img src="{$file.id|sefurl:thumbnail}" style="max-height:{$thumbnailcontener_size}px">
							{/if}
						{else}
							{$file.filename|iconify:$key_type:null:3}
						{/if}
					{else}
						<object data="{$file.id|sefurl:thumbnail}" style="width:{$thumbnail_size}px;height:{$thumbnailcontener_size}px;" type="{$key_type}"></object>
					{/if}
				</a>
			{/if}
		</div>
	</div>
</div>