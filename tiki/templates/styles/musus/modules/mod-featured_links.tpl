{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-featured_links.tpl,v 1.2 2004-01-09 15:29:32 musus Exp $ *}

{if $feature_featuredLinks eq 'y'}
  {tikimodule title="{tr}Featured links{/tr}" name="featured_links"}
   <table>
    {section name=ix loop=$featuredLinks}
     {if $featuredLinks[ix].type eq 'f'}
      <tr>
       <td class="module">
        <a class="linkmodule" href="tiki-featured_link.php?type={$featuredLinks[ix].type}&amp;url={$featuredLinks[ix].url|escape:"url"}">
         {$featuredLinks[ix].title}
        </a>
       </td>
      </tr>
     {else}
      <tr>
       <td class="module">
        <a class="linkmodule" {if $featuredLinks[ix].type eq 'n'}target='_blank'{/if} href="{$featuredLinks[ix].url}">
         {$featuredLinks[ix].title}
        </a>
       </td>
      </tr>
     {/if}
    {/section}
   </table>
  {/tikimodule}
{/if}
