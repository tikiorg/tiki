{* $Id$ *}

{if $prefs.feature_directory eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top $module_rows Sites{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Sites{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="directory_top_sites" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
 {if $module_params.nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modTopdirSites}
        <li>
		<a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modTopdirSites[ix].siteId}" {if $directory_open_links eq 'n'}target="_new"{/if}>
            {$modTopdirSites[ix].name}
          </a>
      </li>
    {/section}
 {if $module_params.nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
