{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-browse_freetags.tpl,v 1.6 2005-12-13 22:15:42 amette Exp $ *}

<script src="lib/cpaint/cpaint2.inc.compressed.js" type="text/javascript"></script>
<script src="lib/freetag/freetag_ajax.js" type="text/javascript"></script>

<h1>{tr}Browse related tags{/tr}</h1>

<div class="morcego_embedded">
<h2>{tr}Network of Tags related to{/tr}: <span id="currentTag1">{$tag}</span></h2>
<applet codebase="./lib/wiki3d" archive="morcego-0.4.0.jar" code="br.arca.morcego.Morcego" width="{$wiki_3d_width}" height="{$wiki_3d_height}">
      <param name="serverUrl" value="{$base_url}/tiki-freetag3d_xmlrpc.php">
      <param name="startNode" value="{$tag}">
      <param name="windowWidth" value="{$wiki_3d_width}">
      <param name="windowHeight" value="{$wiki_3d_height}">
      <param name="viewWidth" value="{$wiki_3d_width}">
      <param name="viewHeight" value="{$wiki_3d_height}">
      <param name="navigationDepth" value="{$wiki_3d_navigation_depth}">
      <param name="feedAnimationInterval" value="{$wiki_3d_feed_animation_interval}">
      <param name="controlWindowName" value="tiki">
      
      <param name="showArcaLogo" value="false">
      <param name="showMorcegoLogo" value="false">

      <param name="loadPageOnCenter" value="{$wiki_3d_autoload|default:"true"}">
      
      <param name="cameraDistance" value="{$wiki_3d_camera_distance|default:"200"}">
      <param name="adjustCameraPosition" value="{$wiki_3d_adjust_camera|default:"true"}">

      <param name="fieldOfView" value="{$wiki_3d_fov|default:"250"}">
      <param name="nodeSize" value="{$wiki_3d_node_size|default:"30"}">
      <param name="textSize" value="{$wiki_3d_text_size|default:"40"}">

      <param name="frictionConstant" value="{$wiki_3d_friction_constant|default:"0.4f"}">
      <param name="elasticConstant" value="{$wiki_3d_elastic_constant|default:"0.5f"}">
      <param name="eletrostaticConstant" value="{$wiki_3d_eletrostatic_constant|default:"1000f"}">
      <param name="springSize" value="{$wiki_3d_spring_size|default:"100"}">
      <param name="nodeMass" value="{$wiki_3d_node_mass|default:"5"}">
      <param name="nodeCharge" value="{$wiki_3d_node_charde|default:"1"}">

</applet>
</div>

<h2>Objects tagged <span id="currentTag2">{$tag}</span></h2>
<div class="navbar">
<a class="linkbut {if $type eq ''} highlight{/if}"  href="javascript:setObjectType('','typeAll');" id="typeAll">{tr}All{/tr}</a>
{if $feature_wiki eq 'y'}
<a class="linkbut {if $type eq "wiki page"} highlight{/if}"  href="javascript:setObjectType('wiki page','typeWikiPage');" id="typeWikiPage">{tr}Wiki pages{/tr}</a>
{/if}
{if $feature_galleries eq 'y'}
<a class="linkbut {if $type eq 'image gallery'} highlight{/if}"  href="javascript:setObjectType('image gallery','typeImageGalleries');" id="typeImageGalleries">{tr}Image galleries{/tr}</a>
{/if}
{if $feature_galleries eq 'y'}
<a class="linkbut {if $type eq "image"} highlight{/if}"  href="javascript:setObjectType('image','typeImage');" id="typeImage">{tr}Images{/tr}</a>
{/if}
{if $feature_file_galleries eq 'y'}
<a class="linkbut {if $type eq "file gallery"} highlight{/if}"  href="javascript:setObjectType('file gallery','typeFileGallery');" id="typeFileGallery">{tr}File galleries{/tr}</a>
{/if}
{if $feature_blogs eq 'y'}
<a class="linkbut {if $type eq "blog post"} highlight{/if}"  href="javascript:setObjectType('blog post','typeBlogPost');" id="typeBlogPost">{tr}Blogs{/tr}</a>
{/if}
{if $feature_trackers eq 'y'}
<a class="linkbut {if $type eq "tracker"} highlight{/if}"  href="javascript:setObjectType('tracker','typeTracker');" id="typeTracker">{tr}Trackers{/tr}</a>
{/if}<a class="linkbut {if $type eq "tracker item"} highlight{/if}"  href="javascript:setObjectType('tracker item','typeTrackerItem');" id="typeTrackerItem">{tr}Trackers Items{/tr}</a>
{if $feature_quizzes eq 'y'}
<a class="linkbut {if $type eq "quizz"} highlight{/if}"  href="javascript:setObjectType('quizz','typeQuiz');" id="typeQuiz">{tr}Quizzes{/tr}</a>
{/if}
{if $feature_polls eq 'y'}
<a class="linkbut {if $type eq "poll"} highlight{/if}"  href="javascript:setObjectType('poll','typePoll');" id="typePoll">{tr}Polls{/tr}</a>
{/if}
{if $feature_surveys eq 'y'}
<a class="linkbut {if $type eq "survey"} highlight{/if}"  href="javascript:setObjectType('survey','typeSurvey');" id="typeSurvey">{tr}Surveys{/tr}</a>
{/if}
{if $feature_directory eq 'y'}
<a class="linkbut {if $type eq "directory"} highlight{/if}"  href="javascript:setObjectType('directory','typeDirectory');" id="typeDirectory">{tr}Directory{/tr}</a>
{/if}
{if $feature_faqs eq 'y'}
<a class="linkbut {if $type eq "faq"} highlight{/if}"  href="javascript:setObjectType('faq','typeFaq');" id="typeFaq">{tr}FAQs{/tr}</a>
{/if}
{if $feature_sheet eq 'y'}
<a class="linkbut {if $type eq "sheet"} highlight{/if}"  href="javascript:setObjectType('sheet','typeSheet');" id="typeSheet">{tr}Sheets{/tr}</a>
{/if}
{if $feature_articles eq 'y'}
<a class="linkbut {if $type eq "article"} highlight{/if}"  href="javascript:setObjectType('article','typeArticle');" id="typeArticle">{tr}Articles{/tr}</a>
{/if}
</div>

<div id="objectList"></div>
<script language="JavaScript">listObjects('{$tag}');</script>
