{if $feature_galleries eq 'y'}
  {tikimodule title="{tr}Random Images{/tr}" name="random_images" flip=$module_params.flip decorations=$module_params.decorations}
  <a class="linkmodule" href="tiki-browse_image.php?imageId={$modRandomImages}">
       <img src="show_image.php?id={$modRandomImages}&amp;thumb=1" alt="{tr}image{/tr}" />
       </a>
  {/tikimodule}
{/if}
