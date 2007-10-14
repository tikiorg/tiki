{if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Random Images{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="random_images" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <center>
  <a class="linkmodule" href="tiki-browse_image.php?imageId={$modRandomImages}">
       <img border="0" src="show_image.php?id={$modRandomImages}&amp;thumb=1" alt="{tr}Image{/tr}" />
       </a>
  </center>
  {/tikimodule}
{/if}
