{* $Id$ *}
{$headerlib->add_jsfile('lib/jquery/jquery.dcdrilldown.1.2.js')}
{$headerlib->add_cssfile('lib/jquery/dcdrilldown.css')}
{jq}
	$('.dropdownmenuparent a').each(function() {
		var me = $(this);
		if (me.next().find('a').length) {
			me.append('<span class="sep">&nbsp;&raquo;</span>');
		}
		me.prepend('&nbsp;');
	});

	function hideOtherLists(list) {
		var lists = $('li.drilldown')
			.removeClass('active')
			.not(list);

		list.addClass('drilldown active');
	}

	$('.dropdownmenuparent')
		.bind('actionDrilldown', function(event, element, wrapper, obj) {
			var list = $('> ul > li', element);

			hideOtherLists(list);

			list.filter('.active').parent().unbind('hover').hover(function() {
				list.filter('.active').fadeIn();
			}, function() {
				list.filter('.active').first().siblings().hide();
			});

			list.filter('.active').first().siblings().hide();
		})
		.bind('resetDrilldown', function(event, obj, wrapper) {
			var list = $('> li',this);

			hideOtherLists(list);

			list.filter('.active').parent().unbind('hover').hover(function() {
				list.filter('.active').fadeIn();
			}, function() {
				list.filter('.active').first().siblings().hide();
			});

			list.filter('.active').first().siblings().hide();
		})
		.dcDrilldown({
			speed: 'fast',
			linkType: 'breadcrumb',
			headerTag: 'span',
			showCount: false,
			eventPreventDefault: true,
			horizontal: true
		});
{/jq}
<style>
	.dd-header, .dd-header > *, .dd-header > ul > li {
		display: block ! important;
		float: left ! important;
	}
	.dropdownmenuparent
	{
		position: absolute ! important;
	}
</style><br />
<div class="dd_container">
	<ul class="dropdownmenuparent">
		<li>
			<a href="">{$home_info.pageName}</a>
			{include file="tiki-user_cssmenu.tpl" drilldownmenu='y'}
		</li>
	</ul>
</div>
