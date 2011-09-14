{if $style eq 'tr'}
<tr{if isset($tr_class)} class="{$tr_class}"{/if}>
  <td><label for="wiki_authors_style">{tr}List authors:{/tr}</label></td>
  <td>
    <select id="wiki_authors_style" name="wiki_authors_style">
      {if isset($wiki_authors_style_site) && $wiki_authors_style_site eq 'y'}
      <option value="" style="font-style:italic;border-bottom:1px dashed #666;"{if $wiki_authors_style eq ''} selected="selected"{/if}>{tr}Site default{/tr}</option>
      {/if}
      <option value="classic"{if $wiki_authors_style eq 'classic'} selected="selected"{/if}>{tr}Creator &amp; Last Editor{/tr}</option>
      <option value="business"{if $wiki_authors_style eq 'business'} selected="selected"{/if}>{tr}Business style{/tr}</option>
      <option value="collaborative"{if $wiki_authors_style eq 'collaborative'} selected="selected"{/if}>{tr}Collaborative style{/tr}</option>
      <option value="lastmodif"{if $wiki_authors_style eq 'lastmodif'} selected="selected"{/if}>{tr}Page last modified on{/tr}</option>
      <option value="none"{if $wiki_authors_style eq 'none'} selected="selected"{/if}>{tr}None{/tr} ({tr}Disabled{/tr})</option>
    </select> 
  </td>
</tr>

{else}
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wiki_authors_style">{tr}List authors:{/tr}</label> <a class="link" href="tiki-objectpermissions.php?permType=wiki" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
	<select name="wiki_authors_style" id="wiki_authors_style">
      {if isset($wiki_authors_style_site) && $wiki_authors_style_site eq 'y'}
      <option value="" style="font-style:italic;border-bottom:1px dashed #666;"{if $wiki_authors_style eq ''} selected="selected"{/if}>{tr}Site default{/tr}</option>
      {/if}
      <option value="classic"{if $wiki_authors_style eq 'classic'} selected="selected"{/if}>{tr}as Creator &amp; Last Editor{/tr}</option>
      <option value="business"{if $wiki_authors_style eq 'business'} selected="selected"{/if}>{tr}Business style{/tr}</option>
      <option value="collaborative"{if $wiki_authors_style eq 'collaborative'} selected="selected"{/if}>{tr}Collaborative style{/tr}</option>
      <option value="lastmodif"{if $wiki_authors_style eq 'lastmodif'} selected="selected"{/if}>{tr}Page last modified on{/tr}</option>
      <option value="none"{if $wiki_authors_style eq 'none'} selected="selected"{/if}>{tr}no (disabled){/tr}</option>
    </select> 
	</div>
</div>
{/if}
