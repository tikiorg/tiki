// $Header

// Class IM_UI
// Instant messaging user interface

function IM_UI(div) {
    this.container = div;
    
    this.friends = new Array();
    this.messages = new Array();

    this.container.innerHTML = '<div id="im-friendlist"></div>';
    this.friendsDiv = document.getElementById('im-friendlist');

    IM_UI.prototype.setFriends = IM_UI_setFriends;
    IM_UI.prototype.renderFriends = IM_UI_renderFriends;
    /*
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

	content += '<div id="im-friend-'+friend.login+'" class="im-friend">'+friend.login+'</div>';
    }
    this.friendsDiv.innerHTML = content;
}




