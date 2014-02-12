<form action="tiki-admin.php?page=metrics" onreset="return(confirm('{tr}Cancel Edit{/tr}'))" class="admin" method="post">

    <div class="row">
        <div class="form-group col-lg-12 clearfix">
            <div class="pull-right">
                <input type="submit" class="btn btn-default btn-sm" value="{tr}Change preferences{/tr}">
            </div>
        </div>
    </div>
	<fieldset>
		<legend>{tr}Metrics Dashboard{/tr}</legend>

	<div class="t_navbar">
		{button href="tiki-admin_metrics.php" class="btn btn-default" _text="{tr}Configure metrics{/tr}"}
	</div>

  		{preference name=feature_metrics_dashboard visible="always"}
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
<br>{* I cheated. *}
<div class="row">
    <div class="form-group col-lg-12">
        <input type="submit" class="btn btn-default btn-sm pull-right" value="{tr}Change preferences{/tr}">
    </div>
</div>
</form>
