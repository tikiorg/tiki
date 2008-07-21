{* $Id$ *}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}Recently visited pages{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="breadcrumb" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
   {if $module_params.nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$breadCrumb}
      <li>
        <a class="linkmodule" href="{$breadCrumb[ix]|sefurl}">
          {if ($maxlen > 0 && strlen($breadCrumb[ix]) > $maxlen)}
            {$breadCrumb[ix]|truncate:$maxlen:"...":true}
          {else}
            {$breadCrumb[ix]|escape:'html'}
          {/if}
        </a>
      </li>
    {*{sectionelse}
      <div class="module">&nbsp;</div>*}
    {/section}
	 {if $module_params.nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
