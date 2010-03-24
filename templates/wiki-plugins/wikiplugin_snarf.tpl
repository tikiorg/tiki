<a id="asnarf{$snarfParams.iSnarf}">{tr}{$snarfParams.ajax}{/tr}</a>
<div id="snarf{$snarfParams.iSnarf}"></div>
{jq}
$jq('#asnarf{{$snarfParams.iSnarf}}').click(function() {
	$jq('#snarf{{$snarfParams.iSnarf}}').load('snarf_ajax.php?{{$snarfParams.href}}');
});
{/jq}