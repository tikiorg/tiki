this.Screencasts = this.Screencasts || {};

Screencasts.Uploader = {
  files: {},
  forms: 1,
  formId: 'screencast-add-form',
  formClass: 'screencast-add-form',
  formsWrapperId: 'screencast-add-wrapper',
  errorWrapperId: 'screencast-error',
  flashClass: 'screencast-flash',
  oggClass: 'screencast-ogg'
};

Screencasts.Uploader.loaderRedirect = {
  loaderTarget: "screencast-loader",
  loaderUrl: "tiki-upload_screencast_ajax.php",
  formId: "editpageform",
  formUrl: "",
  formTarget: ""
};

Screencasts.Uploader.cloneForm = function() {
  $jq("#" + this.formId).clone().appendTo("#" + this.formsWrapperId);
  $jq("." + this.formClass + ":last").attr('id', this.formClass + this.forms);
  $jq("." + this.formClass + ":last > input").each( function() {
    this.value = "";
  });
  
  this.forms++;
};

Screencasts.Uploader.reset = function() {
  var uploader = this;
  $jq("." + this.formClass).each( function () {
    if ( this.id != uploader.formId) {
      this.remove();
    }
  });
  
  $jq("." + this.flashClass).each( function() {
    if ( this.type == "file" )
      this.value = "";
  });

  $jq("." + this.oggClass).each( function() {
    if ( this.type == "file" )
      this.value = "";
  });
  
  $jq("#" + this.errorWrapperId).hide();
};

Screencasts.Uploader.doUpload = function() {
  var p = $jq("#editpageform");
  if ( !p ) return false;
  
  this.loaderRedirect.formUrl = p.attr("action");
  this.loaderRedirect.formTarget = p.attr("target");
  
  p.attr("target", this.loaderRedirect.loaderTarget);
  p.attr("action", this.loaderRedirect.loaderUrl);
  p.submit();
  p.attr("target", this.loaderRedirect.formTarget);
  p.attr("action", this.loaderRedirect.formUrl);
   
};

Screencasts.Player = {
  width: 640,
  height: 480,
  thumbnails: [],
  priority: { 'ogg': 1, 'swf': 2, 'flv': 3 },
  handlers: { 'swf': 'Swf', 'flv': 'Flash', 'ogg': 'Ogg' },
  properNouns: { 'swf': 'Flash', 'flv': 'Flash', 'ogg': 'Ogg Theora' },
  canUseVideo: false,
  isThumb: false,
  thumbWidth: 160,
  thumbHeight: 120,
  thumbText: function() {
    if ( Screencasts.Player.isThumb ) {
      if ( typeof screencastNoPreview == 'undefined') {
        screencastThumbText = 'Insert Screencast';
      }
      return '<div class="screencast-thumb-text">' + screencastThumbText + '</div>';
    }
    
    return "";
  },
  noPreviewText: function() {
    if ( Screencasts.Player.isThumb ) {
      if ( typeof screencastNoPreview == 'undefined' ) {
        screencastNoPreview = 'Preview not possible';
      }
      return '<div class="screencast-no-preview-text">' + screencastNoPreview + '</div>';
    }
    
    return "";
  }
};

// getPageScroll() by quirksmode.com
Screencasts.Player.getPageScroll = function() { 
  var xScroll, yScroll;
  if (self.pageYOffset) {
    yScroll = self.pageYOffset;
    xScroll = self.pageXOffset;
  } else if (document.documentElement && document.documentElement.scrollTop) {// Explorer 6 Strict
    yScroll = document.documentElement.scrollTop;
    xScroll = document.documentElement.scrollLeft;
  } else if (document.body) {// all other Explorers
    yScroll = document.body.scrollTop;
    xScroll = document.body.scrollLeft;
  }
  
  return new Array(xScroll,yScroll);
};

// getPageSize() by Facebox (http://famspam.com/facebox/)
Screencasts.Player.getPageHeight = function() {
  var windowHeight;
  if (self.innerHeight) {     // all except Explorer
    windowHeight = self.innerHeight;
  } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
    windowHeight = document.documentElement.clientHeight;
  } else if (document.body) { // other Explorers
    windowHeight = document.body.clientHeight;
  }
  
  return windowHeight;
};

Screencasts.Player.Flash = {};

/* screencasts.swf is a copy of the 'Normal' player from flv-player.net
 * Documentation can be found at http://flv-player.net/players/normal/documentation/ */
Screencasts.Player.Flash.location = '/lib/screencasts/screencast.swf'; 
Screencasts.Player.Flash.parameters = {
  movie: Screencasts.Player.Flash.location,
  allowFullScreen: true,
  FlashVars: {
    flv: "",
    width: 640,
    height: 480,
    margin: 0,
    showtime: 2
    //showfullscreen: 1, 
    //buffershowbg: 1,
    //showiconplay: 1, 
    //iconplaybgalpha: 50
  }
};

Screencasts.Player.Flash.insert = function(id, file) {
  this.parameters.FlashVars.flv = file;
  this.parameters.FlashVars.width = (Screencasts.Player.isThumb) ? Screencasts.Player.thumbWidth : Screencasts.Player.width;
  this.parameters.FlashVars.height = (Screencasts.Player.isThumb) ? Screencasts.Player.thumbHeight : Screencasts.Player.height;
  
  var player = "<object ";
  player += 'width="' + this.parameters.FlashVars.width + '" ';
  player += 'height="' + this.parameters.FlashVars.height + '" ';
  player += 'type="' + 'application/x-shockwave-flash' + '" ';
  player += 'data="' + this.location + '" ';
  player += '>';
  
  for ( var v in this.parameters ) {
     if ( typeof this.parameters[v] != 'object' ) {
      player += '<param name="' + v + '" value="' +  this.parameters[v] + '" />';
    } else {
      var str = "";
      for ( var x in this.parameters[v] ) {
        str += x + "=" + escape(this.parameters[v][x]) + "&amp;";
      }
      str = str.replace(/&amp;$/, '');
      player += '<param name="' + v + '" value="' +  str  + '" />';
    }
  }

  player += '</object>';
  player += Screencasts.Player.thumbText();
  $jq("#" + id ).html(player);
};


Screencasts.Player.Swf = {};

Screencasts.Player.Swf.parameters = {
  movie: "",
  scale: 'showall'
};

Screencasts.Player.Swf.embed = {
  src: "",
  width: 640,
  height: 495
};

Screencasts.Player.Swf.insert = function(id, file) {
  
  if ( !Screencasts.Player.isThumb ) {
    player = '<object width="' + this.embed.width + '" height="' + this.embed.height + '">';
    this.parameters.movie = file;
    this.embed.src = file;
    /*this.embed.width = Screencasts.Player.width;
    this.embed.height = Screencasts.Player.height;*/
  
    for ( var v in this.parameters ) {
      player += '<param name="' + v + '" value="' +  this.parameters[v] + '" />';
    }
   
    var embedAttr = "";
    for ( var v in this.embed ) {
      embedAttr += v + '="' + this.embed[v] + '" ';
    }
    player += "<embed " + embedAttr + "></embed>";
  
    player +="</object>";
    player += Screencasts.Player.thumbText();  
    $jq("#" + id ).html(player);
  } else {
    $jq("#" + id ).html('<div class="screencast-no-preview"></div>' + Screencasts.Player.noPreviewText() + Screencasts.Player.thumbText());
  }
};

Screencasts.Player.Ogg = {};

Screencasts.Player.Ogg.video = {
  src: "",
  controls: "true",
  height: 480,
  width: 640
};

Screencasts.Player.Ogg.insert = function(id, file) {
  this.video.src = file;
  this.video.width = (Screencasts.Player.isThumb) ? Screencasts.Player.thumbWidth : Screencasts.Player.width;
  this.video.height = (Screencasts.Player.isThumb) ? Screencasts.Player.thumbHeight : Screencasts.Player.height;
  
  var player = "<video ";
  player += 'src="' + this.video.src + '" ';
  player += 'width="' + this.video.width + '" ';
  player += 'height="' + this.video.height + '" ';
  player += 'controls="' + this.video.controls + '" ';
  player += '/>';
  
  player += Screencasts.Player.thumbText();
  
  $jq("#" + id ).html(player);
};

Screencasts.Player.pickBest = function (files) {  
  var list = [];
  for ( var f=0; f < files.length; f++ ) {
    var name = files[f].replace(/(\.[^\.]+)$/, "");
    var type = files[f].replace(/^.+\.([^\.]+)$/, "$1");
    
    if ( !list[name] ) 
      list[name] = [];
    
    list[name].push(type);
  }
  
  for ( var y in list ) {
    if (typeof list[y] !== 'object')
      continue;
    var file = list[y];
    var best = "";
    for ( var x=0; x < file.length; x++ ) {
      if ( !best ) {
        best = file[x];
      } else {
        if ( this.priority[best] < this.priority[file[x]] )
          best = file[x];
      }
    }
    list.push(y + "." + best);
  }
  
  return list;
};

Screencasts.Player.insert = function(id, file, type) {
  try {
    Screencasts.Player[this.handlers[type]].insert(id, file);
  } catch (e) {}
};
    
Screencasts.Player.insertThumbnails = function(id, files) {
  files = Screencasts.Player.pickBest(files);
  this.isThumb = true;
  
  for ( var f = 0; f < files.length; f++ ) {
    if ( $jq.trim(files[f]) != "" ) {
      Screencasts.Player.insertThumbnail(id, files[f]);
    }
  }
  
  this.isThumb = false;
};

Screencasts.Player.insertThumbnail = function(id, file) {
  file = $jq.trim(file);
  this.thumbnails.push(file);
  
  var thumbId = 'screencast-thumbnail' + (this.thumbnails.length-1);
  var type = (file.match(/.+\.([^\.]+)$/)) ? file.replace(/.+\.([^\.]+)$/, "$1") : false;

  if ( type ) {
    var wrapper = $jq("<div></div>").attr('id', thumbId).attr('class', 'screencast-thumbnail');
    wrapper.appendTo("#"+id);
      
    Screencasts.Player.insert(thumbId, file, type, this.thumbWidth, this.thumbHeight);
    $jq("#" + thumbId + " > .screencast-thumb-text").click( function() {
      file = file.replace(/^.+\/([^\/]+)$/, "$1").replace(/^(.+)\.[^\.]+$/, "$1");
      Screencasts.Plugin.insert('editwiki', file);
    });
  }
};

Screencasts.Player.launchPlayer = function(possible_files) {  
  $jq("#screencast-player-overlay").remove();
  $jq("#screencast-player-window").remove();

  var file = this.pickBest(possible_files).pop();
  var type = file.replace(/.+\.([^\.]+)$/, "$1");
  var overlay = $jq("<div></div>").attr("class", "screencast-player-overlay").attr("id", "screencast-player-overlay").appendTo("body");
  var content = $jq("<div></div>").attr("class", "screencast-player-window").attr("id", "screencast-player-window").appendTo("body");

  $jq(overlay).css('height', $jq("body").height() );
  $jq(content).css('top', this.getPageScroll()[1] + (this.getPageHeight() / 45));
  
  $jq("<div></div>").attr("class", "screencast-player-close").html('<span class="screencast-player-close-msg">Close</span>').click( function () {
    $jq("#screencast-player-overlay").remove();
    $jq("#screencast-player-window").remove();
  }).appendTo(content);
  
  $jq("<div></div>").attr("id", "screencast-player-video").appendTo(content);
  
  Screencasts.Player.insert('screencast-player-video', file, type);
  
  if ( possible_files.length > 1 && this.canUseVideo ) {
    var alt_file, alt_type;
    for (var i=0; i < possible_files.length; i++) {
      if ( possible_files[i] == file ) continue;
      alt_file = possible_files[i];
      alt_type = alt_file.replace(/.+\.([^\.]+)$/, "$1");
      break;
    }
    
    $jq("<div></div>").attr("class", "screencast-player-msg").html('View video in ' + this.properNouns[alt_type] + ' format').click( function () {
      Screencasts.Player.launchPlayer([alt_file]);
    }).appendTo(content);
  } else {
    $jq("#screencast-player-video").attr("class", "screencast-no-alt");
  }
};
     
Screencasts.Plugin = {};

Screencasts.Plugin.insert = function(into, file) {
  var syntax = "{SCREENCAST(file=>" + file + ")}{SCREENCAST}";
  insertAt('editwiki', syntax);
};

$jq("document").ready( function() {
  var detect = document.createElement('video');
  if ( typeof detect.canPlayType === 'function' && detect.canPlayType('video/ogg;codecs="theora,vorbis"') == 'probably' ) {
    Screencasts.Player.canUseVideo = true;
    Screencasts.Player.priority.ogg = Screencasts.Player.priority.flv + 2;
  }
  
  if ( $jq("#screencast-insert-wrapper").length && typeof thumb_videos !== 'undefined' ) {
    Screencasts.Player.insertThumbnails('screencast-insert-wrapper', thumb_videos);
  }
  
  if ( typeof videos !== 'undefined' ) {
    $jq(".screencast-content").click( function() {
      Screencasts.Player.launchPlayer(videos[this.id]);
    });
  }
    
  $jq("#screencast-add-another").click( function() {
    Screencasts.Uploader.cloneForm();
    return false;
  });
  
  $jq("#screencast-upload-now").click( function() {
    var uploader = Screencasts.Uploader;
    var u = false;
    
    function fileCheck( f, type, match ) {
      var err = [];
      $jq("." + f ).each( function() {
        if ( this.value ) {
          if ( !this.value.match(match) ) {
            err.push(screencastError(type, 400));
          }
        
          /*if ( type == 'ogg' && this.value && !this.getPrevious().val()) {
            err.push(screencastError(type, 'NEED_FLASH'));
          }*/
          u = true;
        }
      });
      
      return err;  
    }
    
    flashErr = fileCheck(Screencasts.Uploader.flashClass, 'flash', /\.(swf)|(flv)$/);
    oggErr = fileCheck(Screencasts.Uploader.oggClass, 'ogg', /\.ogg$/);
    
    if ( !u ) return false;
    
    if ( flashErr.length || oggErr.length ) {
      var msg = (flashErr.length) ? flashErr[flashErr.length-1] : "";
      msg = (oggErr.length) ? oggErr[oggErr.length-1] : msg;
      
      $jq("#" + Screencasts.Uploader.errorWrapperId).html(msg).focus().show();
      return false;
    }
    
    this.disabled = true;
    Screencasts.Uploader.doUpload(); 
    return false;
  });
  
  $jq("#screencast-loader").load( function() {
    var ie = $jq.browser.msie;
    var response = $jq("#screencast-loader").contents().find('body').html();
    var result = {  
      status: function() { 
        if (!ie) {
          return $jq(response).find('status').html();
        } else {
          return response.replace(/^.*<status>(.*?)<\/status>.*$/i, "$1");
        }
      },
      message: function() {
        if (!ie) {
          return $jq(response).find('message').html();
        } else {
          return response.replace(/^.*<message>(.*?)<\/message>.*$/i, "$1");
        }
      },
      video: function() {
        if (!ie) {
          var videos = [];
          $jq(response).find('video').each( function() {
            videos.push(this.innerHTML);
          });
          return videos;
        } else {
          return response.replace(/^.*<videos>(.*?)<\/videos>.*$/i, "$1").replace(/<video>/gi, "").split(/<\/video>/gi);
        }
      }
    };
  
    if ( result.status() != "201" ) {  
      $jq("#screencast-error").html(result.message());
      $jq("#screencast-error").focus().show();
    } else {
      Screencasts.Uploader.reset();
      var new_videos = result.video();
      if ( new_videos[0] ) {
        $jq("#screencast-insert-wrapper").html("");
        Screencasts.Player.insertThumbnails('screencast-insert-wrapper', new_videos);
        $jq("#screencast-insert-wrapper").show();
        $jq("#screencast-insert-tr").show();
      }
    }
    $jq("#screencast-upload-now").attr("disabled", false);
  });
});

