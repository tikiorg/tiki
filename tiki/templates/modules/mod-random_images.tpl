{if $feature_galleries eq 'y'}
  {tikimodule title="{tr}Random Images{/tr}" name="random_images" flip=$module_params.flip decorations=$module_params.decorations}
  <center>
  <a class="linkmodule" href="tiki-browse_image.php?imageId={$modRandomImages}">
       <img border="0" src="show_image.php?id={$modRandomImages}&amp;thumb=1" alt="{tr}image{/tr}" />
       </a>
  </center>
  {/tikimodule}
{/if}
