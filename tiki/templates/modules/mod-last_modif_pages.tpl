{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_modif_pages.tpl,v 1.12 2003-09-25 01:05:22 rlpowell Exp $ *}

{if $feature_wiki eq 'y'}
 <div class="box">
  <div class="box-title">
    {include file="modules/module-title.tpl" module_title="{tr}Last changes{/tr}" module_name="last_modif_pages"}
  </div>
  <div class="box-data">
   <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastModif}
     <tr>
      <td  class="module" valign="top">
       {$smarty.section.ix.index_next})
      </td>
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
