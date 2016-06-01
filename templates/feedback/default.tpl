{* $Id$ *}

<div id="ajax-feedback" style="display:none"></div>
{if isset($ajaxfeedback) && $ajaxfeedback eq 'y'}
	<div id="posted-ajax-feedback" class="col-sm-12">
		{include file="utilities/alert.tpl"}
	</div>
{/if}

<div id="tikifeedback">
	{if !empty($tikifeedback)}
		{foreach $tikifeedback as $tpl => $fb}
			{if $tpl === 'default'}
				{foreach $fb as $item}
					{remarksbox type=$item.type title=$item.title icon=$item.icon}
						{if $item.mes|count == 1}
							<ul class="list-unstyled">
						{else}
							<ul>
						{/if}
							{foreach $item.mes as $mes}
								<li>
									{$mes}
								</li>
							{/foreach}
						</ul>
					{/remarksbox}
				{/foreach}
			{elseif $tpl}
				{include file='feedback/'|cat:$tpl|cat:'.tpl'}
			{/if}
		{/foreach}
	{/if}
</div>
