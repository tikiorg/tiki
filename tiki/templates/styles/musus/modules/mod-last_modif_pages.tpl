{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-last_modif_pages.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $feature_wiki eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` changes{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last changes{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_modif_pages"}
   <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastModif}
     <tr>
      {if $nonums != 'y'}
        <td class="module" valign="top">{$smarty.section.ix.index_next})</td>
      {/if}
      <td class="module">&nbsp;
       <a class="linkmodule" href="tiki-index.php?page={$modLastModif[ix].pageName|escape:"url"}" title="{$modLastModif[ix].lastModif|tiki_short_datetime}, by {$modLastModif[ix].user}{if (strlen($modLastModif[ix].pageName) > $maxlen) && ($maxlen > 0)}, {$modLastModif[ix].pageName}{/if}">
        {if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
         {$modLastModif[ix].pageName|truncate:$maxlen:"...":true}
        {else}
         {$modLastModif[ix].pageName}
        {/if}
       </a>
      </td>
     </tr>
    {/section}
   </table>
{/tikimodule}
{/if}
