{* $Id$ *}

{if $prefs.feature_file_galleries eq 'y'}
	{if !isset($tpl_module_title)}
		{if isset($module_rows) && $module_rows gt 0 }
			{eval var="{tr}Last `$module_rows` Podcasts{/tr}" assign="tpl_module_title"}
		{else}
			{eval var="{tr}Newest Podcasts{/tr}" assign="tpl_module_title"}
		{/if}
	{/if}
	{tikimodule error=$module_params.error title=$tpl_module_title name="last_podcasts" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}

		{if $nonums != 'y'}<ol>{else}<ul>{/if}
			{section name=ix loop=$modLastFiles}
			<li>
				<div class="module">
					<a class="linkmodule" href="tiki-download_file.php?fileId={$modLastFiles[ix].fileId}" {if $verbose eq 'n'}title="{$modLastFiles[ix].description|escape:'html'}"{/if} onclick="return false;">
						{$modLastFiles[ix].name|escape:'html'}
					</a>
				</div>
				<div class="module podcast">
					{if $mediaplayer ne ""}
						{if $modLastFiles[ix].path ne ""}
							<object type="application/x-shockwave-flash" data="{$mediaplayer}?mp3={$prefs.fgal_podcast_dir}/{$modLastFiles[ix].path}" width="190" height="20">
								<param name="movie" value="{$mediaplayer}?mp3=files/{$modLastFiles[ix].path}" />
							</object>
							{if $verbose eq 'y'}
								<div class="moduledescription" >
									{$modLastFiles[ix].description|nl2br}
								</div>
							{/if}
						{else} {* This probably means it is not an mp3, or it was not uploaded into a file gallery of type "Podcast (Audio)" *}
							{tr}This is likely not a podcast file.{/tr}
						{/if}
					{else}
						{tr}The path to a podcast player is required.{/tr}
					{/if}
				</div>
	 		</li>
			{/section}
		{if $nonums != 'y'}</ol>{else}</ul>{/if}

		{if $link_url neq "" }
			<div class="lastlinkmodule" >
				<a class="linkmodule" href="{$link_url}" >{if $link_text neq ""}{tr}{$link_text}{/tr}{else}{$link_url}{/if}</a>
			</div>
		{/if}

	{/tikimodule}
{/if}
