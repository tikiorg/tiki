<a id="asnarf{$snarfParams.iSnarf}" href="#">{tr}{$snarfParams.ajax|escape:html}{/tr}</a>
<div id="snarf{$snarfParams.iSnarf}"></div>
{jq}
$('#asnarf{{$snarfParams.iSnarf}}').click(function() {
	$('#snarf{{$snarfParams.iSnarf}}').load('snarf_ajax.php?{{$snarfParams.href}}');
});
{/jq}