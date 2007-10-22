{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_pages.tpl,v 1.16.2.1 2007-10-22 07:29:56 mose Exp $ *}

{if $prefs.feature_wiki eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` Pages{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Pages{/tr}" assign="tpl_module_title"}
{/if}
{/if}

  {tikimodule title=$tpl_module_title name="top_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <table  border="0" cellpadding="0" cellspacing="0">
  {section name=ix loop=$modTopPages}
     <tr>{if $nonums != 'y'}<td class="module" valign='top'>{$smarty.section.ix.index_next})</td>{/if}
     <td class="module"><a class="linkmodule" href="tiki-index.php?page={$modTopPages[ix].name|escape:'url'}">{$modTopPages[ix].name}</a></td></tr>
  {/section}
  </table>
  {/tikimodule}
{/if}
