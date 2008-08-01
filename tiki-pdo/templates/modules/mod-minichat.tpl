{tikimodule title="minichat" name="minichat" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}

<div id='minichatchans' class='minichatchans'></div>
<div id='minichat' class='minichat' style='overflow-x: hidden; overflow-y: scroll; width: 100%; height: {$module_rows}px;'>
</div>

<div style='width: 100%; overflow: hidden;'>
 <form name='minichatinputform' action='javascript:minichatpost();'>
  <input name='minichatinput' id='minichatinput' type='text' style='width: 95%;' autocomplete='off' /><input type='submit' value='Ok' style='display: none;' />
 </form>
</div>
{/tikimodule}

{literal}
<script language='javascript' type='text/javascript'>
var minichat_lasttimeout=2000;

var minichat_firstchan=null;
var minichat_lastchan=null;
var minichat_bigid=0;
var minichat_selectedchan=null;

function minichat_urlencode(str) {
  str = encodeURIComponent(str);
  str = str.replace('+', '%2B');
  str = str.replace('%20', '+');
  str = str.replace('*', '%2A');
  str = str.replace('/', '%2F');
  str = str.replace('@', '%40');
  return str;
}

function minichat_mkurl() {
  var date=new Date();
  var u="tiki-minichat_ajax.php?lasttimeout="+minichat_lasttimeout+"&iebug="+date.getTime();

  u+="&chans=";
  var c=minichat_firstchan;
  while(c) {
	var cname=minichat_urlencode(c.name);
    u+=cname+';'+c.lastid;

    c=c.nxt;
    if (c) u+=',';
  }

  return u;
}

function minichat_loadJS(file) {
	var head = document.getElementsByTagName('head').item(0);
	var scriptTag = document.getElementById('minichat_loadJS');
	if (scriptTag) head.removeChild(scriptTag);
	script = document.createElement('script');
	script.src = file;
	script.type = 'text/javascript';
	script.id = 'minichat_loadJS';
	head.appendChild(script);
}

function minichat_update() {
  var u=minichat_mkurl();
  minichat_loadJS(u);
}

function minichatpost() {
  var obj=document.getElementById('minichatinput');
  var value=minichat_urlencode(obj.value);

  var u=minichat_mkurl();
  if (minichat_selectedchan) u+="&msgon="+minichat_urlencode(minichat_selectedchan.name);
  u+="&msg="+value;

  minichat_loadJS(u);
  obj.value='';
}

function minichat_newelem(type, vals) {
  var elem=document.createElement(type);
  for (key in vals) {
	//elem[key]=vals[key];
	elem.setAttribute(key, vals[key]);
  }
  return elem;
}

function minichat_selectchannel(chan) {
  var c=minichat_lastchan;

  if (minichat_selectedchan) {
    var d=document.getElementById('minichatdiv_'+minichat_selectedchan.id);
    d.style.display="none";

    d=document.getElementById('minichata_'+minichat_selectedchan.id);
    d.setAttribute('class', 'minichata_unselected');

    minichat_selectedchan=null;
  }

  while(c) {
	if (c.name == chan) {
      var d=document.getElementById('minichatdiv_'+c.id);
      d.style.display="";

	  d=document.getElementById('minichata_'+c.id);
	  d.setAttribute('class', 'minichata_selected');

      minichat_selectedchan=c;
    }
    c=c.prv;
  }  

  document.getElementById('minichat').scrollTop=99999;
}

function minichat_addchannel(chan) {
  var c={ prv : null, nxt : null, name : chan, id: minichat_bigid++, lastid: 0 };
  c.prv=minichat_lastchan;
  if (minichat_lastchan) minichat_lastchan.nxt=c;
  minichat_lastchan=c;
  if (!minichat_firstchan) minichat_firstchan=c;

  var d=minichat_newelem("div", { 'id' : 'minichatdiv_'+c.id , 'class' : 'minichatdiv' });
  document.getElementById('minichat').appendChild(d);

  d=minichat_newelem("a", { 'id' : 'minichata_'+c.id, 'class' : 'minichata_unselected', 'href' : "javascript: minichat_selectchannel('"+c.name+"');" });
  d.innerHTML=c.name;
  document.getElementById('minichatchans').appendChild(d);

  minichat_selectchannel(chan);
}

function minichat_removechannel(chan) {
  var c=minichat_lastchan;
  var found=null;
  while(c) {
	if (c.name == chan) {
      found=c;
      if (minichat_firstchan==c) minichat_firstchan=c.nxt;
      if (minichat_lastchan==c) minichat_lastchan=c.prv;
	  if (c.prv) c.prv.nxt=c.nxt;
      if (c.nxt) c.nxt.prv=c.prv;
      
      var d=document.getElementById('minichatdiv_'+c.id);
      document.getElementById('minichat').removeChild(d);      

      d=document.getElementById('minichata_'+c.id);
      document.getElementById('minichatchans').removeChild(d);      

	  break;
    }
    c=c.prv;
  }

  if (found == minichat_selectedchan) {
	minichat_selectedchan=null;
    if (found.prv) minichat_selectchannel(found.prv.name);
	else if (minichat_lastchan) minichat_selectchannel(minichat_lastchan.name);
  }
}

function minichat_updatelastid(chan, lastid) {
  var c=minichat_lastchan;
  while(c) {
	if (c.name == chan) {
	  c.lastid=lastid;
    }
    c=c.prv;
  }
}

function minichat_getchanid(chan) {
  var c=minichat_lastchan;
  while(c) {
	if (c.name == chan) {
	  return c.id;
    }
    c=c.prv;
  }
  return null;
}

{/literal}{$jscode}{literal}

setTimeout('minichat_update()', 20);

</script>
{/literal}
