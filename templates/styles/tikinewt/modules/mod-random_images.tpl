{* based on BRANCH 1-10cvs -- center tag changed to div text-align center *}
{if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Random Images{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="random_images" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <div style="text-align:center">
  <a class="linkmodule" href="tiki-browse_image.php?imageId={$modRandomImages}">
       <img style="border:0" src="show_image.php?id={$modRandomImages}&amp;thumb=1" alt="{tr}image{/tr}" />
       </a>
  </div>
  {/tikimodule}
{/if}
