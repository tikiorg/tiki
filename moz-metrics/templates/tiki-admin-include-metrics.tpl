

<div class="cbox">
  <div class="cbox-title">{tr}Metrics results{/tr}</div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=metrics">
        <table class="admin">
        <tr class="form">
          <td><label>{tr}Show past results{/tr}:</label></td>
          <td><input type="checkbox" name="metrics_pastresults"
              {if $prefs.metrics_pastresults eq 'y'}checked="checked"{/if}/></td>
        </tr>
        <tr class="form">
          <td><label>{tr}Number of past results to show{/tr}:</label></td>
          <td><input size="5" type="text" name="metrics_pastresults_count" value="{$prefs.metrics_pastresults_count|escape}" /></td>
        </tr>
        <tr class="form">
          <td><label>{tr}Trend No Value{/tr}:</label></td>
          <td><input size="5" type="text" name="metrics_trend_novalue" value="{$prefs.metrics_trend_novalue|escape}" /></td>
        </tr>
        <tr class="form">
          <td><label>{tr}Trend Prefix{/tr}:</label></td>
          <td><input size="5" type="text" name="metrics_trend_prefix" value="{$prefs.metrics_trend_prefix|escape}" /></td>
        </tr>
        <tr class="form">
          <td><label>{tr}Trend Suffix{/tr}:</label></td>
          <td><input size="5" type="text" name="metrics_trend_suffix" value="{$prefs.metrics_trend_suffix|escape}" /></td>
        </tr>
        <tr class="form">
          <td><label>{tr}Metric Name Length{/tr}:</label></td>
          <td><input size="5" type="text" name="metrics_metric_name_length" value="{$prefs.metrics_metric_name_length|escape}" /></td>
        </tr>
        <tr class="form">
          <td><label>{tr}Tab Name Length{/tr}:</label></td>
          <td><input size="5" type="text" name="metrics_tab_name_length" value="{$prefs.metrics_tab_name_length|escape}" /></td>
        </tr>
        <tr class="form">
          <td colspan="2" class="button"><input type="submit" name="metricsprefs"
              value="{tr}Change preferences{/tr}" /></td>
        </tr>
        </table>
      </form>
  </div>
</div>
