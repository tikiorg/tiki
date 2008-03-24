{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-last_images_th.tpl,v 1.9 2007/10/14 17:51:01 mose *}

{if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Last Images{/tr}"}{/if}
  {tikimodule title="{tr}Last Images{/tr}" name="last_images_th" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  {if $nonums != 'y'}<ol>{else}<ul>{/if}
  {section name=ix loop=$modLastImages}
    {if $smarty.section.ix.index < $module_rows}
       <li>
		<span class="module">
       <a class="linkmodule" href="tiki-browse_image.php?imageId={$modLastImages[ix].imageId}">
       {* doing regex to prevent xss *}
       <img src="show_image.php?id={$modLastImages[ix].imageId}&amp;thumb=1" title="{$modLastImages[ix].name|regex_replace:"/\"/":"'"}" alt="{$modLastImages[ix].description|regex_replace:"/\"/":"'"}" />
       </a>
		</span>
{if isset($quicktags)}
<span class="module">
<a class="linkmodule" href="javascript:insertAt('editwiki','{literal}{{/literal}img src=show_image.php?id={$modLastImages[ix].imageId}{literal}}{/literal}');">{tr}insert original{/tr}</a>
::<a class="linkmodule" href="javascript:insertAt('editwiki','{literal}{{/literal}img src=show_image.php?id={$modLastImages[ix].imageId}&amp;thumb=1{literal}}{/literal}');">{tr}insert thumbnail{/tr}</a></td></tr>
</span>
{/if}
       </li>
    {/if}
  {/section}
 {if $nonums != 'y'}</ol>{else}</ul>{/if}
  {/tikimodule}
{/if}
