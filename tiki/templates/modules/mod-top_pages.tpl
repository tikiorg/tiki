{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_pages.tpl,v 1.14 2007-02-18 11:21:19 mose Exp $ *}

{if $feature_wiki eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` Pages{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Pages{/tr}" assign="tpl_module_title"}
{/if}
{/if}

  {tikimodule title=$tpl_module_title name="top_pages" flip=$module_params.flip decorations=$module_params.decorations}
  <table  border="0" cellpadding="0" cellspacing="0">
  {section name=ix loop=$modTopPages}
     <tr>{if $nonums != 'y'}<td class="module" valign='top'>{$smarty.section.ix.index_next})</td>{/if}
     <td class="module"><a class="linkmodule" href="tiki-index.php?page={$modTopPages[ix].pageName|escape:'url'}">{$modTopPages[ix].pageName}</a></td></tr>
  {/section}
  </table>
  {/tikimodule}
{/if}
