{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/modules/mod-last_images_th.tpl,v 1.3 2006-06-24 14:23:59 amette Exp $ *}

{if $feature_galleries eq 'y'}
  {tikimodule title="{tr}Last Images{/tr}" name="last_images_th" flip=$module_params.flip decorations=$module_params.decorations}
  {section name=ix loop=$modLastImages}
    {if $smarty.section.ix.index < 5}
       <div align="center">
       <a class="linkmodule" href="tiki-browse_image.php?imageId={$modLastImages[ix].imageId}">
       <img src="show_image.php?id={$modLastImages[ix].imageId}&amp;thumb=1" title="{if $modLastImages[ix].description ne ''}{$modLastImages[ix].description}{else}{$modLastImages[ix].name}{/if}" alt="{$smarty.section.ix.index+1} {tr}of{/tr} {tr}Last Images{/tr}" />
       </a>
       </div>
    {/if}
  {/section}
  {/tikimodule}
{/if}
