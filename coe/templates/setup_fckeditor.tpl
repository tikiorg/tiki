var _TikiPath = '{$tikipath}' ;
var _TikiRoot = '{$tikiroot}' ;
var _TikiDomain = '{$tikidomain}' ;
var _TikiBaseHost = '{$base_host}' ;
var _FileBrowserLanguage      = 'php' ;
var _QuickUploadLanguage      = 'php' ;
var _FileBrowserExtension     = 'php' ;

FCKConfig.BodyClass = 'wikitext';
FCKConfig.FontNames = 'sans serif;serif;monospace;Arial;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana' ;

FCKConfig.ToolbarSets["Tiki"] = [ 
{foreach item=it from=$toolbar name=lines}
  {foreach item=i from=$it name=item}
  [{foreach item=m from=$i name=im}'{$m}'{if $smarty.foreach.im.index+1 ne $smarty.foreach.im.total},{/if}{/foreach}]{if $smarty.foreach.lines.index+1 ne $smarty.foreach.lines.total},{/if}

  {/foreach}
  {if $smarty.foreach.lines.index+1 ne $smarty.foreach.lines.total}'/',{/if}

{/foreach}
] ;

FCKConfig.StylesXmlPath = _TikiRoot + 'lib/fckeditor_tiki/tikistyles.xml';
FCKConfig.TemplatesXmlPath = _TikiRoot + 'lib/fckeditor_tiki/tikitemplates.xml';

FCKConfig.EditorAreaCSS = _TikiRoot + '{$fckstyle}' ;
{if !empty($fckstyleoption)}FCKConfig.EditorAreaStyles = '' + _TikiRoot + '{$fckstyleoption}';{/if}

FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/{$prefs.wysiwyg_toolbar_skin}/' ;
FCKConfig.DefaultLanguage   = '{$prefs.language}' ;
FCKConfig.AutoDetectLanguage   = {if $prefs.feature_detect_language eq 'y'}true{else}false{/if} ;
FCKConfig.ContentLangDirection = '{if $prefs.feature_bidi eq 'y'}rtl{else}ltr{/if}' ;
FCKConfig.StartupFocus = true ;
FCKConfig.FormatOutput = true ;

{if $prefs.feature_filegals_manager eq 'y'}
FCKConfig.ImageBrowserURL = _TikiRoot + 'tiki-list_file_gallery.php?filegals_manager=txtUrl';
{else}
FCKConfig.ImageBrowserURL = FCKConfig.BasePath + 'filemanager/browser/default/browser.html?Type=Image&Connector=../../connectors/' + _FileBrowserLanguage + '/connector.' + _FileBrowserExtension ;
{/if}

FCKConfig.Plugins.Add( 'tikilink', null, _TikiRoot + 'lib/fckeditor_tiki/plugins/' ) ;
FCKConfig.tikilinkBtn     = "{tr}Insert/Edit an internal wiki link{/tr}" ;
FCKConfig.tikilinkDlgTitle    = "{tr}Tiki Link - Insert internal link{/tr}" ;
FCKConfig.tikilinkDlgName   = "{tr}Wiki Link insert{/tr}" ;
FCKConfig.tikilinkDlgSelection    = "{tr}Please make a selection of text in order to create a link{/tr}" ;

FCKConfig.LinkBrowser = false;
FCKConfig.LinkUpload = false;
FCKConfig.ProcessHTMLEntities = false;

{if $prefs.feature_filegals_manager eq 'y'}
FCKConfig.ImageDlgHideAdvanced = true ;
FCKConfig.ImageDlgHideLink = true ;
{else}
FCKConfig.Plugins.Add( 'tikiimage', null, _TikiRoot + 'lib/fckeditor_tiki/plugins/' ) ;
FCKConfig.tikiimageBtn = "{tr}Insert an image{/tr}" ;
FCKConfig.tikiimageDlgTitle = "{tr}Tiki Image - Insert an image{/tr}" ;
{/if}
FCKConfig.ImageUpload = false ;

FCKConfig.Plugins.Add( 'CleanHTML', null, _TikiRoot + 'lib/fckeditor_tiki/plugins/' );

FCKConfig.Plugins.Add( 'dragresizetable' );

{if $prefs.feature_ajax_autosave eq 'y'}
   //----------------------------------------------------
   // ajaxAutoSave plugin
   FCKConfig.Plugins.Add('ajaxAutoSave','en', _TikiRoot + 'lib/fckeditor_tiki/plugins/') ;

   // --- config settings for the ajaxAutoSave plugin ---
   // URL to post to
   FCKConfig.ajaxAutoSaveTargetUrl = '{$tikiroot}tiki-auto_save.php' ;

   // Enable / Disable Plugin onBeforeUpdate Action
   FCKConfig.ajaxAutoSaveBeforeUpdateEnabled = true ;

   // RefreshTime
   FCKConfig.ajaxAutoSaveRefreshTime = 30 ;

   // Sensitivity to key strokes
   FCKConfig.ajaxAutoSaveSensitivity = 2 ;
{/if}


/*
 * FCKeditor Extension for MediaWiki specific settings.
 */

// When using the modified image dialog you must set this variable. It must
// correspond to $wgScriptPath in LocalSettings.php.
FCKConfig.mwScriptPath = '' ;     

{if $prefs.wysiwyg_htmltowiki eq 'y'}
// Load the extension plugins.
FCKConfig.Plugins.Add( 'mediawiki', 'en,pl',  _TikiRoot + 'lib/fckeditor_tiki/plugins/') ;

FCKConfig.ForcePasteAsPlainText = true ;
FCKConfig.FontFormats	= 'p;h1;h2;h3;h4;h5;h6;pre' ;

FCKConfig.AutoDetectLanguage	= true ;
FCKConfig.DefaultLanguage		= 'en' ;

// FCKConfig.DisableObjectResizing = true ;

FCKConfig.EditorAreaStyles = '\
.FCK__MWTemplate, .FCK__MWRef, .FCK__MWSpecial, .FCK__MWReferences, .FCK__MWMath, .FCK__MWNowiki, .FCK__MWIncludeonly, .FCK__MWNoinclude, .FCK__MWOnlyinclude, .FCK__MWGallery \
{ \
	border: 1px dotted #00F; \
	background-position: center center; \
	background-repeat: no-repeat; \
	vertical-align: middle; \
} \
.FCK__MWTemplate \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_template.gif); \
	width: 20px; \
	height: 15px; \
} \
.FCK__MWRef \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_ref.gif); \
	width: 18px; \
	height: 15px; \
} \
.FCK__MWSpecial \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_special.gif); \
	width: 66px; \
	height: 15px; \
} \
.FCK__MWNowiki \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_nowiki.gif); \
	width: 66px; \
	height: 15px; \
} \
.FCK__MWHtml \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_html.gif); \
	width: 66px; \
	height: 15px; \
} \
.FCK__MWMath \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_math.gif); \
	width: 66px; \
	height: 15px; \
} \
.FCK__MWIncludeonly \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_includeonly.gif); \
	width: 66px; \
	height: 15px; \
} \
.FCK__MWNoinclude \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_noinclude.gif); \
	width: 66px; \
	height: 15px; \
} \
.FCK__MWGallery \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_gallery.gif); \
	width: 66px; \
	height: 15px; \
} \
.FCK__MWOnlyinclude \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_onlyinclude.gif); \
	width: 66px; \
	height: 15px; \
} \
.FCK__MWReferences \
{ \
	background-image: url(' + FCKConfig.PluginsPath + 'mediawiki/images/icon_references.gif); \
	width: 66px; \
	height: 15px; \
} \
' ;
{/if}
