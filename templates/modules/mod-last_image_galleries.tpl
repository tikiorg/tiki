{* $Id$ *}

{if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` galleries{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last galleries{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_image_galleries" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modLastGalleries}
      <li>
          <a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modLastGalleries[ix].galleryId}">
            {$modLastGalleries[ix].name}
          </a>
        </li>
    {/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
