{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_modif_pages.tpl,v 1.13 2003-10-20 01:13:16 zaufi Exp $ *}

{if $feature_wiki eq 'y'}
 <div class="box">
  <div class="box-title">
    {include file="modules/module-title.tpl" module_title="{tr}Last changes{/tr}" module_name="last_modif_pages"}
  </div>
  <div class="box-data">
   <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastModif}
     <tr>
      {if $nonums != 'y'}
        <td class="module" valign="top">{$smarty.section.ix.index_next})</td>
      {/if}
      <td class="module">&nbsp;
       <a class="linkmodule" href="tiki-index.php?page={$modLastModif[ix].pageName|escape:"url"}"
        {if (strlen($modLastModif[ix].pageName) > $maxlen) && ($maxlen > 0)}title="{$modLastModif[ix].pageName}"{/if}>
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
  </div>
 </div>
{/if}
