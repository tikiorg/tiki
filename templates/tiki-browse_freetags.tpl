{* $Id$ *}

<h1><a href="tiki-browse_freetags.php" class="pagetitle">{tr}Browse related tags{/tr}</a></h1>

{if $prefs.feature_morcego eq 'y' and $prefs.freetags_feature_3d eq 'y'}

  <div class="morcego_embedded">
    <h2>{tr}Network of Tags related to{/tr}: <span id="currentTag1">{$tag}</span></h2>
      <applet codebase="./lib/wiki3d" archive="morcego-0.4.0.jar" code="br.arca.morcego.Morcego" width="{$prefs.freetags_3d_width}" height="{$prefs.freetags_3d_height}">
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
        <param name="nodeCharge" value="{$freetags_3d_node_charde|default:"1"}">
      </applet>
  </div>
{/if}

	<div align="center">
        <form action="tiki-browse_freetags.php" method="get" style="padding:5px 0;">
          <b>{tr}Tags{/tr}</b> 
          <input type="text" id="tagBox" name="tag" size="25" value="{$tagString|escape}" />
          <a class="linkbut" onclick="clearTags();">{tr}Clear{/tr}</a>
          <input type="submit" value="{tr}Go{/tr}" />
          <br />
          <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
          <input type="radio" name="broaden" value="n"{if $broaden eq 'n'} checked{/if}> 
          <label for="stopb">{tr}With all selected tags{/tr}</label>
          <input type="radio" name="broaden" value="y"{if $broaden eq 'y'} checked{/if}> 
          <label for="stopb">{tr}With one selected tag{/tr}</label>
		  <input type="radio" name="broaden" value="last"{if $broaden eq 'last'} checked{/if}> 
          <label for="stopb">{tr}With last selected tag{/tr}</label>
          <br />
	</div>
  <div width="100%">
      <div width="200" style="vertical-align:top;">
        {if $prefs.freetags_browse_show_cloud eq 'y'}
          <script type="text/javascript">
            {literal}
              function addTag(tag) {
			  if (tag.search(/ /) >= 0) tag = '"'+tag+'"';
	        document.getElementById('tagBox').value = document.getElementById('tagBox').value + ' ' + tag;	
              }
              function clearTags() {
	        document.getElementById('tagBox').value = '';
              }
            {/literal}
          </script>
        
          {foreach from=$most_popular_tags item=popular_tag}
            <a class="freetag_{$popular_tag.size}" href="tiki-browse_freetags.php?tag={$popular_tag.tag}" onclick="javascript:addTag('{$popular_tag.tag|escape:'javascript'}');return false;" onDblClick="location.href=this.href;"{if $popular_tag.color} style="color:{$popular_tag.color}"{/if}>{$popular_tag.tag}</a> 
          {/foreach}
		  <div class="mini">
		  {if empty($maxPopular)}{assign var=maxPopular value=50+$prefs.freetags_browse_amount_tags_in_cloud}{/if}<a href="{$smarty.server.PHP_SELF}?{query maxPopular=$maxPopular tagString=$tagString}">{tr}More Popular Tags{/tr}</a>
		  </div>
		  <div class="mini">
		  {tr}Sort:{/tr}<a href="{$smarty.server.PHP_SELF}?{query tsort_mode=tag_asc}">{tr}Alphabetically{/tr}</a> | <a href="{$smarty.server.PHP_SELF}?{query tsort_mode=count_desc tagString=$tagString}">{tr}By Size{/tr}</a>
		  </div>
		  <div class="mini">
		  <a href="{$smarty.server.PHP_SELF}?{query mode=c tagString=$tagString}">{tr}Cloud{/tr}</a> | <a href="{$smarty.server.PHP_SELF}?{query mode=l tagString=$tagString}">{tr}List{/tr}</a>
		  </div>
        {/if}
      </div>
  
      <div style="vertical-align:top;">
  
        {if $tagString}<h2>{$cantobjects} {tr}results found{/tr}</h2>{/if}
  
{tr}Browse in{/tr}:

  <a class="linkbut" {if $type eq ''} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}">{tr}All{/tr}</a>
  
  {if $prefs.feature_wiki eq 'y'}
    <a class="linkbut" {if $type eq 'wiki page'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=wiki+page">{tr}Wiki pages{/tr}</a>
  {/if}
  
  {if $prefs.feature_galleries eq 'y'}
    <a class="linkbut" {if $type eq 'image gallery'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=image+gallery">{tr}Image galleries{/tr}</a>
    <a class="linkbut" {if $type eq 'image'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=image">{tr}Images{/tr}</a>
  {/if}
  
  {if $prefs.feature_file_galleries eq 'y'}
    <a class="linkbut" {if $type eq 'file gallery'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=file+gallery">{tr}File galleries{/tr}</a>
  {/if}
  
  {if $prefs.feature_blogs eq 'y'}
    <a class="linkbut" {if $type eq 'blog post'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=blog+post">{tr}Blogs{/tr}</a>
  {/if}
  
  {if $prefs.feature_trackers eq 'y'}
    <a class="linkbut" {if $type eq 'tracker'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=tracker">{tr}Trackers{/tr}</a>
  
    <a class="linkbut" {if $type eq 'trackerItem'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=trackerItem">{tr}Trackers Items{/tr}</a>
  {/if}
  
  {if $prefs.feature_quizzes eq 'y'}
    <a class="linkbut" {if $type eq 'quiz'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=quiz">{tr}Quizzes{/tr}</a>
  {/if}
  
  {if $prefs.feature_polls eq 'y'}
    <a class="linkbut" {if $type eq 'poll'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=poll">{tr}Polls{/tr}</a>
  {/if}
  
  {if $prefs.feature_surveys eq 'y'}
    <a class="linkbut" {if $type eq 'survey'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=survey">{tr}Surveys{/tr}</a>
  {/if}
  
  {if $prefs.feature_directory eq 'y'}
    <a class="linkbut" {if $type eq 'directory'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=directory">{tr}Directory{/tr}</a>
  {/if}
  
  {if $prefs.feature_faqs eq 'y'}
    <a class="linkbut" {if $type eq 'faq'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=faq">{tr}FAQs{/tr}</a>
  {/if}
  
  {if $prefs.feature_sheet eq 'y'}
  <a class="linkbut" {if $type eq 'sheet'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=sheet">{tr}Sheets{/tr}</a>
  {/if}
  
  {if $prefs.feature_articles eq 'y'}
    <a class="linkbut" {if $type eq 'article'} id="highlight"{/if} href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=article">{tr}Articles{/tr}</a>
  {/if}   
          <input type="text" name="find" value="{$find}" />
          <input type="submit" value="{tr}Filter{/tr}" />
        </form>

        {if $cantobjects > 0}
            {cycle values="odd,even" print=false}
            {section name=ix loop=$objects}
		<div class="{cycle} freetagitemlist" >
			<h3><a href="{$objects[ix].href}" class="catname">{$objects[ix].name}</a>
			{if $tiki_p_unassign_freetags eq 'y' or $tiki_p_admin eq 'y'}
			<a href="tiki-browse_freetags.php?del=1&amp;tag={$tag}{if $type}&amp;type={$type|escape:'url'}{/if}&amp;typeit={$objects[ix].type|escape:'url'}&amp;itemit={$objects[ix].name|escape:'url'}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
			{/if}</h3>
			<div class="type">
			{tr}{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"|regex_replace:"/tracker [0-9]*/":"tracker item"}{/tr}
			</div>
			<div>
			{$objects[ix].description}&nbsp;
			</div>
		</div>
            {/section}
    
          {pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
        {/if}
      </div>
