{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-top_images_th.tpl,v 1.16 2007/10/14 17:51:02 mose *}

{if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Top Images{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="top_images_th" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
	{section name=ix loop=$modTopImages}
	{if $smarty.section.ix.index < $modrows}
		<li>
		<a class="linkmodule" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">
       <img alt="image" src="show_image.php?id={$modTopImages[ix].imageId}&amp;thumb=1" />
       </a>
       </li>
    {/if}
  {/section}
  {if $nonums != 'y'}</ol>{else}</ul>{/if}
  {/tikimodule}
{/if}
