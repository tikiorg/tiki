{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_pages.tpl,v 1.6 2003-08-07 20:56:53 zaufi Exp $ *}

{if $user}
  {if $feature_wiki eq 'y'}
    <div class="box">
     <div class="box-title">
       {include file="modules/module-title.tpl" module_title="{tr}My Pages{/tr}" module_name="user_pages"}
     </div>
     <div class="box-data">
       <table width="100%" border="0" cellpadding="0" cellspacing="0">
       {section name=ix loop=$modUserPages}
       <tr>
        <td  width="5%" class="module" valign="top">
          {$smarty.section.ix.index_next})
        </td>
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