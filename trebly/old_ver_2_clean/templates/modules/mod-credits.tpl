{tikimodule error=$module_params.error title=$tpl_module_title name="credits" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{foreach key=id item=data from=$tiki_user_credits}
			<div>
				{$data.display_text|escape}: 
				{if $data.empty}
					{section name=used loop=$data.discreet_used}<img src="images/barre_fluo_empty.gif" width="5" height="9" class="header_comptebarre" alt=""/>{/section}{section name=remain loop=$data.discreet_remain}<img src="images/barre_empty.gif" width="5" height="9" class="header_comptebarre" alt=""/>{/section} <span class="textes_comptevert"><font color='red'>{$data.used|default:0}</font></span>/{$data.total|default:0} {$data.unit_text|escape}
					{tr}empty{/tr}
				{elseif $data.low}
					{section name=used loop=$data.discreet_used}<img src="images/barre_fluo_low.gif" width="5" height="9" class="header_comptebarre" alt=""/>{/section}{section name=remain loop=$data.discreet_remain}<img src="images/barre_low.gif" width="5" height="9" class="header_comptebarre" alt=""/>{/section} <span class="textes_comptevert"><font color='yellow'>{$data.used|default:0}</font></span>/{$data.total|default:0} {$data.unit_text|escape}
					{tr}low{/tr}
				{else}
					{section name=used loop=$data.discreet_used}<img src="images/barre_fluo.gif" width="5" height="9" class="header_comptebarre" alt=""/>{/section}{section name=remain loop=$data.discreet_remain}<img src="images/barre.gif" width="5" height="9" class="header_comptebarre" alt=""/>{/section} <span class="textes_comptevert">{$data.used|default:0}</span>/{$data.total|default:0} {$data.unit_text|escape}
				{/if}
			</div>
	{/foreach}
{/tikimodule}
