{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-last_files.tpl,v 1.14 2007/10/14 17:51:01 mose *}

{if $prefs.feature_file_galleries eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Files{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Files{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_files" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
	{section name=ix loop=$modLastFiles}
	<li>
          <a class="linkmodule" href="tiki-download_file.php?fileId={$modLastFiles[ix].fileId}">
            {$modLastFiles[ix].filename}
          </a>
	 </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
