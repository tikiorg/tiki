{if $feature_wiki eq 'y'}
 <div class="box">
  <div class="box-title">
   {tr}Last changes{/tr}
  </div>
  <div class="box-data">
   <table width="100%" border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastModif}
     <tr>
      <td  width="5%" class="module" valign="top">
       {$smarty.section.ix.index_next})
      </td>
      <td class="module">&nbsp;
       <a class="linkmodule" href="tiki-index.php?page={$modLastModif[ix].pageName|escape:"url"}" 
			 {if strlen($modLastModif[ix].pageName) gt $maxlen}title="{$modLastModif[ix].pageName}"{/if}>
				{if $maxlen > 0}
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
{/if}{* $feature_wiki eq 'y' *}
