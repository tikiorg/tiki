{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_pages.tpl,v 1.8 2003-10-20 01:13:16 zaufi Exp $ *}

{if $user}
  {if $feature_wiki eq 'y'}
    <div class="box">
     <div class="box-title">
       {include file="modules/module-title.tpl" module_title="{tr}My Pages{/tr}" module_name="user_pages"}
     </div>
     <div class="box-data">
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
     </div>
    </div>
  {/if} {* $feature_wiki eq 'y' *}
{/if}   {* $user *}