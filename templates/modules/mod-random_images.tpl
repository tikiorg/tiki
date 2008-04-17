{if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Random Images{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="random_images" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <center>
  <a class="linkmodule" href="tiki-browse_image.php?imageId={$modRandomImages}">
       <img border="0" src="show_image.php?id={$modRandomImages}{if empty($module_params.thumb) or $module_params.thumb eq 'y'}&amp;thumb=1{/if}" alt="{tr}Image{/tr}" />
       </a>
  {if $module_params.showdescription eq 'y'}<br />{$modRandomDescription}{/if}
  </center>
  {/tikimodule}
{/if}
