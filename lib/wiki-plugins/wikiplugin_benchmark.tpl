{tabset name="wiki_benchmark"}
{tab name="General Info"}
	<div class="row" style="font-weight: bold;"><div class="col-md-4">Iteration</div>
		<div class="col-md-4">Time in seconds</div>
		<div class="col-md-4">Memory in bytes</div></div>

{if isset($secondMemory)}
	<div class="row"><div class="col-md-4">1</div>
		<div class="col-md-4"> &#160;</div>
		<div class="col-md-4">{$firstMemory}</div></div>
	<div class="row"><div class="col-md-4">2</div>
		<div class="col-md-4"> &#160;</div>
		<div class="col-md-4">{$secondMemory}</div></div>
{/if}

	<div class="row"><div class="col-md-4">3 - {$times}</div>
		<div class="col-md-4">{$time}</div>
		<div class="col-md-4">{$memory}</div></div>

	<div class="row"><div class="col-md-4">Empty Call</div>
		<div class="col-md-4">{$overTime} (microseconds)</div>
	</div>
{/tab}
{if isset($iterations)}
	{tab name="Iteration Details"}
		<div class="row"><div class="col-md-2">Iteration</div><div class="col-md-2">Bytes</div><div class="col-md-3">Secs</div></div>

	{foreach $iterations['mem'] as $times => $mem}
		<div class="row"><div class="col-md-2">{($times +1)}</div><div class="col-md-2">{$mem}</div><div class="col-md-3">{$iterations['time'][$times]}</div></div>
	{/foreach}
	{/tab}
{/if}
	<hr>
{/tabset}