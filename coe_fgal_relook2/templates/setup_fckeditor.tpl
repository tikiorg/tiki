var _TikiPath = '{$tikipath}' ;
var _TikiRoot = '{$tikiroot}' ;
var _TikiDomain = '{$tikidomain}' ;
var _TikiBaseHost = '{$base_host}' ;
var _FileBrowserLanguage      = 'php' ;
var _QuickUploadLanguage      = 'php' ;
var _FileBrowserExtension     = 'php' ;

FCKConfig.BodyClass = 'wikitext';
FCKConfig.FontNames =  '{$prefs.wysiwyg_fonts}';

FCKConfig.ToolbarSets["Tiki"] = [ 
{if $prefs.feature_ajax == 'y' and $prefs.feature_ajax_autosave eq 'y'}
	['ajaxAutoSave'],
{/if}
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

FCKConfig.Plugins.Add( 'tikiimage', null, _TikiRoot + 'lib/fckeditor_tiki/plugins/' ) ;
FCKConfig.tikiimageBtn = "{tr}Insert an image{/tr}" ;
FCKConfig.tikiimageDlgTitle = "{tr}Tiki Image - Insert an image{/tr}" ;
FCKConfig.Plugins.Add( 'tikiswitch', null, _TikiRoot + 'lib/fckeditor_tiki/plugins/' ) ;
FCKConfig.tikiswitchBtn = "{tr}Switch to normal (wiki) editor{/tr}" ;
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

{if $prefs.wysiwyg_htmltowiki eq 'y'}
	// When using the modified image dialog you must set this variable. It must
	// correspond to $wgScriptPath in LocalSettings.php.
	FCKConfig.mwScriptPath = '' ;     
	// Load the extension plugins.
	FCKConfig.Plugins.Add( 'tikiwiki', 'en,pl',  _TikiRoot + 'lib/fckeditor_tiki/plugins/') ;
	FCKConfig.ForcePasteAsPlainText = true ;
	FCKConfig.FontFormats  = 'p;h1;h2;h3;h4;h5;h6;pre' ;
	FCKConfig.AutoDetectLanguage   = true ;
	// FCKConfig.DisableObjectResizing = true ;
{/if}

// as tikiwiki, we prefer <br> instead of <p> for newlines
FCKConfig.EnterMode = 'br' ;			// p | div | br
FCKConfig.ShiftEnterMode = 'p' ;	// p | div | br
