{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-directory_last_sites.tpl,v 1.14 2007/10/14 17:51:00 mose *}

{if $prefs.feature_directory eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Sites{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Sites{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="directory_last_sites" flip=$module_params.flip decorations=$module_params.decorations}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
  {section name=ix loop=$modLastdirSites}
     <li>{if $absurl == 'y'}
          <a class="linkmodule" href="{$feature_server_name}tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $directory_open_links eq 'n'}target="_new"{/if}>
          {$modLastdirSites[ix].name}
          </a>
		  {else}
        <a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $directory_open_links eq 'n'}target="_new"{/if}>
          {$modLastdirSites[ix].name}
        </a>
		{/if}
      </li>
  {/section}
 {if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
