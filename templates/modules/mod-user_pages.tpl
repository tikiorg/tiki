{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_pages.tpl,v 1.15 2007-10-14 17:51:02 mose Exp $ *}

{if $user}
  {if $prefs.feature_wiki eq 'y'}
       
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}My Pages{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="user_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
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
       
  {/if} {* $prefs.feature_wiki eq 'y' *}
{/if}   {* $user *}
