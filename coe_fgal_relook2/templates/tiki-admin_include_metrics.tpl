<form action="tiki-admin.php?page=metrics" onreset="return(confirm("{tr}Cancel Edit{/tr}"))" class="admin" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
	<fieldset>
		<legend>{tr}Metrics Dashboard{/tr}</legend>

	<div class="navbar">
		{button href="tiki-admin_metrics.php" _text="{tr}Configure metrics{/tr}"}
	</div>

  		{preference name=feature_metrics_dashboard}
		<div class="adminoptionboxchild" id="feature_metrics_dashboard_childcontainer">
			{preference name=metrics_pastresults label="{tr}Show past results{/tr}"}
			{preference name=metrics_pastresults_count label="{tr}Past results count{/tr}"}
			{preference name=metrics_trend_novalue}
			{preference name=metrics_trend_prefix}
			{preference name=metrics_trend_suffix}
			{preference name=metrics_metric_name_length}
			{preference name=metrics_tab_name_length}
			{preference name=metrics_cache_output}
		</div>
	</fieldset>
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
