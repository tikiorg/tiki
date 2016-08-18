{* Currently this needs to be copy and pasted into a wiki page and used as the wiki template for a custom_search plugin *}
{literal}
	<div class="clearfix cs_topbar">
		<b>Any text:</b> {input _filter="content" type="text" class="cs_content_input"} {input type="submit" class="btn btn-default btn-sm" value="Search"}
		Sort by:
		<select class="cs_sortby" name="cs_sortby">
			<option value=""></option>
			<option value="title:asc">Name a-z</option>
			<option value="title:desc">Name z-a</option>
			<option value="price:asc">Price low-high</option>
			<option value="price:desc">Price high-low</option>
		</select>
		Category: <img src="img/trans.png" width="16" height="16" class="badge icon" /> {input value="" class="cs_catlabel" readonly="readonly" style="font-weight: bold;"}<a href="#" class="cs_cat_all">All</a>{input _filter="content" _field="tracker_field_category" value="" type="hidden" class="cs_category"}
		<div class="clearfix cs_catdiv">{/literal}{menu id="44" type="vert"}{literal}</div>
	</div>
	{JQ(notonready=true)}
		(function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'styles/custom_search.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		})();
	{JQ}
{/literal}
