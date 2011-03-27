{* $Id$ *}
{* Module layout with controls *}
{if $module_nobox neq 'y'}
{if $prefs.feature_layoutshadows}<div class="box-shadow">{$prefs.box_shadow_start}{/if}
	<div class="box box-{$module_name|escape}{if $module_type eq 'cssmenu'} cssmenubox{/if}"{if $module_params.overflow == 'y'} style="overflow:visible !important"{/if}>
		<div class="bt">{*box top and right corner*}
			<div></div>{*left corner*}
		</div>
	{if $module_decorations ne 'n'}
		<h3 class="box-title clearfix"{if !empty($module_params.bgcolor)} style="background-color:{$module_params.bgcolor};"{/if}>
		{if $user and $prefs.user_assigned_modules == 'y' and $prefs.feature_modulecontrols eq 'y'}
			<span class="modcontrols">
			<a title="{tr}Move module up{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_up={$module_name|escape}">
				{icon _id="resultset_up" alt="[{tr}Up{/tr}]"}
			</a>
			<a title="{tr}Move module down{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_down={$module_name|escape}">
				{icon _id="resultset_down" alt="[{tr}Down{/tr}]"}
			</a>
			<a title="{tr}Move module to opposite side{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_move={$module_name|escape}">
				{icon _id="arrow_right-left" alt="[{tr}opp side{/tr}]"}
			</a>
			<a title="{tr}Unassign this module{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_unassign={$module_name|escape}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to unassign this module?{/tr}')">
				{icon _id="cross" alt="[{tr}Remove{/tr}]"}
			 </a>
			</span>
		{/if}
		{if $module_notitle ne 'y' }
		<span class="moduletitle">{$module_title}</span>
		{/if}
		{if $module_flip eq 'y' and $prefs.javascript_enabled ne 'n'}
			<span class="moduleflip" id="moduleflip-{$module_name|cat:$module_position|cat:$module_ord|escape}">
				<a title="{tr}Toggle module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','module.png');">
					{capture name=name}icnmod-{$module_name|cat:$module_position|cat:$module_ord|escape}{/capture}
					{icon name=$smarty.capture.name class="flipmodimage" _id="module" alt="[{tr}toggle{/tr}]"}
				</a>
			</span>
			{if $prefs.menus_items_icons eq 'y'}
			<span class="moduleflip moduleflip-vert" id="moduleflip-vert-{$module_name|cat:$module_position|cat:$module_ord|escape}">
				<a title="{tr}Toggle module contents{/tr}" class="flipmodtitle" href="javascript:flip_class('main','minimize-modules-left','maximize-modules');{if $prefs.feature_phplayers eq 'y' and isset($phplayers_LayersMenu)}moveLayers();{/if}icntoggle('modv-{$module_name|cat:$module_position|cat:$module_ord|escape}','vmodule.png');">
					{capture name=name}
						icnmodv-{$module_name|cat:$module_position|cat:$module_ord|escape}
					{/capture}
					{icon name=$smarty.capture.name class="flipmodimage" _id="trans" alt="[{tr}Toggle Vertically{/tr}]" _defaultdir="pics"}
				</a>
			</span>
			{/if}
		{/if}
		<!--[if IE]><br class="clear" style="height: 0px !important" /><![endif]--></h3>
		<div class="box-titlebottom"><div></div></div>
	{elseif $module_notitle ne 'y' }
		{if $module_flip eq 'y' and $prefs.javascript_enabled ne 'n'}
			<h3 class="box-title" ondblclick="javascript:icntoggle('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','module.png');"{if !empty($module_params.color)} style="color:{$module_params.color};"{/if}>
		{else}
			<h3 class="box-title"{if !empty($module_params.color)} style="color:{$module_params.color};"{/if}>
		{/if}
		{$module_title}
		{if $module_flip eq 'y' and $prefs.javascript_enabled ne 'n'}
			<span id="moduleflip-{$module_name|cat:$module_position|cat:$module_ord|escape}">
				<a title="{tr}Toggle module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','module.png');">
					{assign var=name value=`icnmod-$module_name|cat:$module_position|cat:$module_ord|escape`}
					{capture name=name}
						icnmod-{$module_name|cat:$module_position|cat:$module_ord|escape}
					{/capture}
					{icon name=$smarty.capture.name class="flipmodimage" _id="module" alt="[{tr}Hide{/tr}]"}
				</a>
			</span>
		{/if}
		</h3><!--[if IE]><br class="clear" style="height: 0px !important" /><![endif]-->
		<div class="box-titlebottom"><div></div></div>
	{/if}
		<div id="mod-{$module_name|cat:$module_position|cat:$module_ord|escape}" style="display: block" class="clearfix box-data-top">{*box data top and right corner*}
			<div></div>{*box data top left corner*}
			<div class="i1">{*box left border*}
				<div class="i2">{*box right border*}
					<div class="i3 clearfix box-data">{*box data actual*}
{/if}
{$module_content}<!--[if IE]><br class="clear" style="height: 0px" /><![endif]-->
{$module_error}
{if $module_nobox neq 'y'}
{if $module_flip eq 'y'}
			<script type="text/javascript">
<!--//--><![CDATA[//><!--
				setsectionstate('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','{$module_dstate}', 'module.png');
//--><!]]>
			</script>
{/if}
					</div>{*box data actual*} 
				</div>{*box right border*}
			</div>{*box left border*}

	</div>{*box*}		<div class="bb">{*box bottom and right corner*}
		<div></div>{*box bottom left corner*}
		</div>{* box bottom and right corner *}
</div>{*cb box-shadow*}
{/if}
