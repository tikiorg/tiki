{* $Id$ *}

{if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Last Images{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="last_images_th" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  {section name=ix loop=$modLastImages}
    {if $smarty.section.ix.index < $module_rows}
       <table  cellpadding="0" cellspacing="0">
       <tr>
       <td align="center">
       <a class="linkmodule" href="tiki-browse_image.php?imageId={$modLastImages[ix].imageId}">
       {* doing regex to prevent xss *}
       <img src="show_image.php?id={$modLastImages[ix].imageId}&amp;thumb=1" title="{$modLastImages[ix].name|regex_replace:"/\"/":"'"}" alt="{$modLastImages[ix].description|regex_replace:"/\"/":"'"}" />
       </a>
       </td></tr>
{if isset($quicktags)}
<tr><td><a class="linkmodule" href="javascript:insertAt('editwiki','{literal}{{/literal}img src=show_image.php?id={$modLastImages[ix].imageId}{literal}}{/literal}');">{tr}insert original{/tr}</a>
::<a class="linkmodule" href="javascript:insertAt('editwiki','{literal}{{/literal}img src=show_image.php?id={$modLastImages[ix].imageId}&amp;thumb=1{literal}}{/literal}');">{tr}insert thumbnail{/tr}</a></td></tr>
{/if}
       </table>
    {/if}
  {/section}
  {/tikimodule}
{/if}
