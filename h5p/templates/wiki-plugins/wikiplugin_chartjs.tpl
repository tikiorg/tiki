<div class="tiki-chartjs"><canvas id="{$id}" width="{$width}" height="{$height}"></canvas></div>

{jq}
	var data = {{$data}}
	var ctx = $("#{{$id}}").get(0).getContext("2d");
	var myChart = new Chart(ctx).{{$type}}(data);
{/jq}