{* $Id$ *}
{$headerlib->add_jsfile('lib/jquery/jquery.dcdrilldown.1.2.js')}
{$headerlib->add_cssfile('lib/jquery/dcdrilldown.css')}
{jq}
	$('.drilldownmenuparent a').each(function() {
		var me = $(this);
		if (me.next().find('a').length) {
			me.append('<span class="sep">&nbsp;&raquo;</span>');
		}
		me.prepend('&nbsp;');
	});

	function toggleSiblings(li, visible) {
		var siblings;

		if (li.find('li').length > 0) {
			siblings = li.children('ul').children('li');
			li = siblings.first();
		} else {
			siblings = li.siblings();
		}

		siblings.not(li)[visible ? 'show' : 'fadeOut']();
	}

	var ddmp = $('.drilldownmenuparent').data('i', 0);
	ddmp
		.bind('actionDrilldown', function(event, element, wrapper, obj) {
			ddmp.data('i', $('a', element).data('i'));
		})
		.dcDrilldown({
			speed: 'fast',
			linkType: 'breadcrumb',
			headerTag: 'span',
			showCount: false,
			eventPreventDefault: false,
			horizontal: true
		})
		.hover(function(e) {
			var i = ddmp.data('i'),
				li = lists.eq(i).parent();

			toggleSiblings(li, true);
		}, function() {
			var i = ddmp.data('i'),
				li = lists.eq(i).parent();

			toggleSiblings(li, false);
		});

	var lists = ddmp.find('li a');
	//console.log(lists);
	ddmp.mouseleave();
{/jq}
<style>
	.dd-header, .dd-header > *, .dd-header > ul > li {
		display: block ! important;
		float: left ! important;
	}
	.drilldownmenuparent
	{
		position: absolute ! important;
	}
</style><br />
<div class="dd_container">
	<ul class="drilldownmenuparent">
		<li>
			<a href="">{$home_info.pageName}</a>
			{include file="tiki-user_cssmenu.tpl" drilldownmenu='y'}
		</li>
	</ul>
</div>
