{* $Id$ *}
{* Module layout with controls *}
{if !isset($module_position)}{assign var=module_position value=' '}{/if}
{if !isset($module_ord)}{assign var=module_ord value=' '}{/if}
{capture name=name}{$module_name|replace:"+":"_"|cat:$module_position|cat:$module_ord|escape}{/capture}
{if $module_nobox neq 'y'}
{if $prefs.feature_layoutshadows eq 'y'}<div class="box-shadow">{$prefs.box_shadow_start}{/if}
	{if !isset($moduleId)}{assign var=moduleId value=' '}{/if}
	<div id="module_{$moduleId}" class="box box-{$module_name}{if $module_type eq 'cssmenu'} cssmenubox{/if} module"{if !empty($tpl_module_style)} style="{$tpl_module_style}"{/if}>
	{if $module_decorations ne 'n'}
		<h3 class="box-title clearfix"{if !empty($module_params.bgcolor)} style="background-color:{$module_params.bgcolor};"{/if}>
		{if isset($user) and $user and $prefs.user_assigned_modules == 'y' and $prefs.feature_modulecontrols eq 'y'}
			<span class="modcontrols">
			<a title="{tr}Move module up{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_up={$module_name}">
				{icon _id="resultset_up" alt="[{tr}Up{/tr}]"}
			</a>
			<a title="{tr}Move module down{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_down={$module_name}">
				{icon _id="resultset_down" alt="[{tr}Down{/tr}]"}
			</a>
			<a title="{tr}Move module to opposite side{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_move={$module_name}">
				{icon _id="arrow_right-left" alt="[{tr}opp side{/tr}]"}
			</a>
			<a title="{tr}Unassign this module{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_unassign={$module_name}" onclick='return confirmTheLink(this,"{tr}Are you sure you want to unassign this module?{/tr}")'>
				{icon _id="cross" alt="[{tr}Remove{/tr}]"}
			 </a>
			</span>
		{/if}
		{if $module_notitle ne 'y'}
			<span class="moduletitle">{$module_title}</span>
		{/if}
		{if $module_flip eq 'y' and $prefs.javascript_enabled ne 'n'}
			<span class="moduleflip" id="moduleflip-{$smarty.capture.name}">
				<a title="{tr}Toggle module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$smarty.capture.name}','module.png');">
					{icon id="icnmod-"|cat:$smarty.capture.name class="flipmodimage" _id="module" alt="[{tr}toggle{/tr}]"}
				</a>
			</span>
			{if $prefs.menus_items_icons eq 'y'}
				<span class="moduleflip moduleflip-vert" id="moduleflip-vert-{$smarty.capture.name}">
					<a title="{tr}Toggle module contents{/tr}" class="flipmodtitle" href="javascript:flip_class('main','minimize-modules-left','maximize-modules');icntoggle('modv-{$smarty.capture.name}','vmodule.png');">
						{capture name='name'}
							icnmodv-{$smarty.capture.name}
						{/capture}
						{icon name="icnmod-"|cat:$smarty.capture.name class="flipmodimage" _id="trans" alt="[{tr}Toggle Vertically{/tr}]" _defaultdir="pics"}
					</a>
				</span>
			{/if}
		{/if}
		<!--[if IE]><br class="clear" style="height: 1px !important" /><![endif]--></h3>
	{elseif $module_notitle ne 'y'}
		{if $module_flip eq 'y' and $prefs.javascript_enabled ne 'n'}
			<h3 class="box-title" ondblclick="javascript:icntoggle('mod-{$smarty.capture.name}','module.png');"{if !empty($module_params.color)} style="color:{$module_params.color};"{/if}>
		{else}
			<h3 class="box-title"{if !empty($module_params.color)} style="color:{$module_params.color};"{/if}>
		{/if}
		{$module_title}
		{if $module_flip eq 'y' and $prefs.javascript_enabled ne 'n'}
			<span id="moduleflip-{$smarty.capture.name}">
				<a title="{tr}Toggle module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$smarty.capture.name}','module.png');">
					{assign var="name" value="icnmod-"|cat:$smarty.capture.name}
					{capture name=name}
						icnmod-{$smarty.capture.name}
					{/capture}
					{icon name="icnmod-"|cat:$smarty.capture.name class="flipmodimage" _id="module" alt="[{tr}Hide{/tr}]"}
				</a>
			</span>
		{/if}
		<!--[if IE]><br class="clear" style="height: 1px !important" /><![endif]--></h3>
	{/if}
		<div id="mod-{$smarty.capture.name}" style="display: {if !isset($module_display) or $module_display}block{else}none{/if};{$module_params.style}" class="clearfix box-data{if !empty($module_params.class)} {$module_params.class}{/if}">
{else}{* $module_nobox eq 'y' *}
		<div id="module_{$moduleId}" style="{$module_params.style}{$tpl_module_style}" class="module{if !empty($module_params.class)} {$module_params.class}{/if} box-{$module_name} clearfix">
			<div id="mod-{$smarty.capture.name}" class="clearfix">
{/if}
{$module_content}
{$module_error}
{if $module_nobox neq 'y'}
		</div>
		<div class="box-footer">

		</div>
	</div>
{if $prefs.feature_layoutshadows eq 'y'}{$prefs.box_shadow_end}</div>{/if}
{else}
		</div>
	</div>
{/if}
