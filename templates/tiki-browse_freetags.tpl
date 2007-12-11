{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-browse_freetags.tpl,v 1.35.2.4 2007-12-11 12:49:55 pkdille Exp $ *}

{if $prefs.feature_ajax eq 'y'}
  {include file='tiki-ajax_header.tpl' test=$test}
  <script src="lib/freetag/freetag_ajax.js" type="text/javascript"></script>
{/if}

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

<br />
<br />

{if $prefs.feature_ajax eq 'y'}
  <div class="navbar">
    <a class="linkbut {if $type eq ''} highlight{/if}"  href="javascript:setObjectType('','typeAll');" id="typeAll">{tr}All{/tr}</a>

    {if $prefs.feature_wiki eq 'y'}
      <a class="linkbut {if $type eq "wiki page"} highlight{/if}"  href="javascript:setObjectType('wiki page','typeWikiPage');" id="typeWikiPage">{tr}Wiki pages{/tr}</a>
    {/if}

    {if $prefs.feature_galleries eq 'y'}
      <a class="linkbut {if $type eq 'image gallery'} highlight{/if}"  href="javascript:setObjectType('image gallery','typeImageGalleries');" id="typeImageGalleries">{tr}Image galleries{/tr}</a>
    {/if}

    {if $prefs.feature_galleries eq 'y'}
      <a class="linkbut {if $type eq "image"} highlight{/if}"  href="javascript:setObjectType('image','typeImage');" id="typeImage">{tr}Images{/tr}</a>
    {/if}
  
    {if $prefs.feature_file_galleries eq 'y'}
      <a class="linkbut {if $type eq "file gallery"} highlight{/if}"  href="javascript:setObjectType('file gallery','typeFileGallery');" id="typeFileGallery">{tr}File galleries{/tr}</a>
    {/if}

    {if $prefs.feature_blogs eq 'y'}
      <a class="linkbut {if $type eq "blog post"} highlight{/if}"  href="javascript:setObjectType('blog post','typeBlogPost');" id="typeBlogPost">{tr}Blogs{/tr}</a>
    {/if}

    {if $prefs.feature_trackers eq 'y'}
      <a class="linkbut {if $type eq "tracker"} highlight{/if}"  href="javascript:setObjectType('tracker','typeTracker');" id="typeTracker">{tr}Trackers{/tr}</a>
    {/if}
    
    <a class="linkbut {if $type eq "tracker item"} highlight{/if}"  href="javascript:setObjectType('tracker item','typeTrackerItem');" id="typeTrackerItem">{tr}Trackers Items{/tr}</a>
    
    {if $prefs.feature_quizzes eq 'y'}
      <a class="linkbut {if $type eq "quizz"} highlight{/if}"  href="javascript:setObjectType('quizz','typeQuiz');" id="typeQuiz">{tr}Quizzes{/tr}</a>
    {/if}
    
    {if $prefs.feature_polls eq 'y'}
      <a class="linkbut {if $type eq "poll"} highlight{/if}"  href="javascript:setObjectType('poll','typePoll');" id="typePoll">{tr}Polls{/tr}</a>
    {/if}

    {if $prefs.feature_surveys eq 'y'}
      <a class="linkbut {if $type eq "survey"} highlight{/if}"  href="javascript:setObjectType('survey','typeSurvey');" id="typeSurvey">{tr}Surveys{/tr}</a>
    {/if}

    {if $prefs.feature_directory eq 'y'}
      <a class="linkbut {if $type eq "directory"} highlight{/if}"  href="javascript:setObjectType('directory','typeDirectory');" id="typeDirectory">{tr}Directory{/tr}</a>
    {/if}

    {if $prefs.feature_faqs eq 'y'}
      <a class="linkbut {if $type eq "faq"} highlight{/if}"  href="javascript:setObjectType('faq','typeFaq');" id="typeFaq">{tr}FAQs{/tr}</a>
    {/if}

    {if $prefs.feature_sheet eq 'y'}
      <a class="linkbut {if $type eq "sheet"} highlight{/if}"  href="javascript:setObjectType('sheet','typeSheet');" id="typeSheet">{tr}Sheets{/tr}</a>
    {/if}
  
    {if $prefs.feature_articles eq 'y'}
      <a class="linkbut {if $type eq "article"} highlight{/if}"  href="javascript:setObjectType('article','typeArticle');" id="typeArticle">{tr}Articles{/tr}</a>
    {/if}
  </div>

  {include file="tiki-ajax_table.tpl"}
  <script type="text/javascript">listObjects('{$tagString}','{if $broaden}{$broaden}{else}y{/if}');</script>

{else}

  <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}">{tr}All{/tr}</a>
  
  {if $prefs.feature_wiki eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=wiki+page">{if $type eq 'wiki page'}<span class="highlight">{/if}{tr}Wiki pages{/tr}{if $type eq 'wiki page'}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_galleries eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=image+gallery">{if $type eq 'image gallery'}<span class="highlight">{/if}{tr}Image galleries{/tr}{if $type eq 'image gallery'}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_galleries eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=image">{if $type eq 'image'}<span class="highlight">{/if}{tr}Images{/tr}{if $type eq image}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_file_galleries eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=file+gallery">{if $type eq 'file gallery'}<span class="highlight">{/if}{tr}File galleries{/tr}{if $type eq 'file gallery'}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_blogs eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=blog+post">{if $type eq 'blog'}<span class="highlight">{/if}{tr}Blogs{/tr}{if $type eq 'blog'}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_trackers eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=tracker">{if $type eq 'tracker'}<span class="highlight">{/if}{tr}Trackers{/tr}{if $type eq 'tracker'}</span>{/if}</a>
  {/if}
  
  <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=trackerItem">{if $type eq 'trackerItem'}<span class="highlight">{/if}{tr}Trackers Items{/tr}{if $type eq 'trackerItem'}</span>{/if}</a>
  
  {if $prefs.feature_quizzes eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=quiz">{if $type eq 'quiz'}<span class="highlight">{/if}{tr}Quizzes{/tr}{if $type eq 'quiz'}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_polls eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=poll">{if $type eq 'poll'}<span class="highlight">{/if}{tr}Polls{/tr}{if $type eq 'poll'}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_surveys eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=survey">{if $type eq 'survey'}<span class="highlight">{/if}{tr}Surveys{/tr}{if $type eq 'survey'}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_directory eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=directory">{if $type eq 'directory'}<span class="highlight">{/if}{tr}Directory{/tr}{if $type eq 'directory'}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_faqs eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=faq">{if $type eq 'faq'}<span class="highlight">{/if}{tr}FAQs{/tr}{if $type eq 'faq'}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_sheet eq 'y'}
  <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=sheet">{if $type eq 'sheet'}<span class="highlight">{/if}{tr}Sheets{/tr}{if $type eq 'sheet'}</span>{/if}</a>
  {/if}
  
  {if $prefs.feature_articles eq 'y'}
    <a class="linkbut" href="tiki-browse_freetags.php?tag={$tagString}{if $broaden}&amp;broaden={$broaden}{/if}&amp;type=article">{if $type eq 'article'}<span class="highlight">{/if}{tr}Articles{/tr}{if $type eq 'article'}</span>{/if}</a>
  {/if}   

  <br /> 
  <br /> 

  <table width="100%">
    <tr>
      <td width="200">
        {if $prefs.freetags_browse_show_cloud eq 'y'}
          <script type="text/javascript">
            {literal}
              function addTag(tag) {
	        document.getElementById('tagBox').value = document.getElementById('tagBox').value + ' ' + tag;	
              }
              function clearTags() {
	        document.getElementById('tagBox').value = '';
              }
            {/literal}
          </script>
        
          {foreach from=$most_popular_tags item=popular_tag}
            <a class="freetag_{$popular_tag.size}" href="tiki-browse_freetags.php?tag={$popular_tag.tag}" onClick="javascript:addTag('{$popular_tag.tag|escape:'javascript'}');return false;" onDblClick="location.href=this.href;">{$popular_tag.tag}</a> 
          {/foreach}
        {/if}
      </td>
  
      <td>
  
        <h3>{$cantobjects} {tr}results found{/tr}</h3>
        <form action="tiki-browse_freetags.php" method="get" style="padding:5px 0;">
          <b>{tr}Tags{/tr}</b> 
          <input type="text" id="tagBox" name="tag" size="25" value="{$tagString|escape}" />
          <a class="link" onClick="clearTags();">{tr}clear{/tr}</a>
          <br />
          <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
          <input type="checkbox" id="stopb" name="stopbroaden" {if $broaden eq 'n'}checked{/if}> 
          <label for="stopb">{tr}Show only objects with all selected tags{/tr}</label>
          <br />
          {tr}Find:{/tr} 
          <input type="text" name="find" />
          <input type="submit" />
        </form>
  
        {if $cantobjects > 0}
          <table class="normal">
            {cycle values="odd,even" print=false}
            {section name=ix loop=$objects}
              <tr class="{cycle}" >
                <td>
                  {tr}{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"|regex_replace:"/tracker [0-9]*/":"tracker item"}{/tr}
                </td>
                <td>
                  <a href="{$objects[ix].href}" class="catname">{$objects[ix].name}</a>
                </td>
                <td>{$objects[ix].description}&nbsp;</td>
                <td align="right">
                  <a href="tiki-browse_freetags.php?del=1&amp;tag={$tag}{if $type}&amp;type={$type|escape:'url'}{/if}&amp;typeit={$objects[ix].type|escape:'url'}&amp;itemit={$objects[ix].name|escape:'url'}">del</a>
                </td>
              </tr>
            {/section}
          </table>
    
          <br />   
    
          <div class="mini">
            {if $prev_offset >= 0}
              [<a class="prevnext" href="tiki-browse_freetags.php?tag={$tagString|escape:'url'}&find={$find|escape:'url'}&amp;type={$type|escape:'url'}&amp;offset={$prev_offset}{if $broaden}&amp;broaden={$broaden}{/if}">{tr}Prev{/tr}</a>]
              &nbsp;
            {/if}
            {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      
            {if $next_offset >= 0}
              &nbsp;
              [<a class="prevnext" href="tiki-browse_freetags.php?tag={$tagString|escape:'url'}&find={$find|escape:'url'}&amp;type={$type|escape:'url'}&amp;offset={$next_offset}{if $broaden}&amp;broaden={$broaden}{/if}">{tr}Next{/tr}</a>]
            {/if}
      
            {if $prefs.direct_pagination eq 'y'}
              <br />
              {section loop=$cant_pages name=foo}
              {assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
                <a class="prevnext" href="tiki-browse_freetags.php?tag={$tagString|escape:'url'}&find={$find|escape:'url'}&amp;type={$type|escape:'url'}&amp;offset={$selector_offset}{if $broaden}&amp;broaden={$broaden}{/if}">{$smarty.section.foo.index_next}</a>
                &nbsp;
              {/section}
            {/if}
          </div>
        {/if}
      </td>
    </tr>
  </table>
{/if $prefs.feature_ajax}
