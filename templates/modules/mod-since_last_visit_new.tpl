{* $Id$ *}

{if $user}
	{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}`$slvn_info.label`{/tr}"}{/if}
	{tikimodule title=$tpl_module_title name="since_last_visit_new" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
    <div style="text-align: center">
	{if $prefs.feature_calendar eq 'y'}<a class="linkmodule" href="tiki-calendar.php?todate={$slvn_info.lastLogin}" title="{tr}click to edit{/tr}">{/if}
		<strong>{$slvn_info.lastLogin|tiki_short_date}</strong>
	{if $prefs.feature_calendar eq 'y'}</a>{/if}
    </div>
    {if $slvn_info.cant == 0}
	<div class="separator">
		<em>{tr}Nothing has changed{/tr}</em>
	</div>
    {else}
         {foreach key=pos item=slvn_item from=$slvn_info.items}
            {if $slvn_item.count > 0 }
              {assign var=cname value=$slvn_item.cname}
              <div class="separator">
                <a class="separator" href="javascript:flip('{$cname}');" title="{tr}click to show/hide{/tr}">
                  {$slvn_item.label}:&nbsp;{$slvn_item.count}
                </a>
             </div>
             {assign var=showcname value=show_$cname}
             <div id="{$cname}" style="display:{if !isset($cookie.$showcname) or $cookie.$showcname eq 'y'}block{else}none{/if}">
               <ol style="padding-left: 10px; margin-left: 10px">
                 {section name=ix loop=$slvn_item.list}
                   <li class="listitem" style="margin-left: 5px">
                       <a class="linkmodule"
                           href="{$slvn_item.list[ix].href|escape}"
                           title="{$slvn_item.list[ix].title|escape}">
                         {if $slvn_item.list[ix].label == ''}-{else}{$slvn_item.list[ix].label|escape}{/if}
                       </a>
                   </li>
                 {/section}
               </ol>
             </div>
           {/if}
         {/foreach}
    {/if}
  {/tikimodule}
{/if}
      
