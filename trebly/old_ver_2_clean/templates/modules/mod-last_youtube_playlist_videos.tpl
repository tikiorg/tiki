{if !isset($tpl_module_title)}
	{if isset($data.info.title)}
		{capture name=title}{tr}{$data.info.title|escape:"html"}{/tr}{/capture}
		{assign var=tpl_module_title value=$smarty.capture.title}
	{else}
		{assign var=tpl_module_title value="{tr}Videos on YouTube{/tr}"}
	{/if}
{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="youtube" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<div class="youtubemodule" >
		{foreach from=$data.videos item=videoEntry key=videoId}
			<a class="linkmodule" href="http://www.youtube.com/watch?v={$videoId}" {if $verbose eq 'y'}title="{$videoEntry.description|escape:'html'}"{/if} >
				{$videoEntry.title|escape:"html"}
			</a>
			<div class="module video youtube" >{$videoEntry.xhtml}</div>
		{/foreach}
	</div>
	{if $link_url neq "" }
		<div class="lastlinkmodule" >
			<a class="linkmodule" href="{$link_url}" >{if $link_text neq ""}{tr}{$link_text}{/tr}{else}{$link_url}{/if}</a>
		</div>
	{/if}
{/tikimodule}
