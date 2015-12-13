{* $Id$ *}

<div id="ajax-feedback" style="display:none"></div>
{if isset($ajaxfeedback) && $ajaxfeedback eq 'y'}
	<div id="posted-ajax-feedback">
		{include file="utilities/alert.tpl"}
	</div>
{/if}

{if !empty($tikifeedback)}
	{remarksbox type="feedback" title="{tr}Feedback{/tr}"}
		{section name=n loop=$tikifeedback}
			{tr}{$tikifeedback[n].mes|escape}{/tr}
			<br>
		{/section}
	{/remarksbox}
{/if}

{if !empty($errors)}
	{remarksbox type="warning" title="{tr}Errors{/tr}"}
		{foreach from=$errors item=error name=error}
			{if !$smarty.foreach.error.first}<br>{/if}
			{$error|escape}
		{/foreach}
	{/remarksbox}
{/if}

{if !empty($feedbacks)}
	{remarksbox type="note"}
		{foreach from=$feedbacks item=feedback name=feedback}
			{$feedback|escape}
			{if !$smarty.foreach.feedback.first}<br>{/if}
		{/foreach}
	{/remarksbox}
{/if}

