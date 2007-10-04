{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/modules/mod-last_images_th.tpl,v 1.5 2007-10-04 22:17:50 nyloth Exp $ *}

{if $prefs.feature_galleries eq 'y'}
  {tikimodule title="{tr}Last Images{/tr}" name="last_images_th" flip=$module_params.flip decorations=$module_params.decorations}
  {section name=ix loop=$modLastImages}
    {if $smarty.section.ix.index < $module_rows}
       <div style="text-align:center">
       <a class="linkmodule" href="tiki-browse_image.php?imageId={$modLastImages[ix].imageId}">
       {* doing regex to prevent xss *}
       <img src="show_image.php?id={$modLastImages[ix].imageId}&amp;thumb=1" title="{$modLastImages[ix].name|regex_replace:"/\"/":"'"}" alt="{$modLastImages[ix].description|regex_replace:"/\"/":"'"}" />
       </a>
       </div>
    {/if}
  {/section}
  {/tikimodule}
{/if}
