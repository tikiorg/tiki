{* $Id$ *}
{jq}
	var drilldownactive = false;
	var drilldowntimer = false;
	$('div.tocnav a')
		.mouseover(function(e) {
			var href = $(e.target).attr('href');
			var ul = $('ul.drilldownmenuparent').find('a[href="' + href + '"]').next(); //possible ul menu
			var a = ul.children('li').children('a');

			var drillshow = $('div.drillshow').html('');
			if (a.length < 1) return;
			a.each(function(i) {
				var newA = $(this).clone().appendTo(drillshow);
				if (i < a.length - 1) {
					$('<span> | </span>').insertAfter(newA);
				}
			});
			drilldownactive = true;
		});

	$('div.tocnav').mouseout(function() {
		drilldownactive = false;
		if (drilldowntimer == false) {
			drilldowntimer = true;
			setTimeout(function() {
				drilldowntimer = false;
				if (drilldownactive == false) {
					$('div.drillshow').html('');
				}
			}, 5000);
		}
	});
{/jq}
<style>
	.drilldownmenucontainer
	{
		display: none;
	}
	.drillshow
	{
		position:absolute;
		background:#ffffff;
	}
</style>
<div class="drillshow"></div>
<div class="drilldownmenucontainer">
	<ul class="drilldownmenuparent">
		<li>
			<a href="tiki-index.php?page={$home_info.pageName|urlencode}&structure={$home_info.pageName|urlencode}">{$home_info.pageName}</a>
			{include file="tiki-user_cssmenu.tpl" drilldownmenu='y'}
		</li>
	</ul>
</div>
