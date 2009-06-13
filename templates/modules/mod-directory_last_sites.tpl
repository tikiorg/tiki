{* $Id$ *}

{if $prefs.feature_directory eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Sites{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Sites{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="directory_last_sites" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $module_params.nonums != 'y'}<ol>{else}<ul>{/if}
  {section name=ix loop=$modLastdirSites}
     <li>{if $absurl == 'y'}
          <a class="linkmodule" href="{$base_url}tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $prefs.directory_open_links eq 'n'}target="_new"{/if}>
          {$modLastdirSites[ix].name}
          </a>
		  {else}
        <a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $prefs.directory_open_links eq 'n'}target="_new"{/if}>
          {$modLastdirSites[ix].name}
        </a>
		{/if}
      </li>
  {/section}
 {if $module_params.nonums != 'y'}</ol>{else}</ul>{/if}
{if $module_params.more eq 'y'}
	<div class="more">
		{if $module_params.categoryId}
			{assign var='thisparent' value='?parent='|cat:$module_params.categoryId}
		{/if}
		{button href="tiki-directory_browse.php$thisparent" _text="{tr}More...{/tr}"}
	</div>
{/if}
{/tikimodule}
{/if}
