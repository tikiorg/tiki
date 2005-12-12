{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-browse_freetags.tpl,v 1.1 2005-12-12 22:45:06 lfagundes Exp $ *}

<h1><a class="pagetitle" href="tiki-freetag_list_objects.php">{tr}Tags{/tr}</a></h1>
{tr}Show objects tagged with{/tr} <b>{$tag}</b> {tr}in{/tr}:<br />
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}">{tr}All{/tr}</a>
{if $feature_wiki eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=wiki+page">{if $type eq 'wiki page'}<span class="highlight">{/if}{tr}Wiki pages{/tr}{if $type eq 'wiki page'}</span>{/if}</a>
{/if}
{if $feature_galleries eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=image+gallery">{if $type eq 'image gallery'}<span class="highlight">{/if}{tr}Image galleries{/tr}{if $type eq 'image gallery'}</span>{/if}</a>
{/if}
{if $feature_galleries eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=image">{if $type eq 'image'}<span class="highlight">{/if}{tr}Images{/tr}{if $type eq image}</span>{/if}</a>
{/if}
{if $feature_file_galleries eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=file+gallery">{if $type eq 'file gallery'}<span class="highlight">{/if}{tr}File galleries{/tr}{if $type eq 'file gallery'}</span>{/if}</a>
{/if}
{if $feature_blogs eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=blog+post">{if $type eq 'blog'}<span class="highlight">{/if}{tr}Blogs{/tr}{if $type eq 'blog'}</span>{/if}</a>
{/if}
{if $feature_trackers eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=tracker">{if $type eq 'tracker'}<span class="highlight">{/if}{tr}Trackers{/tr}{if $type eq 'tracker'}</span>{/if}</a>
{/if}<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=trackerItem">{if $type eq 'trackerItem'}<span class="highlight">{/if}{tr}Trackers Items{/tr}{if $type eq 'trackerItem'}</span>{/if}</a>
{if $feature_quizzes eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=quiz">{if $type eq 'quiz'}<span class="highlight">{/if}{tr}Quizzes{/tr}{if $type eq 'quiz'}</span>{/if}</a>
{/if}
{if $feature_polls eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=poll">{if $type eq 'poll'}<span class="highlight">{/if}{tr}Polls{/tr}{if $type eq 'poll'}</span>{/if}</a>
{/if}
{if $feature_surveys eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=survey">{if $type eq 'survey'}<span class="highlight">{/if}{tr}Surveys{/tr}{if $type eq 'survey'}</span>{/if}</a>
{/if}
{if $feature_directory eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=directory">{if $type eq 'directory'}<span class="highlight">{/if}{tr}Directory{/tr}{if $type eq 'directory'}</span>{/if}</a>
{/if}
{if $feature_faqs eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=faq">{if $type eq 'faq'}<span class="highlight">{/if}{tr}FAQs{/tr}{if $type eq 'faq'}</span>{/if}</a>
{/if}
{if $feature_sheet eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=sheet">{if $type eq 'sheet'}<span class="highlight">{/if}{tr}Sheets{/tr}{if $type eq 'sheet'}</span>{/if}</a>
{/if}
{if $feature_articles eq 'y'}
<a class="linkbut" href="tiki-freetag_list_objects.php?tag={$tag}&amp;type=article">{if $type eq 'article'}<span class="highlight">{/if}{tr}Articles{/tr}{if $type eq 'article'}</span>{/if}</a>
{/if}

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

<h3>{$cantobjects} {tr}results found{/tr}</h3>
{if $cantobjects > 0}
<table class="normal">
{cycle values="odd,even" print=false}
{section name=ix loop=$objects}
<tr class="{cycle}" >
<td>{tr}{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"|regex_replace:"/tracker [0-9]*/":"tracker item"}{/tr}</td>
<td><a href="{$objects[ix].href}" class="catname">{$objects[ix].name}</a></td>
<td>{$objects[ix].description}&nbsp;</td>
</tr>
{/section}
</table>
<br />

<div align="center">
  <div class="mini">
    {if $prev_offset >= 0}
      [<a class="prevnext" href="tiki-freetag_list_objects.php?find={$find}&amp;type={$type}&amp;offset={$prev_offset}">{tr}prev{/tr}</a>]&nbsp;
    {/if}
    {tr}Page{/tr}: {$actual_page}/{$cant_pages}
    {if $next_offset >= 0}
      &nbsp;[<a class="prevnext" href="tiki-freetag_list_objects.php?find={$find}&amp;type={$type}&amp;offset={$next_offset}">{tr}next{/tr}</a>]
    {/if}
    {if $direct_pagination eq 'y'}
      <br />
      {section loop=$cant_pages name=foo}
        {assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
        <a class="prevnext" href="tiki-freetag_list_objects.php?find={$find}&amp;type={$type}&amp;offset={$selector_offset}">
          {$smarty.section.foo.index_next}
        </a>&nbsp;
      {/section}
    {/if}
 </div>
</div>
{/if}
