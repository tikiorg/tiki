/**
 * AjaxQueue - Handling multiple Ajax instances
 * 
 * Wraps several Xhr-instances and manages multiple
 * requests with them.
 * 
 * @version		1.0rc1
 * 
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */
var AjaxQueue = new Class({

	options: {
		defaultData: false,
		max: 3,
		ajaxOptions: {},
		onRequest: Class.empty,
		onSuccess: Class.empty,
		onFailure: Class.empty
	},

	initialize: function(url, options) {
		this.url = url;
		this.setOptions(options);

		this.xhr = [];
		this.data = [];
		this.queue = [];
		this.running = 0;
	},

	request: function(data) {
		var index;
		if (this.queue.length != this.options.max){
			index = this.queue.length;
			var xhr = new Ajax(this.url, this.options.ajaxOptions);
			xhr.addEvent('onSuccess', this.onComplete.pass([index], this));
			xhr.addEvent('onFailure', this.onComplete.pass([index, true], this));
			this.xhr[index] = xhr;
		} else {
			index = this.queue.shift();
			if (this.xhr[index].running) {
				this.xhr[index].cancel();
				this.onComplete(index, true);
			}
		}
		this.queue.push(index);
		this.running++;

		data = $merge(data || {}, this.options.defaultData || {});
		this.data[index] = data;
		this.fireEvent('onRequest', [this.xhr[index], data]);
		this.xhr[index].request(data);
	},

	onComplete: function(index, failed) {
		this.running--;
		var xhr = this.xhr[index], data = this.data[index];
		this.fireEvent('onComplete', [xhr, data]);

		if (failed) {
			this.fireEvent('onFailure', [data]);
		} else {
			var response = xhr.response;
			this.fireEvent('onSuccess', [response.text, response.xml, data, response]);
			xhr.response = null;
		}
		this.queue.remove(index).push(index);
	}

});

AjaxQueue.implement(new Events, new Options);