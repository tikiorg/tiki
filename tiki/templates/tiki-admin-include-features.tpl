{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-features.tpl,v 1.112.2.8 2008-01-25 17:23:31 nyloth Exp $ *}
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
 * The following section is typically for features that act like Tikiwiki
 * sections and add a configuration icon to the sections list
 *}
<table width="100%" class="admin">
  <tr>
    <td class="heading" colspan="5" align="center">{tr}Tiki sections and features{/tr}</td>
  </tr>
  <tr>
    <td ><input type="checkbox" name="feature_wiki" {if $prefs.feature_wiki eq 'y'}checked="checked"{/if}/></td>
    <td class="form" > {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Wiki" target="tikihelp" class="tikihelp" title="{tr}Wiki{/tr}">{/if} {tr}Wiki{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td >&nbsp;</td>
    <td><input type="checkbox" name="feature_blogs" {if $prefs.feature_blogs eq 'y'}checked="checked"{/if}/></td>
    <td class="form" > {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Blogs" target="tikihelp" class="tikihelp" title="{tr}Wiki{/tr}">{/if} {tr}Blogs{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_galleries" {if $prefs.feature_galleries eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Image+Galleries" target="tikihelp" class="tikihelp" title="{tr}Image Galleries{/tr}">{/if} {tr}Image Galleries{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_file_galleries" {if $prefs.feature_file_galleries eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}File+Galleries" target="tikihelp" class="tikihelp" title="{tr}File Galleries{/tr}">{/if} {tr}File Galleries{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_articles" {if $prefs.feature_articles eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Articles" target="tikihelp" class="tikihelp" title="{tr}Articles{/tr}">{/if} {tr}Articles{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_forums" {if $prefs.feature_forums eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Forums" target="tikihelp" class="tikihelp" title="{tr}Forums{/tr}">{/if} {tr}Forums{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_faqs" {if $prefs.feature_faqs eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}FAQs" target="tikihelp" class="tikihelp" title="{tr}FAQs{/tr}">{/if} {tr}FAQs{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_shoutbox" {if $prefs.feature_shoutbox eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=ShoutboxDoc" target="tikihelp" class="tikihelp" title="{tr}Shoutbox{/tr}">{/if} {tr}Shoutbox{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="form">&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_trackers" {if $prefs.feature_trackers eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Trackers" target="tikihelp" class="tikihelp" title="{tr}Trackers{/tr}">{/if} {tr}Trackers{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_directory" {if $prefs.feature_directory eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Directory" target="tikihelp" class="tikihelp" title="{tr}Directory{/tr}">{/if} {tr}Directory{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_webmail" {if $prefs.feature_webmail eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Webmail" target="tikihelp" class="tikihelp" title="{tr}Webmail{/tr}">{/if} {tr}Webmail{/tr} {if $prefs.feature_help eq 'y'}</a>{/if} </td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_polls" {if $prefs.feature_polls eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Polls" target="tikihelp" class="tikihelp" title="{tr}Polls{/tr}">{/if} {tr}Polls{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_tell_a_friend" {if $prefs.feature_tell_a_friend eq 'y'}checked="checked"{/if}/></td>
    <td class="form">{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Tell+a+Friend" target="tikihelp" class="tikihelp" title="{tr}Tell a Friend{/tr}">{/if} {tr}Tell a Friend{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_quizzes" {if $prefs.feature_quizzes eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Quizzes" target="tikihelp" class="tikihelp" title="{tr}Quizzes{/tr}">{/if} {tr}Quizzes{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td ><input type="checkbox" name="feature_search" {if $prefs.feature_search eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Search" target="tikihelp" class="tikihelp" title="{tr}Search{/tr}">{/if} {tr}Search{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_featuredLinks" {if $prefs.feature_featuredLinks eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Featured+Links" target="tikihelp" class="tikihelp" title="{tr}Featured Help{/tr}">{/if} {tr}Featured links{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_banners" {if $prefs.feature_banners eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Banners" target="tikihelp" class="tikihelp" title="{tr}Banners{/tr}">{/if} {tr}Banners{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_games" {if $prefs.feature_games eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Games" target="tikihelp" class="tikihelp" title="{tr}Games{/tr}">{/if} {tr}Games{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_workflow" {if $prefs.feature_workflow eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Workflow" target="tikihelp" class="tikihelp" title="{tr}Workflow{/tr}">{/if} {tr}Workflow engine{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_newsletters" {if $prefs.feature_newsletters eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Newsletters" target="tikihelp" class="tikihelp" title="{tr}Newsletters{/tr}">{/if} {tr}Newsletters{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_live_support" {if $prefs.feature_live_support eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Live+Support" target="tikihelp" class="tikihelp" title="{tr}Live Support{/tr}">{/if} {tr}Live support system{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_sefurl" {if $prefs.feature_sefurl eq 'y'}checked="checked"{/if}/></td>
    <td class="form">{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Menu+HOWTO" target="tikihelp" class="tikihelp" title="{tr}Menus{/tr}">{/if} {tr}Sefurl for menu options (()){/tr}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_surveys" {if $prefs.feature_surveys eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Surveys" target="tikihelp" class="tikihelp" title="{tr}Surveys{/tr}">{/if} {tr}Surveys{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
     </tr>
  <tr>
		<td><input type="checkbox" name="feature_categories" {if $prefs.feature_categories eq 'y'}checked="checked"{/if}/></td>
		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Categories" target="tikihelp" class="tikihelp" title="{tr}Categories{/tr}">{/if} {tr}Categories{/tr} {if $prefs.feature_categories eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
		<td><input type="checkbox" name="feature_maps" {if $prefs.feature_maps eq 'y'}checked="checked"{/if}/></td>
		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Maps" target="tikihelp" class="tikihelp" title="{tr}Maps{/tr}">{/if} {tr}Maps{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="form">&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_gmap" {if $prefs.feature_gmap eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/Gmap" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Google Maps{/tr}">{/if} {tr}Google Maps{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="form">&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_calendar" {if $prefs.feature_calendar eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Calendar" target="tikihelp" class="tikihelp" title="{tr}Calendar{/tr}">{/if} {tr}Calendar{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td></tr>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="form"> <a href="tiki-admin.php?page=module">{tr}Show Module Controls{/tr}</a></td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_action_calendar" {if $prefs.feature_action_calendar eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Action+Calendar" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Action Calendar{/tr}">{/if} {tr}Tiki Calendar{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <tr>
    <td><input type="checkbox" name="feature_mailin" {if $prefs.feature_mailin eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Mail-in" target="tikihelp" class="tikihelp" title="{tr}Mail-in{/tr}">{/if} 
{tr}Mail-in{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td class="form"> <a href="tiki-admin.php?page=look"> {tr}Tiki Template Viewing{/tr} </a></td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_integrator" {if $prefs.feature_integrator eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Tiki+Integrator" target="tikihelp" class="tikihelp" title="{tr}Integrator{/tr}">{/if} {tr}Integrator{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_phplayers" {if $prefs.feature_phplayers eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://themes.tikiwiki.org/tiki-index.php?page=PhpLayersMenu" 
target="tikihelp" class="tikihelp" title="{tr}PHPLayers{/tr}">{/if} {tr}PhpLayers Dynamic menus{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_jscalendar" {if $prefs.feature_jscalendar eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=JsCalendar" target="tikihelp" class="tikihelp" title="{tr}JsCalendar{/tr}">{/if} {tr}JsCalendar{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td class="form"> <a href="tiki-admin.php?page=look">{tr}Use Tabs{/tr}</a> </td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_score" {if $prefs.feature_score eq 'y'}checked="checked"{/if} /></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=ScoreSystem" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Score{/tr}">{/if} {tr}Score{/tr}{if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_sheet" {if $prefs.feature_sheet eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Spreadsheet" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: 
{tr}TikiSheet{/tr}">{/if} {tr}Tiki Sheet{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr><tr>
    <td><input type="checkbox" name="feature_friends" {if $prefs.feature_friends eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=FriendshipNetwork" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Friendship Network{/tr}">{/if} {tr}Friendship Network{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_siteidentity" {if $prefs.feature_siteidentity eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://themes.tikiwiki.org/tiki-index.php?page=Using+Site+Identity+Feature" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Site Logo and Identity{/tr}">{/if} {tr}Site Logo and Identity{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr><tr>
    <td><input type="checkbox" name="feature_mobile" {if $prefs.feature_mobile eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://mobile.tikiwiki.org/" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Mobile{/tr}">{/if} {tr}Mobile{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_actionlog" {if $prefs.feature_actionlog eq 'y'}checked="checked"{/if}/></td>
    <td class="form">{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Action Log" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Action Log{/tr}">{/if}{tr}Action log setting{/tr}{if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_freetags" {if $prefs.feature_freetags eq 'y'}checked="checked"{/if}/></td>
    <td class="form">{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Freetags" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Freetags{/tr}">{/if}{tr}Freetags{/tr}{if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_intertiki" {if $prefs.feature_intertiki eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}InterTiki" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Intertiki{/tr}">{/if} {tr}Intertiki{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_morcego" {if $prefs.feature_morcego eq 'y'}checked="checked"{/if}/></td>
    <td class="form">{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Morcego3D" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Morcego 3D browser{/tr}">{/if}{tr}Morcego 3D browser{/tr}{if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_ajax" {if $prefs.feature_ajax eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Ajax" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Ajax{/tr}">{/if} {tr}Ajax{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr><td><input type="checkbox" name="feature_cal_manual_time"
					{if $prefs.feature_cal_manual_time eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Calendar manual selection of time/date" target="tikihelp" class="tikihelp" title="{tr}Calendar manual selection of time/date{/tr}">{/if} {tr}Calendar manual selection of time/date{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
	<td>&nbsp;</td>
	<td><input type="checkbox" name="feature_contribution"{if $prefs.feature_contribution eq 'y'} checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Contribution" target="tikihelp" class="tikihelp" title="{tr}Contribution{/tr}">{/if} {tr}Contribution{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr>

  </tr>
	<tr>
    <td><input type="checkbox" name="feature_wysiwyg" {if $prefs.feature_wysiwyg eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Wysiwyg+Editor" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}: {tr}Wysiwyg editor{/tr}">{/if} {tr}Wysiwyg editor{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_fullscreen" {if $prefs.feature_fullscreen eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Fullscreen" target="tikihelp" class="tikihelp" title="{tr}tikiwiki.org help{/tr}:
		{tr}Fullscreen{/tr}">{/if} {tr}Propose a Fullscreen mode{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr>

  <tr>
    <td><input type="checkbox" name="feature_help"
            {if $prefs.feature_help eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Help+System+Future+Concept" target="tikihelp" class="tikihelp" title="{tr}Help System{/tr}">{/if} {tr}Help System{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td> 
    <td><input type="checkbox" name="feature_copyright"
            {if $prefs.feature_copyright eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Copyright" target="tikihelp" class="tikihelp" title="{tr}Copyright System{/tr}">{/if} {tr}Copyright system{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td> 
    <td>&nbsp;</td> 
</tr>

<tr>
    <td><input type="checkbox" name="feature_purifier" {if $prefs.feature_purifier eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> <a href="{$prefs.helpurl}Purifier" target="tikihelp" class="tikihelp" title="{tr}HTML Purifier{/tr}"> {tr}HTML Purifier (Content is cleaned to XHTML 1.1 Strict on each save){/tr} </td>
    <td>&nbsp;</td> 
    <td><input type="checkbox" name="feature_lightbox" {if $prefs.feature_lightbox eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> <a href="{$prefs.helpurl}Lightbox" target="tikihelp" class="tikihelp" title="{tr}Lightbox{/tr}"> {tr}Lightbox (Javascript modern visual effects on images){/tr} </td>
    <td>&nbsp;</td> 
</tr>

<tr>
    <td><input type="checkbox" name="feature_multimedia" {if $prefs.feature_multimedia eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> <a href="{$prefs.helpurl}Multimedia" target="tikihelp" class="tikihelp" title="{tr}Multimedia{/tr}"> {tr}Multimedia{/tr} </td>
    <td>&nbsp;</td> 
    <td><input type="checkbox" name="feature_mootools" {if $prefs.feature_mootools eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> <a href="{$prefs.helpurl}Mootools" target="tikihelp" class="tikihelp" title="{tr}Mootools{/tr}"> {tr}Mootools{/tr}</td> 
    <td>&nbsp;</td> 
</tr>
<tr>
    <td><input type="checkbox" name="feature_swffix" {if $prefs.feature_swffix eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> <a href="{$prefs.helpurl}Swffix" target="tikihelp" class="tikihelp" title="{tr}Swffix{/tr}"> {tr}Swffix{/tr} </td>
    <td>&nbsp;</td> 
    <td>&nbsp;</td>
    <td class="form">&nbsp;</td>
    <td>&nbsp;</td> 
</tr>
{* ---------- Content features ------------ *}
  <tr>
    <td class="heading" colspan="5"
            align="center">{tr}Content Features{/tr}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_hotwords" 
            {if $prefs.feature_hotwords eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Hotwords" target="tikihelp" class="tikihelp" title="{tr}Hotwords{/tr}">{/if} {tr}Hotwords{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td class="form">&nbsp;</td>
    <td><input type="checkbox" name="feature_edit_templates"
            {if $prefs.feature_edit_templates eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=EditTemplatesDoc" target="tikihelp" class="tikihelp" title="{tr}Edit Templates{/tr}">{/if} {tr}Edit Templates{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_hotwords_nw"
            {if $prefs.feature_hotwords_nw eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Hotwords" target="tikihelp" class="tikihelp" title="{tr}Hotwords in New Windows{/tr}">{/if} {tr}Hotwords in New Windows{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td class="form">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="form"> &nbsp;</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_custom_home"
            {if $prefs.feature_custom_home eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=CustomHome" target="tikihelp" class="tikihelp" title="{tr}Custom Home{/tr}">{/if} {tr}Custom Home{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td class="form">&nbsp;</td>
    <td><input type="checkbox" name="feature_html_pages"
            {if $prefs.feature_html_pages eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Html+Pages" target="tikihelp" class="tikihelp" title="{tr}HTML Pages{/tr}">{/if} {tr}HTML pages{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_drawings"
            {if $prefs.feature_drawings eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=DrawingsDoc" target="tikihelp" class="tikihelp" title="{tr}Drawings{/tr}">{/if} {tr}Drawings{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td class="form">&nbsp;</td>
    <td><input type="checkbox" name="feature_dynamic_content"
            {if $prefs.feature_dynamic_content eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Dynamic+Content" target="tikihelp" class="tikihelp" title="{tr}Dynamic Content System{/tr}">{/if} {tr}Dynamic Content System{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_charts"
            {if $prefs.feature_charts eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {tr}Charts{/tr} </td>
    <td class="form">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="form"> <a href="tiki-admin.php?page=textarea">{tr}Allow Smileys{/tr}</a> </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="form"> <a href="tiki-admin.php?page=textarea">{tr}Allow Smileys{/tr}</a></td>
    <td class="form">&nbsp;</td>
    <td><input type="checkbox" name="feature_use_quoteplugin"
            {if $prefs.feature_use_quoteplugin eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {tr}Use Quote plugin rather than &ldquo;>&rdquo; for quoting{/tr} </td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_filegals_manager"
            {if $prefs.feature_filegals_manager eq 'y'}checked="checked"{/if}/></td>
    <td class="form">{tr}Use File Galleries for images inclusion{/tr}</td>
  </tr>
{* ---------- Administration features ------------ *}
  <tr>
    <td class="heading" colspan="5" 
            align="center">{tr}Administration Features{/tr}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_banning"
            {if $prefs.feature_banning eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Banning" target="tikihelp" class="tikihelp" title="{tr}Banning System{/tr}">{/if} {tr}Banning system{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_debug_console"
            {if $prefs.feature_debug_console eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://dev.tikiwiki.org/TikiDebuggerConsole" target="tikihelp" class="tikihelp" title="{tr}Debugger Console{/tr}">{/if} {tr}Debugger Console{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_stats"
            {if $prefs.feature_stats eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=SiteStatisticsDoc" target="tikihelp" class="tikihelp" title="{tr}Stats{/tr}">{/if} {tr}Stats{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_comm"
            {if $prefs.feature_comm eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=CommunicationsCenterDoc" target="tikihelp" class="tikihelp" title="{tr}Communications (send/receive objects){/tr}">{/if} {tr}Communications (send/receive objects){/tr} {if $prefs.feature_help eq 'y'}</a>{/if} </td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="form"> <a href="tiki-admin.php?page=look">{tr}Theme Control{/tr}</a></td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_xmlrpc"
            {if $prefs.feature_xmlrpc eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=XmlrpcApi" target="tikihelp" class="tikihelp" title="{tr}XMLRPC API{/tr}">{/if} {tr}XMLRPC API{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="feature_referer_stats"
            {if $prefs.feature_referer_stats eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=SiteStatisticsDoc" target="tikihelp" class="tikihelp" title="{tr}Referer Stats{/tr}">{/if} {tr}Referer Stats{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_contact"
            {if $prefs.feature_contact eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=ContactUs" target="tikihelp" class="tikihelp" title="{tr}Contact Us{/tr}">{/if} {tr}Contact Us{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    </tr>
  <tr>
    <td><input type="checkbox" name="contact_anon"
            {if $prefs.contact_anon eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=ContactUs" target="tikihelp" class="tikihelp" title="{tr}Contact Us{/tr}">{/if} {tr}Contact Us (Anonymous){/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td><input type="checkbox" name="feature_redirect_on_error"
    	{if $prefs.feature_redirect_on_error eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {tr}Redirect On Error{/tr} </td>
    </tr>
{* --- User Features --- *}
  <tr>
    <td class="heading" colspan="5"
            align="center">{tr}User Features{/tr}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_mytiki" {if $prefs.feature_mytiki eq 'y'}checked="checked"{/if}/></td>
    <td colspan="4" class="form"> {tr}Display 'MyTiki' in the application menu{/tr} </td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_userPreferences"
            {if $prefs.feature_userPreferences eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserPreferences" target="tikihelp" class="tikihelp" title="{tr}User Preferences Screen{/tr}">{/if} {tr}User Preferences Screen{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td></td>
    <td class="form"><a href="tiki-admin.php?page=module">Modules</a></td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_user_bookmarks"
            {if $prefs.feature_user_bookmarks eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserBookmarkDoc" target="tikihelp" class="tikihelp" title="{tr}User Bookmarks{/tr}">{/if} {tr}User Bookmarks{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td colspan="2"></td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_user_watches"
            {if $prefs.feature_user_watches eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}User+Watches" target="tikihelp" class="tikihelp" title="{tr}User Watches{/tr}">{/if} {tr}User Watches{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td align="right"><div align="right">
      <input type="checkbox" name="feature_user_watches_translations"
            {if $prefs.feature_user_watches_translations eq 'y'}checked="checked"{/if}/>
    </div></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}User+Watches" target="tikihelp" class="tikihelp" title="{tr}User Watches Translations{/tr}">{/if} {tr}User Watches Translations{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_usermenu"
            {if $prefs.feature_usermenu eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserMenuDoc" target="tikihelp" class="tikihelp" title="{tr}User Menu{/tr}">{/if} {tr}User Menu{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td align="right"><div align="right">
      <input type="checkbox" name="feature_tasks"
            {if $prefs.feature_tasks eq 'y'}checked="checked"{/if}/>
    </div></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}User+Tasks" target="tikihelp" class="tikihelp" title="{tr}User Tasks{/tr}">{/if} {tr}User Tasks{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_messages"
            {if $prefs.feature_messages eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserMessagesDoc" target="tikihelp" class="tikihelp" title="{tr}User Messages{/tr}">{/if} {tr}User Messages{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td align="right"><div align="right">
      <input type="checkbox" name="feature_userfiles"
            {if $prefs.feature_userfiles eq 'y'}checked="checked"{/if}/>
    </div></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserFileDoc" target="tikihelp" class="tikihelp" title="{tr}User Files{/tr}">{/if} {tr}User Files{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_notepad"
            {if $prefs.feature_notepad eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserNotepadDoc" target="tikihelp" class="tikihelp" title="{tr}User Notepad{/tr}">{/if} {tr}User Notepad{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
    <td align="right"><div align="right">
      <input type="checkbox" name="feature_contacts" {if $prefs.feature_contacts eq 'y'}checked="checked"{/if}/>
    </div></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserContacts" target="tikihelp" class="tikihelp" title="{tr}User Contacts{/tr}">{/if} {tr}User Contacts{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input type="checkbox" name="feature_newsreader" {if $prefs.feature_newsreader eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Newsreader" target="tikihelp" class="tikihelp" title="{tr}Newsreader{/tr}">{/if} {tr}Newsreader{/tr} {if $prefs.feature_help eq 'y'}</a>{/if} </td>
		<td>&nbsp;</td>
    <td align="right"><div align="right">
      <input type="checkbox" name="feature_userlevels" {if $prefs.feature_userlevels eq 'y'}checked="checked"{/if}/>
    </div></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="http://tikiwiki.org/tiki-index.php?page=UserLevels" target="tikihelp" class="tikihelp" title="{tr}User Levels{/tr}">{/if} {tr}User Levels{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
    <td>&nbsp;</td>
		</tr>
		<tr>
    <td><input type="checkbox" name="feature_minical" {if $prefs.feature_minical eq 'y'}checked="checked"{/if}/></td>
    <td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Calendar" target="tikihelp" class="tikihelp" title="{tr}Mini Calendar{/tr}">{/if} {tr}Mini Calendar{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
  </tr>
  <tr>
        <td colspan="5" class="button">
          <input type="submit" name="features" value="{tr}Save{/tr}" />
        </td>
      </tr></table>
    </div>
  </div>
</form>
