
{* this is the very top most box of the feature section in tiki-admin.php?page=features,
 * each td is a cell,each tr is a row, not to be confused with tr-smarty-tag which means translate...
 * there are five cells for every row, the middle cell is empty to keep feature and ckboxes separate
 *}

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}Please see the <a class='rbox-link' target='tikihelp' href='http://doc.tikiwiki.org/tiki-index.php?page=Features'>evaluation of each feature</a> on Tiki's developer site.{/tr}</div>
</div>
<br /> 
 
<form action="tiki-admin.php?page=features" method="post">
  <div class="cbox">
    <div class="cbox-title">
      {tr}{$crumbs[$crumb]->title}{/tr}
      {help crumb=$crumbs[$crumb]}
    </div>
{* the heading of the  box *}
<div class="cbox-data">

{*
 * The following section is typically for features that act like TikiWiki
 * sections and add a configuration icon to the sections list
 *}
<table width="100%" class="admin">
  <tr>
    <td class="heading" colspan="5" align="center">{tr}Tiki sections and features{/tr}</td>
  </tr>
  <tr>
    <td ><input type="checkbox" name="feature_wiki" {if $feature_wiki eq 'y'}checked="checked"{/if}/></td>
    <td class="form" > {if $feature_help eq 'y'}<a href="{$helpurl}Wiki" target="tikihelp" class="tikihelp" title="{tr}Wiki{/tr}">{/if} {tr}Wiki{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td >&nbsp;</td>
    <td><input type="checkbox" name="feature_blogs" {if $feature_blogs eq 'y'}checked="checked"{/if}/></td>
    <td class="form" > {if $feature_help eq 'y'}<a href="{$helpurl}Blogs" target="tikihelp" class="tikihelp" title="{tr}Wiki{/tr}">{/if} {tr}Blogs{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_galleries" {if $feature_galleries eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Image+Galleries" target="tikihelp" class="tikihelp" title="{tr}Image Galleries{/tr}">{/if} {tr}Image Galleries{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_file_galleries" {if $feature_file_galleries eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}File+Galleries" target="tikihelp" class="tikihelp" title="{tr}File Galleries{/tr}">{/if} {tr}File Galleries{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_articles" {if $feature_articles eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Articles" target="tikihelp" class="tikihelp" title="{tr}Articles{/tr}">{/if} {tr}Articles{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_forums" {if $feature_forums eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Forums" target="tikihelp" class="tikihelp" title="{tr}Forums{/tr}">{/if} {tr}Forums{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_faqs" {if $feature_faqs eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}FAQs" target="tikihelp" class="tikihelp" title="{tr}FAQs{/tr}">{/if} {tr}FAQs{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_shoutbox" {if $feature_shoutbox eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=ShoutboxDoc" target="tikihelp" class="tikihelp" title="{tr}Shoutbox{/tr}">{/if} {tr}Shoutbox{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_chat" {if $feature_chat eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Chat" target="tikihelp" class="tikihelp" title="{tr}Chat{/tr}">{/if} {tr}Chat{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_trackers" {if $feature_trackers eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Trackers" target="tikihelp" class="tikihelp" title="{tr}Trackers{/tr}">{/if} {tr}Trackers{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_directory" {if $feature_directory eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Directory" target="tikihelp" class="tikihelp" title="{tr}Directory{/tr}">{/if} {tr}Directory{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_webmail" {if $feature_webmail eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=WebmailDoc" target="tikihelp" class="tikihelp" title="{tr}Webmail{/tr}">{/if} {tr}Webmail{/tr} {if $feature_help eq 'y'}</a>{/if} </td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_polls" {if $feature_polls eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Polls" target="tikihelp" class="tikihelp" title="{tr}Polls{/tr}">{/if} {tr}Polls{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_eph" {if $feature_eph eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Ephemerides" target="tikihelp" class="tikihelp" title="{tr}Ephemerides{/tr}">{/if} {tr}Ephemerides{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_quizzes" {if $feature_quizzes eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Quizzes" target="tikihelp" class="tikihelp" title="{tr}Quizzes{/tr}">{/if} {tr}Quizzes{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td ><input type="checkbox" name="feature_search" {if $feature_search eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Search" target="tikihelp" class="tikihelp" title="{tr}Search{/tr}">{/if} {tr}Search{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_featuredLinks" {if $feature_featuredLinks eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Featured+Links" target="tikihelp" class="tikihelp" title="{tr}Featured Help{/tr}">{/if} {tr}Featured links{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_banners" {if $feature_banners eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Banners" target="tikihelp" class="tikihelp" title="{tr}Banners{/tr}">{/if} {tr}Banners{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_games" {if $feature_games eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=Games" target="tikihelp" class="tikihelp" title="{tr}Games{/tr}">{/if} {tr}Games{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_workflow" {if $feature_workflow eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://workflow.tikiwiki.org/" target="tikihelp" class="tikihelp" title="{tr}Workflow{/tr}">{/if} {tr}Workflow engine{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_newsletters" {if $feature_newsletters eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Newsletters" target="tikihelp" class="tikihelp" title="{tr}Newsletters{/tr}">{/if} {tr}Newsletters{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_live_support" {if $feature_live_support eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/LiveSupportDoc" target="tikihelp" class="tikihelp" title="{tr}Live Support{/tr}">{/if} {tr}Live support system{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
		<td><input type="checkbox" name="feature_minical" {if $feature_minical eq 'y'}checked="checked"{/if}/></td>
		<td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Calendar" target="tikihelp" class="tikihelp" title="{tr}Mini Calendar{/tr}">{/if} {tr}Mini Calendar{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
		<td>&nbsp;</td>
    <td><input type="checkbox" name="feature_surveys" {if $feature_surveys eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=SurveyDoc" target="tikihelp" class="tikihelp" title="{tr}Surveys{/tr}">{/if} {tr}Surveys{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
     </tr>
  <tr>
    <td><input type="checkbox" name="feature_categories" {if $feature_categories eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Categories" target="tikihelp" class="tikihelp" title="{tr}Categories{/tr}">{/if} {tr}Categories{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
		<td><input type="checkbox" name="feature_maps" {if $feature_maps eq 'y'}checked="checked"{/if}/></td>
		<td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Maps" target="tikihelp" class="tikihelp" title="{tr}Maps{/tr}">{/if} {tr}Maps{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_categorypath" {if $feature_categorypath eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {tr}Show Category Path{/tr} </td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_gmap" {if $feature_gmap eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/Gmap" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Google Maps{/tr}">{/if} {tr}Google Maps{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_categoryobjects" {if $feature_categoryobjects eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {tr}Show Category Objects{/tr} </td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_calendar" {if $feature_calendar eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Calendar" target="tikihelp" class="tikihelp" title="{tr}Calendar{/tr}">{/if} {tr}Calendar{/tr} {if $feature_help eq 'y'}</a>{/if}</td></tr>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_modulecontrols" {if $feature_modulecontrols eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=RecipeModuleControls" target="tikihelp" class="tikihelp" title="{tr}Show Module Controls{/tr}">{/if} {tr}Show Module Controls{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_action_calendar" {if $feature_action_calendar eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Action+Calendar" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Action Calendar{/tr}">{/if} {tr}Tiki Calendar{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <tr>
    <td><input type="checkbox" name="feature_mailin" {if $feature_mailin eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Mail-in" target="tikihelp" class="tikihelp" title="{tr}Mail-in{/tr}">{/if} 
{tr}Mail-in{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_view_tpl" {if $feature_view_tpl eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=EditTemplatesDoc" target="tikihelp" class="tikihelp" title="{tr}Template Viewing{/tr}">{/if} {tr}Tiki Template Viewing{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_integrator" {if $feature_integrator eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Tiki+Integrator" target="tikihelp" class="tikihelp" title="{tr}Integrator{/tr}">{/if} {tr}Integrator{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_phplayers" {if $feature_phplayers eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://themes.tikiwiki.org/tiki-index.php?page=PhpLayersMenu" 
target="tikihelp" class="tikihelp" title="{tr}PHPLayers{/tr}">{/if} {tr}PhpLayers Dynamic menus{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_jscalendar" {if $feature_jscalendar eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=JsCalendar" target="tikihelp" class="tikihelp" title="{tr}JsCalendar{/tr}">{/if} {tr}JsCalendar{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_tabs" {if $feature_tabs eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {tr}Use Tabs{/tr} </td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_score" {if $feature_score eq 'y'}checked="checked"{/if} /></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=ScoreSystem" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Score{/tr}">{/if} {tr}Score{/tr}{if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_sheet" {if $feature_sheet eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Spreadsheet" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: 
{tr}TikiSheet{/tr}">{/if} {tr}Tiki Sheet{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr><tr>
    <td><input type="checkbox" name="feature_friends" {if $feature_friends eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=FriendshipNetwork" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Friendship Network{/tr}">{/if} {tr}Friendship Network{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_siteidentity" {if $feature_siteidentity eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://themes.tikiwiki.org/tiki-index.php?page=Using+Site+Identity+Feature" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Site Logo and Identity{/tr}">{/if} {tr}Site Logo and Identity{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr><tr>
    <td><input type="checkbox" name="feature_mobile" {if $feature_mobile eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://mobile.tikiwiki.org/" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Mobile{/tr}">{/if} {tr}Mobile{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_actionlog" {if $feature_actionlog eq 'y'}checked="checked"{/if}/></td>
    <td class="form">{if $feature_help eq 'y'}<a href="{$helpurl}Action Log" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Action Log{/tr}">{/if}{tr}Action log setting{/tr}{if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_freetags" {if $feature_freetags eq 'y'}checked="checked"{/if}/></td>
    <td class="form">{if $feature_help eq 'y'}<a href="{$helpurl}Freetags" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Freetags{/tr}">{/if}{tr}Freetags{/tr}{if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_intertiki" {if $feature_intertiki eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}InterTiki" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Intertiki{/tr}">{/if} {tr}Intertiki{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_morcego" {if $feature_morcego eq 'y'}checked="checked"{/if}/></td>
    <td class="form">{if $feature_help eq 'y'}<a href="{$helpurl}Morcego3D" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Morcego 3D browser{/tr}">{/if}{tr}Morcego 3D browser{/tr}{if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_ajax" {if $feature_ajax eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Ajax" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Ajax{/tr}">{/if} {tr}Ajax{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr><td><input type="checkbox" name="feature_cal_manual_time"
					{if $feature_cal_manual_time eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Calendar manual selection of time/date" target="tikihelp" class="tikihelp" title="{tr}Calendar manual selection of time/date{/tr}">{/if} {tr}Calendar manual selection of time/date{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
	<td>&nbsp;</td>
	<td><input type="checkbox" name="feature_contribution"{if $feature_contribution eq 'y'} checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Contribution" target="tikihelp" class="tikihelp" title="{tr}Contribution{/tr}">{/if} {tr}Contribution{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>

  </tr>
	<tr>
    <td><input type="checkbox" name="feature_wysiwyg" {if $feature_wysiwyg eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Wysiwyg+Editor" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Wysiwyg Editor{/tr}">{/if} {tr}Wysiwyg Editor{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_fullscreen" {if $feature_fullscreen eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Fullscreen" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}:
		{tr}Fullscreen{/tr}">{/if} {tr}Propose a Fullscreen mode{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>

  <tr>
    <td><input type="checkbox" name="feature_help"
            {if $feature_help eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Help+System+Future+Concept" target="tikihelp" class="tikihelp" title="{tr}Help System{/tr}">{/if} {tr}Help System{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td> 
    <td>&nbsp;</td> 
    <td>&nbsp;</td> 
</tr>

{* ---------- Content features ------------ *}
  <tr>
    <td class="heading" colspan="5"
            align="center">{tr}Content Features{/tr}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_hotwords" 
            {if $feature_hotwords eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Hotwords" target="tikihelp" class="tikihelp" title="{tr}Hotwords{/tr}">{/if} {tr}Hotwords{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td class="form">&nbsp;</td>
    <td><input type="checkbox" name="feature_edit_templates"
            {if $feature_edit_templates eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=EditTemplatesDoc" target="tikihelp" class="tikihelp" title="{tr}Edit Templates{/tr}">{/if} {tr}Edit Templates{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_hotwords_nw"
            {if $feature_hotwords_nw eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Hotwords" target="tikihelp" class="tikihelp" title="{tr}Hotwords in New Windows{/tr}">{/if} {tr}Hotwords in New Windows{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td class="form">&nbsp;</td>
    <td><input type="checkbox" name="feature_editcss"
            {if $feature_editcss eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=CssEditDev" target="tikihelp" class="tikihelp" title="{tr}Edit CSS{/tr}">{/if} {tr}Edit CSS{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_custom_home"
            {if $feature_custom_home eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=CustomHome" target="tikihelp" class="tikihelp" title="{tr}Custom Home{/tr}">{/if} {tr}Custom Home{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td class="form">&nbsp;</td>
    <td><input type="checkbox" name="feature_html_pages"
            {if $feature_html_pages eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Html+Pages" target="tikihelp" class="tikihelp" title="{tr}HTML Pages{/tr}">{/if} {tr}HTML pages{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_drawings"
            {if $feature_drawings eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=DrawingsDoc" target="tikihelp" class="tikihelp" title="{tr}Drawings{/tr}">{/if} {tr}Drawings{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td class="form">&nbsp;</td>
    <td><input type="checkbox" name="feature_dynamic_content"
            {if $feature_dynamic_content eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Dynamic+Content" target="tikihelp" class="tikihelp" title="{tr}Dynamic Content System{/tr}">{/if} {tr}Dynamic Content System{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_charts"
            {if $feature_charts eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {tr}Charts{/tr} </td>
    <td class="form">&nbsp;</td>
    <td><input type="checkbox" name="feature_smileys"
            {if $feature_smileys eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=AllowSmileys" target="tikihelp" class="tikihelp" title="{tr}Allow Smileys{/tr}">{/if} {tr}Allow Smileys{/tr} {if $feature_help eq 'y'}</a>{/if} </td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_autolinks"
            {if $feature_autolinks eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=AutoLinks" target="tikihelp" class="tikihelp" title="{tr}AutoLinks{/tr}">{/if} {tr}AutoLinks{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td class="form">&nbsp;</td>
    <td><input type="checkbox" name="feature_use_quoteplugin"
            {if $feature_use_quoteplugin eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {tr}Use Quote plugin rather than &ldquo;>&rdquo; for quoting{/tr} </td>
  </tr>
{* ---------- Administration features ------------ *}
  <tr>
    <td class="heading" colspan="5" 
            align="center">{tr}Administration Features{/tr}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_banning"
            {if $feature_banning eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Banning" target="tikihelp" class="tikihelp" title="{tr}Banning System{/tr}">{/if} {tr}Banning system{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_debug_console"
            {if $feature_debug_console eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://dev.tikiwiki.org/TikiDebuggerConsole" target="tikihelp" class="tikihelp" title="{tr}Debugger Console{/tr}">{/if} {tr}Debugger Console{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_stats"
            {if $feature_stats eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=SiteStatisticsDoc" target="tikihelp" class="tikihelp" title="{tr}Stats{/tr}">{/if} {tr}Stats{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_comm"
            {if $feature_comm eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=CommunicationsCenterDoc" target="tikihelp" class="tikihelp" title="{tr}Communications (send/receive objects){/tr}">{/if} {tr}Communications (send/receive objects){/tr} {if $feature_help eq 'y'}</a>{/if} </td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_theme_control"
            {if $feature_theme_control eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Theme+Control" target="tikihelp" class="tikihelp" title="{tr}Theme Control{/tr}">{/if} {tr}Theme Control{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_xmlrpc"
            {if $feature_xmlrpc eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=XmlrpcApi" target="tikihelp" class="tikihelp" title="{tr}XMLRPC API{/tr}">{/if} {tr}XMLRPC API{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_referer_stats"
            {if $feature_referer_stats eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=SiteStatisticsDoc" target="tikihelp" class="tikihelp" title="{tr}Referer Stats{/tr}">{/if} {tr}Referer Stats{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_contact"
            {if $feature_contact eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=ContactUs" target="tikihelp" class="tikihelp" title="{tr}Contact Us{/tr}">{/if} {tr}Contact Us{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="contact_anon"
            {if $contact_anon eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=ContactUs" target="tikihelp" class="tikihelp" title="{tr}Contact Us{/tr}">{/if} {tr}Contact Us (Anonymous){/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_redirect_on_error"
    	{if $feature_redirect_on_error eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {tr}Redirect On Error{/tr} </td>
    </tr>
{* --- User Features --- *}
  <tr>
    <td class="heading" colspan="5"
            align="center">{tr}User Features{/tr}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_userPreferences"
            {if $feature_userPreferences eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserPreferences" target="tikihelp" class="tikihelp" title="{tr}User Preferences Screen{/tr}">{/if} {tr}User Preferences Screen{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td align="right"><div align="right">
      <input type="checkbox" name="user_assigned_modules"
            {if $user_assigned_modules eq 'y'}checked="checked"{/if}/>
    </div></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}Users+Configure+Modules" target="tikihelp" class="tikihelp" title="{tr}Users can Configure Modules{/tr}">{/if} {tr}Users can Configure Modules{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_user_bookmarks"
            {if $feature_user_bookmarks eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserBookmarkDoc" target="tikihelp" class="tikihelp" title="{tr}User Bookmarks{/tr}">{/if} {tr}User Bookmarks{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td colspan="2"><select name="user_flip_modules">
      <option value="y" {if $user_flip_modules eq 'y'}selected="selected"{/if}>{tr}always{/tr}</option>
      <option value="module" {if $user_flip_modules eq 'module'}selected="selected"{/if}>{tr}module decides{/tr}</option>
      <option value="n" {if $user_flip_modules eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
    </select>
    {if $feature_help eq 'y'}<a href="{$helpurl}Users+Shade+Modules" target="tikihelp" class="tikihelp" title="{tr}Users can Shade Modules{/tr}">{/if} {tr}Users can Shade Modules{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_user_watches"
            {if $feature_user_watches eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}User+Watches" target="tikihelp" class="tikihelp" title="{tr}User Watches{/tr}">{/if} {tr}User Watches{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td align="right"><div align="right">
      <input type="checkbox" name="feature_user_watches_translations"
            {if $feature_user_watches_translations eq 'y'}checked="checked"{/if}/>
    </div></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}User+Watches" target="tikihelp" class="tikihelp" title="{tr}User Watches Translations{/tr}">{/if} {tr}User Watches Translations{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_usermenu"
            {if $feature_usermenu eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserMenuDoc" target="tikihelp" class="tikihelp" title="{tr}User Menu{/tr}">{/if} {tr}User Menu{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td align="right"><div align="right">
      <input type="checkbox" name="feature_tasks"
            {if $feature_tasks eq 'y'}checked="checked"{/if}/>
    </div></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="{$helpurl}User+Tasks" target="tikihelp" class="tikihelp" title="{tr}User Tasks{/tr}">{/if} {tr}User Tasks{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_messages"
            {if $feature_messages eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserMessagesDoc" target="tikihelp" class="tikihelp" title="{tr}User Messages{/tr}">{/if} {tr}User Messages{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td align="right"><div align="right">
      <input type="checkbox" name="feature_userfiles"
            {if $feature_userfiles eq 'y'}checked="checked"{/if}/>
    </div></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserFileDoc" target="tikihelp" class="tikihelp" title="{tr}User Files{/tr}">{/if} {tr}User Files{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_notepad"
            {if $feature_notepad eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserNotepadDoc" target="tikihelp" class="tikihelp" title="{tr}User Notepad{/tr}">{/if} {tr}User Notepad{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td align="right"><div align="right">
      <input type="checkbox" name="feature_contacts"
            {if $feature_contacts eq 'y'}checked="checked"{/if}/>
    </div></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserContacts" target="tikihelp" class="tikihelp" title="{tr}User Contacts{/tr}">{/if} {tr}User Contacts{/tr} {if $feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
  </tr>
	<tr>
    <td><input type="checkbox" name="feature_newsreader" {if $feature_newsreader eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=NewsreaderDoc" target="tikihelp" class="tikihelp" title="{tr}Newsreader{/tr}">{/if} {tr}Newsreader{/tr} {if $feature_help eq 'y'}</a>{/if} </td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>
</table>
{* --- General Layout options --- *}
<table class="admin" width="100%">
<tr>


        <td class="heading" colspan="5" 
            align="center">{tr}General Layout options{/tr}</td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Users+Flip+Columns" target="tikihelp" class="tikihelp" title="{tr}Users can Flip Columns{/tr}">{/if}
        		{tr}Left column{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><select name="feature_left_column">
            <option value="y" {if $feature_left_column eq 'y'}selected="selected"{/if}>{tr}always{/tr}</option>
            <option value="user" {if $feature_left_column eq 'user'}selected="selected"{/if}>{tr}user decides{/tr}</option>
            <option value="n" {if $feature_left_column eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
        </select></td>
        <td>&nbsp;</td>
        <td class="form">{tr}Layout per section{/tr}</td>
        <td><input type="checkbox" name="layout_section"
            {if $layout_section eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td class="form">
	        	{if $feature_help eq 'y'}<a href="{$helpurl}Users+Flip+Columns" target="tikihelp" class="tikihelp" title="{tr}Users can Flip Columns{/tr}">{/if}
        		{tr}Right column{/tr}
        		{if $feature_help eq 'y'}</a>{/if}
        		:</td>
        <td><select name="feature_right_column">
            <option value="y" {if $feature_right_column eq 'y'}selected="selected"{/if}>{tr}always{/tr}</option>
            <option value="user" {if $feature_right_column eq 'user'}selected="selected"{/if}>{tr}user decides{/tr}</option>
            <option value="n" {if $feature_right_column eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
        </select></td>
        <td>&nbsp;</td>
        <td align="center" colspan="2"><a href="tiki-admin_layout.php" 
            class="link">{tr}Admin layout per section{/tr}</a></td>
      </tr><tr>
        <td class="form">{tr}Top bar{/tr}</td>
        <td><input type="checkbox" name="feature_top_bar"
            {if $feature_top_bar eq 'y'}checked="checked"{/if}/></td>
        <td colspan="3">&nbsp;</td>
      </tr><tr>
        <td class="form">{tr}Bottom bar{/tr}</td>
        <td><input type="checkbox" name="feature_bot_bar"
            {if $feature_bot_bar eq 'y'}checked="checked"{/if}/></td>
        <td colspan="3">&nbsp;</td>
      </tr><tr>
      <td class="form">{tr}Bottom bar icons{/tr}</td>
        <td><input type="checkbox" name="feature_bot_bar_icons"
            {if $feature_bot_bar_icons eq 'y'}checked="checked"{/if}/></td>
        <td colspan="3">&nbsp;</td>
      </tr><tr>
        <td class="form">{tr}Bottom bar debug{/tr}</td>
        <td><input type="checkbox" name="feature_bot_bar_debug"
	    {if $feature_bot_bar_debug eq 'y'}checked="checked"{/if}/></td>
	<td colspan="3">&nbsp;</td>
      </tr><tr>
        <td colspan="5" class="button">
          <input type="submit" name="features" value="{tr}Change preferences{/tr}" />
        </td>
      </tr></table>
    </div>
  </div>
</form>
