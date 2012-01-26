/**
 * ==DOMCached local storage library, version 0.1c-jquery==
 * DOMCached is a simple wrapper library for the use of DOM Storage provided by the modern browsers.
 * This library is designed after the hugely popular "memcached" caching system, providing similar
 * "caching" options in JavaScript in the form of local storage.
 *
 * While the original DOM Storage provides only methods to save and read string values, DOMCached
 * can input and output any JSON compatible objects. DOMCached includes also support for namespaces 
 * and data expiring.
 *
 * (c) 2009 Andris Reinman, FlyCom
 *         www.andrisreinman.com
 *
 *  DOMCached is freely distributable under the terms of a MIT-style license.
 *  For details, see the DOMCached web site: http://www.domcached.com/
 * 
 * NB! DOMCached requires jquery-json (http://code.google.com/p/jquery-json/) 
 * to convert from/to JSON strings.
 * 
**/

/**
 * Usage:
 * if(!(key = $.DOMCached.get("key", "my_ns"))){
 *     key = load_data_from_server()
 *     $.DOMCached.set("key","value", false, "my_ns");
 * }
 */

(function($) {

	$.DOMCached = {
		/* Version number */
		version: "0.1c-jquery",
	
		/*
		 * This is the object, that holds the cached values
		 * @param {Object} storage 
		 */
		storage: {},
	
		/*
		 * This is the object, that holds the actual storage object (localStorage or globalStorage['domain'])
		 * @param {Object} storage 
		 */
		storage_service: false,
	
	 	/**
 		 * Initialization function. Detects if the browser supports DOM Storage and behaves accordingly
	 	 * @returns undefined
 		 */
		init: function(){
			if("localStorage" in window){
				this.storage_service = localStorage;
			}else if("globalStorage" in window){
				this.storage_service = globalStorage[document.domain];
			}else{
				/* If the browser is IE7 or older, use userData behavior as a storage instead */
				if("addBehavior" in document.createElement('div')){
					// add a link element to the header, this will be the storage element
					document.write('<link id="elm_domcached" style="behavior:url(#default#userData)"/>');
					$('#elm_domcached')[0].load("domcached");
					try{
					var data = $('#elm_domcached')[0].getAttribute("domcached");
					}catch(E){var data = "{}"}
					this.storage_service = {dom_storage:{}};
					if(data && data.length){
						this.storage_service.dom_storage = data;
					}
				}else{
					return;
				}
			}
			if("dom_storage" in this.storage_service && this.storage_service.dom_storage){
				try{
					this.storage = $.evalJSON(this.storage_service.dom_storage);
				}catch(E){this.storage_service.dom_storage = "{}";}
			}else{
				this.storage_service.dom_storage = "{}";
			}
		},
	
	 	/**
 		 * This functions provides the "save" mechanism to store the DOMCached.storage object to the DOM Storage
	 	 * @returns undefined
 		 */
		save:function(){
			if(this.storage_service){
				try{
					this.storage_service.dom_storage = $.toJSON(this.storage);
				}catch(E){/* probably cache is full, nothing is saved this way*/}
				// If userData is used as the storage engine, additional 
				if($('elm_domcached')){
					try{
						$('#elm_domcached')[0].setAttribute("domcached",this.storage_service.dom_storage);
						$('#elm_domcached')[0].save("domcached");
					}catch(E){/* probably cache is full, nothing is saved this way*/}
				}
			}
		},
	
	 	/**
 		 * Sets a key's value, regardless of previous contents in cache.
	 	 * @param {String} key - Key to set. If this value is not set or not a string an exception is raised.
 		 * @param value - Value to set. This can be any value that is JSON compatible (Numbers, Strings, Objects etc.).
	 	 * @param {number} time - Optional expiration time, either relative number of seconds from current time, or an absolute Unix epoch time in milliseconds.
 		 * @param {String} namespace - An optional namespace for the key. This must be string, otherwise an exception is raised.
	 	 * @returns the used value
 		 */
		set: function(key, value, time, namespace){
			namespace = namespace || 'default';
			time = time || false;
			if(time && time<(new Date()).getTime()){
				time = (new Date()).getTime()+Math.ceil(time);
			}
			if(!key || (typeof key != "string" && typeof key != "number")){
				throw new TypeError('Key name must be string or numeric');
			}
			if(typeof namespace != "string" && typeof namespace != "number"){
				throw new TypeError('Namespace name must be string or numeric');
			}
			if(!this.storage[namespace]){
				this.storage[namespace] = {}
			}
			this.storage[namespace][key] = {value: value, time: time};
			this.save();
			return value || true;
		},
		/**
 	 	* Looks up a key in cache
	 	 * @param {String} key - Key to look up. If this value is not string an exception is raised.
 		 * @param {String} namespace - An optional namespace for the key. This must be string, otherwise an exception is raised.
	 	 * @returns the key value or <null> if not found
 		 */
		get: function(key, namespace){
			namespace = namespace || 'default';
			if(typeof namespace != "string" && typeof namespace != "number"){
				throw new TypeError('Namespace name must be string or numeric');
			}
			if(this.storage[namespace] && this.storage[namespace][key]){
			 	if (this.storage[namespace][key].time && 
			 		this.storage[namespace][key].time<(new Date()).getTime()){
			 			this.deleteKey(key, namespace);
			 			return null;
			 	}
				return this.storage[namespace][key].value;
			}
			return null;
		},
		getNamespace: function (namespace) {
			if (this.storage[namespace]) {
				return this.storage[namespace];
			}
			return null;
		},
		getStorage: function () {
			if (this.storage) {
				return this.storage;
			}
			return null;
		},
		deleteNamespace: function (namespace) {
			if (this.storage[namespace]) {
				delete this.storage[namespace];
				this.save();
				return true;
			}
		},
		/**
 	 	* Deletes a key from cache.
 	 	* @param {String} key - Key to delete. If this value is not string an exception is raised.
 	 	* @param {String} namespace - An optional namespace for the key. This must be string, otherwise an exception is raised.
 	 	* @returns true
 	 	*/
 		'delete': function(key, namespace){
 			return this.deleteKey(key, namespace);
 		},
		deleteKey: function(key, namespace){
			namespace = namespace || 'default';
			if(typeof namespace != "string" && typeof namespace != "number"){
				throw new TypeError('Namespace name must be string or numeric');
			}
			if(this.storage[namespace] && this.storage[namespace][key]){
				delete this.storage[namespace][key];
				for(var i in this.storage[namespace]){
					if(this.storage[namespace].hasOwnProperty(i)){
						this.save();
						return true;
					}
				}
				delete this.storage[namespace];
				this.save();
				return true;
			}
		},
	
 		/**
	 	 * Sets a key's value, if and only if the item is not already in cache.
 		 * @param {String} key - Key to set. If this value is not set or not a string an exception is raised.
	 	 * @param value - Value to set. This can be any value that is JSON compatible (Numbers, Strings, Objects etc.).
 		 * @param {number} time - Optional expiration time, either relative number of seconds from current time, or an absolute Unix epoch time in milliseconds.
	 	 * @param {String} namespace - An optional namespace for the key. This must be string, otherwise an exception is raised.
 		 * @returns the used value or false if the key was already used
	 	 */
		add: function(key, value, time, namespace){
			namespace = namespace || 'default';
			time = time || false;
			if(time && time<(new Date()).getTime()){
				time = (new Date()).getTime()+Math.ceil(time)
			}
			if(!key || (typeof key != "string" && typeof key != "number")){
				throw new TypeError('Key name must be string or numeric');
			}
			if(typeof namespace != "string" && typeof namespace != "number"){
				throw new TypeError('Namespace name must be string or numeric');
			}
			if(!this.storage[namespace]){
				this.storage[namespace] = {}
			}
			if(this.storage[namespace].hasOwnProperty(key)){
				return false;
			}
			this.storage[namespace][key] = {value: value, time: time};
			this.save();
			return value || true;
		},
	
 		/**
	 	 * Replaces a key's value, failing if item isn't already in cache.
 		 * @param {String} key - Key to set. If this value is not set or not a string an exception is raised.
	 	 * @param value - Value to set. This can be any value that is JSON compatible (Numbers, Strings, Objects etc.).
 		 * @param {number} time - Optional expiration time, either relative number of seconds from current time, or an absolute Unix epoch time in milliseconds.
	 	 * @param {String} namespace - An optional namespace for the key. This must be string, otherwise an exception is raised.
 		 * @returns the used value or false if the key was already used
	 	 */
		replace: function(key, value, time, namespace){
			namespace = namespace || 'default';
			time = time || false;
			if(time && time<(new Date()).getTime()){
				time = (new Date()).getTime()+time
			}
			if(!key || (typeof key != "string" && typeof key != "number")){
				throw new TypeError('Key name must be string or numeric');
			}
			if(typeof namespace != "string" && typeof namespace != "number"){
				throw new TypeError('Namespace name must be string or numeric');
			}
			if(!this.storage[namespace]){
				this.storage[namespace] = {}
			}
			if(!this.storage[namespace].hasOwnProperty(key)){
				return false;
			}
			this.storage[namespace][key] = {value: value, time: time};
			this.save();
			return value || true;
		},
	
 		/**
	 	 * Automically increments a key's value.
 		 * @param {String} key - Key to increment. If this value is not set or not a string an exception is raised.
	 	 * @param {Number} delta - Numric value to increment key by, defaulting to 1.
 		 * @param {String} namespace - An optional namespace for the key. This must be string, otherwise an exception is raised.
	 	 * @param {Number} initial_value - An initial value to be used if the key does not yet exist in the cache. Ignored if the key already exists.
 		 * @returns the new value
	 	 */
		incr: function(key, delta, namespace, initial_value){
			namespace = namespace || 'default';
			delta = delta || 1;
			initial_value = initial_value || 0;
			time = time || false;
			if(time && time<(new Date()).getTime()){
				time = (new Date()).getTime()+time
			}
			if(!key || (typeof key != "string" && typeof key != "number")){
				throw new TypeError('Key name must be string or numeric');
			}
			if(typeof namespace != "string" && typeof namespace != "number"){
				throw new TypeError('Namespace name must be string or numeric');
			}
			if(typeof delta != "number"){
				throw new TypeError('Delta value must be number');
			}
			if(!this.storage[namespace]){
				this.storage[namespace] = {}
			}
			if(!this.storage[namespace].hasOwnProperty(key) || typeof this.storage[namespace][key] != "number"){
				this.storage[namespace][key].value = initial_value;
				this.save();
				return initial_value;
			}
			this.storage[namespace][key].value += delta
			this.save();
			return value || true;
		},
	
 		/**
	 	 * Automically decrements a key's value.
 		 * @param {String} key - Key to decrement. If this value is not set or not a string an exception is raised.
	 	 * @param {Number} delta - Numric value to decrement key by, defaulting to 1.
 		 * @param {String} namespace - An optional namespace for the key. This must be string, otherwise an exception is raised.
 	 	* @param {Number} initial_value - An initial value to be used if the key does not yet exist in the cache. Ignored if the key already exists.
	 	 * @returns the new value
 		 */
		decr: function(key, delta, namespace, initial_value){
			namespace = namespace || 'default';
			delta = delta || 1;
			initial_value = initial_value || 0;
			time = time || false;
			if(time && time<(new Date()).getTime()){
				time = (new Date()).getTime()+time
			}
			if(!key || (typeof key != "string" && typeof key != "number")){
				throw new TypeError('Key name must be string or numeric');
			}
			if(typeof namespace != "string" && typeof namespace != "number"){
				throw new TypeError('Namespace name must be string or numeric');
			}
			if(typeof delta != "number"){
				throw new TypeError('Delta value must be number');
			}
			if(!this.storage[namespace]){
				this.storage[namespace] = {}
			}
			if(!this.storage[namespace].hasOwnProperty(key) || typeof this.storage[namespace][key] != "number"){
				this.storage[namespace][key].value = initial_value;
				this.save();
				return initial_value;
			}
			this.storage[namespace][key].value -= delta
			this.save();
			return value || true;
		},
	
		/**
	 	 * Deletes everything in cache.
 		 * @returns true
	 	 */
		flush_all: function(){
			this.storage = {}
			this.save()
			return true;
		}
	}

	/* Initialization */
	$.DOMCached.init();
})(jQuery);