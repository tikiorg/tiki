{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-breadcrumb.tpl,v 1.13.2.1 2008/01/07 17:00:29 sylvieg *}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}Recently visited pages{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="breadcrumb" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  {if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$breadCrumb}
      <li>
        <a class="linkmodule" href="tiki-index.php?page={$breadCrumb[ix]|escape:'url'}">
          {if ($maxlen > 0 && strlen($breadCrumb[ix]) > $maxlen)}
            {$breadCrumb[ix]|truncate:$maxlen:"...":true}
          {else}
            {$breadCrumb[ix]}
          {/if}
        </a>
      </li>
    {*{sectionelse}
      <div class="module">&nbsp;</div>*}
    {/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
