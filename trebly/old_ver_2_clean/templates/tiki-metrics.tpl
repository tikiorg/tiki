<div class="page-header">
	<div class="metrics-range-update">
		<form method="GET">
		<span class="metrics-range-text">{tr}Change to:{/tr}</span>
		<select class="metrics-range-select" name="range" id="metrics-range-select">
			<option value="lastweek" {if $smarty.request.range eq 'lastweek'}selected="selected"{/if}>last week</option>
			<option value="weekof" {if $smarty.request.range eq 'weekof'}selected="selected"{/if}>week of...</option>
			<option value="monthof" {if $smarty.request.range eq 'monthof'}selected="selected"{/if}>month of...</option>
			<option value="custom" {if $smarty.request.range eq 'custom'}selected="selected"{/if}>custom range...</option>
		</select>
		<input type="submit" name="update" value="Update" /><br/>
		<div id="range-inputs">
			<span class="range-weekof-text">{tr}Week containing:{/tr}</span>
			<span class="range-monthof-text">{tr}Month containing:{/tr}</span>
			<span class="range-custom-text">{tr}From:{/tr}</span>
			<input type="text" name="date_from" maxlength="10" value="{$date_from}" id="range-date-from" class="range-date-from" />
			<span class="range-custom-text">{tr}To:{/tr}</span>
			<input type="text" name="date_to" maxlength="10" value="{$date_to}" id="range-date-to" class="range-date-to" />
		</div>
		</form>
	</div>
	<h1 class="pagetitle">{tr}Metrics Dashboard{/tr}</h1>
	<div class="metrics-range">
		<span class="metrics-range-prefix">{tr}{$metrics_range_prefix}{/tr}</span>
		<span class="metrics-range">{$metrics_range}</span>
		<span class="metrics-range-suffix">{tr}{$metrics_range_suffix}{/tr}</span>
	</div>
	<div class="metrics-type">
		{section name=type loop=$metrics_type}
		{strip}
		<span>{$metrics_type[type].type}</span>
		{/strip}
		{/section}
	</div>
</div>
<div class="page-content">
	<!-- loop over tabs -->
	<div class="jqtabs">
	<ul>
		{foreach from=$tabs key=tabid item=tab}
			<li><a title="tab-{$tabid}" href="metrics-tab.php?tab_id={$tabid}&date_from={$date_from}&date_to={$date_to}&range={$range}">{$tab.tab_name}</a></li>
		{/foreach}
		{if $tiki_p_admin eq 'y'}
			<li><a title="tab-add" href="tiki-admin_metrics.php#editcreatetab">+</a></li>
		{/if}
	</ul>
</div>

</div><!-- End demo -->

