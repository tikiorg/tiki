<div id="{$id}" class="alert alert-{$type} hide" role="alert">
{if $dismissable}
<div class="trigid hide">triggeralert-{$id}</div>
	<a class="pull-right close" id="triggeralert-{$id}" data-target="{$id}"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></a>
{/if}
{$contents}
</div>
<!--Create the cookie hash and store it in a hidden div for JQ to be able to access it -->
{assign var="cookie_hash" value="{$id}{$version}{$smarty.get.page}{$user}"}
{assign var="cookie_hash" value="{$cookie_hash|md5}"}
<div id="cook" class="hide">{$cookie_hash}</div>

{jq}
    var targetalert = $("#triggeralert-{{$id}}").data("target");
	var pt = $('#cook').html();
	var has_cookie = getCookie(pt);
	if (!has_cookie){
		$("#"+targetalert).removeClass('hide');
	}

	$("#triggeralert-{{$id}}").click(function() {
		var targetalert = $(this).data("target");
		$("#"+targetalert).addClass('hide');
		var pt = $('#cook').html();
		document.cookie=pt + "=" +"dismiss";
	});
{/jq}
