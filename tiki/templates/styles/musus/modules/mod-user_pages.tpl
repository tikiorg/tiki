{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-user_pages.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $user}
  {if $feature_wiki eq 'y'}
       
       {tikimodule title="{tr}My Pages{/tr}" name="user_pages"}
       <table  border="0" cellpadding="0" cellspacing="0">
       {section name=ix loop=$modUserPages}
       <tr>
        {if $nonums != 'y'}
          <td class="module" valign="top">{$smarty.section.ix.index_next})</td>
        {/if}
        <td class="module">&nbsp;
         <a class="linkmodule" href="tiki-index.php?page={$modUserPages[ix].pageName|escape:"url"}">
          {$modUserPages[ix].pageName}
         </a>
        </td>
       </tr>
       {/section}
       </table>
       {/tikimodule}
       
  {/if} {* $feature_wiki eq 'y' *}
{/if}   {* $user *}