{if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Random Images{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="random_images" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <center>
  {if $module_params.showlink ne 'n'}<a class="linkmodule" href="tiki-browse_image.php?imageId={$img.imageId}">{/if}
       <img border="0" src="show_image.php?id={$img.imageId}{if empty($module_params.thumb) or $module_params.thumb eq 'y'}&amp;thumb=1{/if}" alt="{tr}Image{/tr}" />
  {if $module_params.showname eq 'y'}<div class="name">{$img.name}</div>{/if}
  {if $module_params.showlink ne 'n'}</a>{/if}
  {if $module_params.showdescription eq 'y'}<div calss="description">{$img.description}</div>{/if}
  </center>
  {/tikimodule}
{/if}
