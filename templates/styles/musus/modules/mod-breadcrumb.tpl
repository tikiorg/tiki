{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-breadcrumb.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $feature_featuredLinks eq 'y'}
  {tikimodule title="{tr}Recently visited pages{/tr}" name="breadcrumb"}
    <table  border="0" cellpadding="0" cellspacing="0">
      {section name=ix loop=$breadCrumb}
        <tr><td class="module">
          <a class="linkmodule" href="tiki-index.php?page={$breadCrumb[ix]}">
            {if ($maxlen > 0 && strlen($breadCrumb[ix]) > $maxlen)}
              {$breadCrumb[ix]|truncate:$maxlen:"...":true}
            {else}
              {$breadCrumb[ix]}
            {/if}
          </a>
        </td></tr>
      {sectionelse}
        <tr><td class="module">&nbsp;</td></tr>
      {/section}
    </table>
  {/tikimodule}
{/if}
