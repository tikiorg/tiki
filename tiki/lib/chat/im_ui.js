// $Header

// Class IM_UI
// Instant messaging user interface

function IM_UI(div) {
    this.container = div;
    
    this.friends = new Array();
    this.messages = new Array();
    this.unread = new Array();

    this.container.innerHTML = '<div id="im-friendlist"></div>';
    this.friendsDiv = document.getElementById('im-friendlist');

    IM_UI.prototype.setFriends = IM_UI_setFriends;
    IM_UI.prototype.renderFriends = IM_UI_renderFriends;
    IM_UI.prototype.renderMsgBox = IM_UI_renderMsgBox;
    IM_UI.prototype.addMsgs = IM_UI_addMsgs;
    /*
    IM_UI.prototype.xxx = IM_UI_xxx;
    IM_UI.prototype.xxx = IM_UI_xxx;
    IM_UI.prototype.xxx = IM_UI_xxx;
    IM_UI.prototype.xxx = IM_UI_xxx;
    IM_UI.prototype.xxx = IM_UI_xxx;
    IM_UI.prototype.xxx = IM_UI_xxx;
    */
}

function IM_UI_setFriends(friends) {
    friends.sort();

    var oldFriends = this.friends;
    this.friends = friends;

    if (friends.length != oldFriends.length) {
	this.renderFriends();
    } else {
	for(var i=0; i<this.friends.length; i++) {
	    if (this.friends[i] != oldFriends[i]) {
		this.renderFriends();
		break;
	    }
	}
    }
}

function IM_UI_renderFriends() {
    var content = '';
    for (var i=0; i<this.friends.length; i++) {
	var friend = this.friends[i];

	var text;
	if (this.unread[friend.login]) {
	    text = "<b>" + friend.login + " (" + this.unread[friend.login] + ")</b>";
	} else {
	    text = friend.login;
	}

	content += '<div id="im-friend-'+friend.login+'" class="im-friend"><a href="javascript:im_ui.renderMsgBox('+"'"+friend.login+"'"+');">'+text+'</a></div>';
    }
    this.friendsDiv.innerHTML = content;
}

function IM_UI_renderMsgBox(user) {
    if (!this.messages[user]) {
	this.messages[user] = new Array();
    }

    var content = '';
    var msgs = this.messages[user];
    var alt = new Array('odd','even');

    for (var i=0; i < msgs.length; i++) {
	var c = alt[i%2];

	var text = msgs[i]['message'];

	content += '<div class="'+c+'">'+text+'</div>';
    }

    content += "<form onSubmit=\"return im_sendMsg('"+user+"',this);\"><input id=\"im-current-msg\" type=\"text\" name=\"msg\" size=\"40\"></form>";

    this.unread[user] = 0;

    this.renderFriends();

    overlib(content,CAPTION,user,LEFT,STICKY);

    document.getElementById('im-current-msg').focus();
}

function IM_UI_addMsgs(msgs) {
    
    for (var i=0; i<msgs.length; i++) {
	var m = msgs[i];

	if (!this.messages[m.poster]) {
	    this.messages[m.poster] = new Array();
	}

	this.messages[m.poster][this.messages.length] = m;

	if (!this.unread[m.poster]) {
	    this.unread[m.poster] = 0;
	}

	this.unread[m.poster]++;
    }

    this.renderFriends();

    
}
