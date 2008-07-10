{* $Id$ *}

{if $prefs.feature_directory eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Sites{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Sites{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="directory_last_sites" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
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
		<a class="linkbut" href="tiki-directory_browse.php{if $module_params.categoryId}?parent={$module_params.categoryId}{/if}">{tr}More...{/tr}</a>
	</div>
{/if}
{/tikimodule}
{/if}
