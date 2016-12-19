{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{strip}
	{if $prefs.feature_siteloc eq 'y' and $prefs.feature_breadcrumbs eq 'y'}
		<div id="sitelocbar" class="breadcrumb">
			{if !empty($module_params.label) and not $crumbs_all_hidden}{tr}{$module_params.label|escape:"html"}{/tr} {/if}
			{if $trail}
				{breadcrumbs type="trail" loc="site" crumbs=$trail showLinks=$module_params.showLinks|default:null}
			{else}
				<a title="{tr}{$crumbs[0]->description}{/tr}" href="{$crumbs[0]->url}" accesskey="1">{$crumbs[0]->title}</a>
				{if $structure eq 'y'}
					{section loop=$structure_path name=ix}
						{$prefs.site_crumb_seper|escape:"html"}
						{if $structure_path[ix].pageName ne $page or $structure_path[ix].page_alias ne $page_info.page_alias}
							<a href="tiki-index.php?page_ref_id={$structure_path[ix].page_ref_id}">
						{/if}
						{if $structure_path[ix].page_alias}
							{$structure_path[ix].page_alias}
						{else}
							{$structure_path[ix].pageName}
						{/if}
						{if $structure_path[ix].pageName ne $page or $structure_path[ix].page_alias ne $page_info.page_alias}
							</a>
						{/if}
					{/section}
				{elseif $module_params.showLast eq 'y'}
					{if $page ne ''}{$prefs.site_crumb_seper|escape:"html"} {$page|escape}
					{elseif $title ne ''}{$prefs.site_crumb_seper|escape:"html"} {$title}
					{elseif $thread_info.title ne ''}{$prefs.site_crumb_seper|escape:"html"} {$thread_info.title}
					{elseif $forum_info.name ne ''}{$prefs.site_crumb_seper|escape:"html"} {$forum_info.name}{/if}
				{/if}
			{/if}
		</div>{* bar with location indicator *}
		{if $trail}
			{breadcrumbs type="desc" loc="site" crumbs=$trail}
		{else}
			{breadcrumbs type="desc" loc="site" crumbs=$crumbs}
		{/if}
	{/if}
	{/strip}
{/tikimodule}