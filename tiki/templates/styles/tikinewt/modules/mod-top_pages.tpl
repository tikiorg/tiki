{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-top_pages.tpl,v 1.16.2.1 2007/10/22 07:29:56 mose *}

{if $prefs.feature_wiki eq 'y'}
{if !isset($tpl_module_title)}
   {if $nonums eq 'y'}
   {eval var="{tr}Top `$module_rows` Pages{/tr}" assign="tpl_module_title"}
   {else}
   {eval var="{tr}Top Pages{/tr}" assign="tpl_module_title"}
   {/if}
{/if}
  {tikimodule title=$tpl_module_title name="top_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  {if $nonums != 'y'}<ol>{else}<ul>{/if}
  {section name=ix loop=$modTopPages}
     <li>
	<a class="linkmodule" href="tiki-index.php?page={$modTopPages[ix].name|escape:'url'}">{$modTopPages[ix].name}</a>
	 </li>
  {/section}
  {if $nonums != 'y'}</ol>{else}</ul>{/if}
  {/tikimodule}
{/if}
