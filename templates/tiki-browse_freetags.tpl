{title admpage=freetags}{tr}Browse related tags{/tr}{/title}

{if $prefs.feature_morcego eq 'y' and $prefs.freetags_feature_3d eq 'y'}
	<div class="morcego_embedded">
		<h2>{tr}Network of Tags related to:{/tr} <span id="currentTag1">{$tag}</span></h2>
		<applet codebase="./lib/wiki3d" archive="morcego-0.6.0.jar" code="br.arca.morcego.Morcego" width="{$prefs.freetags_3d_width}" height="{$prefs.freetags_3d_height}">
			<param name="serverUrl" value="{$base_url}tiki-freetag3d_xmlrpc.php">
			<param name="startNode" value="{$tag}">
			<param name="windowWidth" value="{$prefs.freetags_3d_width}">
			<param name="windowHeight" value="{$prefs.freetags_3d_height}">
			<param name="viewWidth" value="{$prefs.freetags_3d_width}">
			<param name="viewHeight" value="{$prefs.freetags_3d_height}">
			<param name="navigationDepth" value="{$prefs.freetags_3d_navigation_depth}">
			<param name="feedAnimationInterval" value="{$prefs.freetags_3d_feed_animation_interval}">
			<param name="controlWindowName" value="tiki">
			<param name="showArcaLogo" value="false">
			<param name="showMorcegoLogo" value="false">
			<param name="loadPageOnCenter" value="{$prefs.freetags_3d_autoload|default:"true"}">
			<param name="cameraDistance" value="{$prefs.freetags_3d_camera_distance|default:"200"}">
			<param name="adjustCameraPosition" value="{$prefs.freetags_3d_adjust_camera|default:"true"}">
			<param name="fieldOfView" value="{$prefs.freetags_3d_fov|default:"250"}">
			<param name="nodeSize" value="{$prefs.freetags_3d_node_size|default:"30"}">
			<param name="textSize" value="{$prefs.freetags_3d_text_size|default:"40"}">
			<param name="frictionConstant" value="{$prefs.freetags_3d_friction_constant|default:"0.4f"}">
			<param name="elasticConstant" value="{$prefs.freetags_3d_elastic_constant|default:"0.5f"}">
			<param name="eletrostaticConstant" value="{$prefs.freetags_3d_eletrostatic_constant|default:"1000f"}">
			<param name="springSize" value="{$prefs.freetags_3d_spring_size|default:"100"}">
			<param name="nodeMass" value="{$prefs.freetags_3d_node_mass|default:"5"}">
			<param name="nodeCharge" value="{$prefs.freetags_3d_node_charge|default:"1"}">
		</applet>
	</div>
{/if}

<form class="freetagsearch" action="tiki-browse_freetags.php" method="get">
	<div class="freetagskeywords">
		<b>{tr}Tags{/tr}</b> 
		<input type="text" id="tagBox" name="tag" size="25" value="{$tagString|escape}" />
		{button _onclick="clearTags(); return false;" _text="{tr}Clear{/tr}"}
		<input type="submit" value="{tr}Go{/tr}" />
		<br />
		<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
		<input type="radio" name="broaden" id="stopb1" value="n"{if $broaden eq 'n'} checked="checked"{/if} />
		<label for="stopb1">{tr}With all selected tags{/tr}</label>
		<input type="radio" name="broaden" id="stopb2" value="y"{if $broaden eq 'y'} checked="checked"{/if}/>
		<label for="stopb2">{tr}With one selected tag{/tr}</label>
		<input type="radio" name="broaden" id="stopb3" value="last"{if $broaden eq 'last'} checked="checked"{/if} />
		<label for="stopb3">{tr}With last selected tag{/tr}</label>
	</div>

	{if $prefs.freetags_browse_show_cloud eq 'y'}
		{jq notonready=true}
				function addTag(tag) {
					if (tag.search(/ /) >= 0) tag = '"'+tag+'"';
					document.getElementById('tagBox').value = document.getElementById('tagBox').value + ' ' + tag;	
				}
				function clearTags() {
					document.getElementById('tagBox').value = '';
				}
		{/jq}

		<div class="freetaglist"> 
			{foreach from=$most_popular_tags item=popular_tag}
				{capture name=tagurl}{if (strstr($popular_tag.tag, ' '))}"{$popular_tag.tag}"{else}{$popular_tag.tag}{/if}{/capture}
				<a class="freetag_{$popular_tag.size}{if $tag eq $popular_tag.tag|escape} selectedtag{/if}" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}" onclick="javascript:addTag('{$popular_tag.tag|escape:'javascript'}');return false;" ondblclick="location.href=this.href;"{if $popular_tag.color} style="color:{$popular_tag.color}"{/if}>{$popular_tag.tag|escape}</a> 
			{/foreach}
		</div>

		<div class="freetagsort">
			<div class="mini">
				{if empty($maxPopular)}
					{assign var=maxPopular value=50+$prefs.freetags_browse_amount_tags_in_cloud}
				{/if}
				<a class='more' href="{$smarty.server.PHP_SELF}?{query maxPopular=$maxPopular tagString=$tagString}">{tr}More Popular Tags{/tr}</a>
			</div>

			<div class="mini">
				{tr}Sort:{/tr}<a href="{$smarty.server.PHP_SELF}?{query tsort_mode=tag_asc}">{tr}Alphabetically{/tr}</a> | <a href="{$smarty.server.PHP_SELF}?{query tsort_mode=count_desc tagString=$tagString}">{tr}By Size{/tr}</a>
			</div>

			<div class="mini">
				<a href="{$smarty.server.PHP_SELF}?{query mode=c tagString=$tagString}">{tr}Cloud{/tr}</a> | <a href="{$smarty.server.PHP_SELF}?{query mode=l tagString=$tagString}">{tr}List{/tr}</a>
			</div>
		</div>
	{/if}

	{assign var=cpt value=0} 
	{capture name="browse"}
		{tr}Browse in:{/tr}

		{if $type eq $objectType}
			{assign var=thisclass value='highlight'}
		{else}
			{assign var=thisclass value=''}
		{/if}

		{if $broaden eq ''}
			{assign var=thisbroaden value="&amp;broaden=$broaden"}
		{else}
			{assign var=thisbroaden value=''}
		{/if}
	
		{button _text="{tr}All{/tr}" _class=$thisclass href="tiki-browse_freetags.php?tag=$tagString$thisbroaden"}

		{foreach item=objectType from=$objects_with_freetags}
			{foreach item=sect key=key from=$sections_enabled}
				{if isset($sect.objectType) and $sect.objectType eq $objectType and $objectType neq 'blog post'}
					{assign var=feature_label value=$objectType|ucwords}
					{if $type eq $objectType}
						{assign var=thisclass value='highlight'}
					{else}
						{assign var=thisclass value=''}
					{/if}
				
					{if $broaden eq ''}
						{assign var=thisbroaden value="&amp;broaden=$broaden"}
					{else}
						{assign var=thisbroaden value=''}
					{/if}
				
					{assign var=thistype value=$objectType|escape:'url'}
					{capture name="fl"}{tr}{$feature_label}{/tr}{/capture}
					{button _text=$smarty.capture.fl _class=$thisclass href="tiki-browse_freetags.php?tag=$tagString$thisbroaden&amp;type=$thistype"}
					{assign var=cpt value=$cpt+1}
				{/if}

				{if isset($sect.itemObjectType) and $sect.itemObjectType eq $objectType}
					{if $objectType eq 'tracker %d'}
						{assign var=feature_label value='Tracker Item'}
						{assign var=objectType value='trackerItem'}
					{else}
						{assign var=feature_label value=$objectType|ucwords}
					{/if}
				
					{if $type eq $objectType}
						{assign var=thisclass value='highlight'}
					{else}
						{assign var=thisclass value=''}
					{/if}
				
					{if $broaden eq ''}
						{assign var=thisbroaden value="&amp;broaden=$broaden"}
					{else}
						{assign var=thisbroaden value=''}
					{/if}

					{assign var=thistype value=$objectType|escape:'url'}
					{capture name="fl"}{tr}{$feature_label}{/tr}{/capture}
					{button _text=$smarty.capture.fl _class=$thisclass href="tiki-browse_freetags.php?tag=$tagString$thisbroaden&amp;type=$thistype"}
					{assign var=cpt value=$cpt+1}
				{/if}
			{/foreach}
		{/foreach}

		<input type="text" name="find" value="{$find}" />
		<input type="submit" value="{tr}Filter{/tr}" />
	{/capture}
</form>

{if $cpt > 1}
	<div class="freetagsbrowse">{$smarty.capture.browse}</div>{/if}

<div class="freetagresult">
	{if $tagString}
		{if $cantobjects == 0}
			<h2>{tr}No result found{/tr}</h2>
		{elseif $cantobjects == 1}
			<h2>{$cantobjects} {tr}result found{/tr}</h2>
		{elseif $cantobjects > 0}
			<h2>{$cantobjects} {tr}results found{/tr}</h2>
		{/if}
	{/if}
	{if $cantobjects > 0}
		{cycle values="odd,even" print=false}
		{section name=ix loop=$objects}
			<div class="{cycle} freetagitemlist" >
				<h3>
					<a href="{$objects[ix].href}">{$objects[ix].name|strip_tags|escape}</a>
					{if $tiki_p_unassign_freetags eq 'y' or $tiki_p_admin eq 'y'}
						<a href="tiki-browse_freetags.php?del=1&amp;tag={$tag}{if $type}&amp;type={$type|escape:'url'}{/if}&amp;typeit={$objects[ix].type|escape:'url'}&amp;itemit={$objects[ix].name|escape:'url'}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
					{/if}
				</h3>
				<div class="type">
					{tr}{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"|regex_replace:"/tracker [0-9]*/":"tracker item"}{/tr}
				</div>
				<div class="description">
					{$objects[ix].description|strip_tags|escape}&nbsp;
				</div>
			</div>
		{/section}
		{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
	{/if}
</div>
