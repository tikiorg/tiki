
(function () {
	var mapNumber = 0;

	function delayedExecutor(delay, callback)
	{
		var timeout;

		return function () {
			if (timeout) {
				clearTimeout(timeout);
				timeout = null;
			}

			timeout = setTimeout(callback, delay);
		};
	}

	function getBaseLayers()
	{
		var layers = [], tiles = jqueryTiki.mapTileSets, factories = {
			openstreetmap: function () {
				return new OpenLayers.Layer.OSM();
			},
			openaerialmap: function () {
				return new OpenLayers.Layer.XYZ(
					"OpenAerialMap",
					"http://tile.openaerialmap.org/tiles/1.0.0/openaerialmap-900913/${z}/${x}/${y}.png",
					{sphericalMercator: true}
				);
			},
			google_street: function () {
				return new OpenLayers.Layer.Google(
					"Google Streets",
					{}
				);
			},
			google_satellite: function () {
				return new OpenLayers.Layer.Google(
					"Google Satellite",
					{type: google.maps.MapTypeId.SATELLITE}
				);
			},
			google_hybrid: function () {
				return new OpenLayers.Layer.Google(
					"Google Hybrid",
					{type: google.maps.MapTypeId.HYBRID}
				);
			},
			google_physical: function () {
				return new OpenLayers.Layer.Google(
					"Google Physical",
					{type: google.maps.MapTypeId.TERRAIN}
				);
			},
			blank: function () {
				// Fake layer to hide all tiles
				var layer = new OpenLayers.Layer.OSM(tr('Blank'));
				layer.isBlank = true;
				return layer;
			/* Needs additional testing
			},
			visualearth_road: function () {
				return new OpenLayers.Layer.VirtualEarth(
					"Virtual Earth Roads",
					{'type': VEMapStyle.Road}
				);
			*/
			}
		};

		if (tiles.length === 0) {
			tiles.push('openstreetmap');
		}

		$.each(tiles, function (k, name) {
			var f = factories[name];

			if (f) {
				layers.push(f());
			}
		});

		return layers;
	}

	function parseCoordinates(value) {
		var matching = value.match(/^(-?[0-9]*(\.[0-9]+)?),(-?[0-9]*(\.[0-9]+)?)(,(.*))?$/);
		
		if (matching) {
			var lat = parseFloat(matching[3]);
			var lon = parseFloat(matching[1]);
			var zoom = matching[6] ? parseInt(matching[6], 10) : 0;

			return {lat: lat, lon: lon, zoom: zoom};
		}

		return null;
	}

	function writeCoordinates(lonlat, map, fixProjection) {
		var original = lonlat;

		if (fixProjection) {
			lonlat = lonlat.transform(
				map.getProjectionObject(),
				new OpenLayers.Projection("EPSG:4326")
			);

			if (lonlat.lon < 0.01 && lonlat.lat < 0.01) {
				lonlat = original;
			}
		}

		return lonlat.lon + ',' + lonlat.lat + ',' + map.getZoom();
	}

	$.fn.createMap = function () {
		this.each(function () {
			var id = $(this).attr('id'), container = this, desiredControls;
			$(container).css('background', 'white');
			desiredControls = $(this).data('map-controls');
			if (desiredControls === undefined) {
				desiredControls = 'controls,layers,search_location,current_location,streetview';
			}

			desiredControls = desiredControls.split(',');

			var setupHeight = function () {
				var height = $(container).height();
				if (0 === height) {
					height = $(container).width() / 4.0 * 3.0;
				}

				$(container).closest('.height-size').each(function () {
					height = $(this).data('available-height');
					$(this).css('padding', 0);
					$(this).css('margin', 0);
				});

				$(container).height(height);
			};
			setupHeight();

			$(window).resize(setupHeight);

			if (! id) {
				++mapNumber;
				id = 'openlayers' + mapNumber;
				$(this).attr('id', id);
			}

			setTimeout(function () {
				OpenLayers.ImgPath = "img/openlayers/dark/";
				var map = container.map = new OpenLayers.Map(id, { controls: [] });
				var layers = getBaseLayers();
				container.defaultStyleMap = new OpenLayers.StyleMap({
					"default": new OpenLayers.Style({
						fillColor: "${color}",
						strokeColor: "${color}",
						strokeDashstyle: 'solid',
						strokeWidth: 2,
						graphicZIndex: 2,
						pointRadius: 5,
						fillOpacity: 0.5
					}),
					"select": new OpenLayers.Style({
						externalGraphic: "http://www.openlayers.org/dev/img/marker-gold.png",
						graphicWidth: 21,
						graphicHeight: 25,
						graphicOpacity: 0.9,
						fillColor: "${color}",
						strokeColor: "${color}",
						strokeDashstyle: 'dash',
						strokeLinecap: 'square',
						strokeWidth: 3,
						graphicZIndex: 2,
						pointRadius: 10,
						fillOpacity: 0.6
					}),
					"marker": new OpenLayers.Style({
						externalGraphic: "${url}",
						graphicWidth: "${width}",
						graphicHeight: "${height}",
						graphicOpacity: 0.9
					})
				});
				container.layer = layers[0];
				container.vectors = new OpenLayers.Layer.Vector("Editable", {
					styleMap: container.defaultStyleMap
				});
				container.uniqueMarkers = {};
				container.layers = {};
				map.addLayers(layers);
				map.addLayer(container.vectors);

				container.resetPosition = function () {
					map.setCenter(new OpenLayers.LonLat(0, 0), 3);
				};
				container.resetPosition();

				function setupLayerEvents(vectors) {
					vectors.events.on({
						featureunselected: function (event) {
							if (event.feature.executor) {
								event.feature.executor();
							}
							if (event.feature.attributes.intent) {
								event.feature.renderIntent = event.feature.attributes.intent;
								vectors.redraw();
							}
							$(container).setMapPopup(null);
						},
						featuremodified: function (event) {
							if (event.feature.executor) {
								event.feature.executor();
							}
						},
						beforefeatureadded: function (event) {
							if (! event.feature.attributes.color) {
								event.feature.attributes.color = '#6699cc';
							}
						}
					});
				}

				setupLayerEvents(container.vectors);

				container.modeManager = {
					modes: [],
					activeMode: null,
					addMode: function (options) {
						var mode = $.extend({
							name: tr('Default'),
							icon: null,
							events: {
								activate: [],
								deactivate: []
							},
							controls: [],
							layers: []
						}, options);

						$.each(mode.layers, function (k, layer) {
							layer.displayInLayerSwitcher = false;
							layer.setVisibility(false);
							map.addLayer(layer);
						});

						$.each(mode.controls, function (k, control) {
							control.autoActivate = false;
							map.addControl(control);
						});

						this.modes.push(mode);

						this.register('activate', mode.name, mode.activate);
						this.register('deactivate', mode.name, mode.deactivate);

						if (! this.activeMode) {
							this.activate(mode);
						}

						$(container).trigger('modechanged');

						return mode;
					},
					switchTo: function (modeName) {
						var manager = this;
						$.each(this.modes, function (k, mode) {
							if (mode.name === modeName) {
								manager.activate(mode);
							}
						});
					},
					register: function (eventName, modeName, callback) {
						$.each(this.modes, function (k, mode) {
							if (mode.name === modeName && callback) {
								mode.events[eventName].push(callback);
							}
						});
					},
					activate: function (mode) {
						if (this.activeMode) {
							this.deactivate();
						}

						this.activeMode = mode;
						
						$.each(mode.controls, function (k, control) {
							control.activate();
						});
						$.each(mode.layers, function (k, layer) {
							layer.setVisibility(true);
						});
						$.each(mode.events.activate, function (k, f) {
							f.apply([], container);
						});

						$(container).trigger('modechanged');
					},
					deactivate: function () {
						if (! this.activeMode) {
							return;
						}

						$.each(this.activeMode.controls, function (k, control) {
							control.deactivate();
						});
						$.each(this.activeMode.layers, function (k, layer) {
							layer.setVisibility(false);
						});
						$.each(this.activeMode.events.deactivate, function (k, f) {
							f.apply([], container);
						});

						this.activeMode = null;
					}
				};

				var defaultMode = {
					controls: []
				};

				map.addControl(new OpenLayers.Control.Attribution());

				if (-1 !== $.inArray('coordinates', desiredControls)) {
					map.addControl(new OpenLayers.Control.MousePosition({
						displayProjection: new OpenLayers.Projection("EPSG:4326")
					}));
				}

				if (layers.length > 1 && -1 !== $.inArray('scale', desiredControls)) {
					map.addControl(new OpenLayers.Control.ScaleLine());
				}

				if (layers.length > 1 && -1 !== $.inArray('navigation', desiredControls)) {
					defaultMode.controls.push(new OpenLayers.Control.NavToolbar());
				}

				if (layers.length > 1 && -1 !== $.inArray('controls', desiredControls)) {
					if (-1 !== $.inArray('levels', desiredControls)) {
						map.addControl(new OpenLayers.Control.PanZoomBar());
					} else {
						map.addControl(new OpenLayers.Control.PanZoom());
					}
				}

				if (layers.length > 1 && -1 !== $.inArray('layers', desiredControls)) {
					map.addControl(new OpenLayers.Control.LayerSwitcher());
				}

				var selectControl, vectorLayerList = [container.vectors];
				defaultMode.controls.push(selectControl = new OpenLayers.Control.SelectFeature(vectorLayerList, {
					onSelect: function (feature) {
						var type = feature.attributes.type
							, object = feature.attributes.object
							, lonlat = feature.geometry.getBounds().getCenterLonLat()
							, loaded = false
							;

						if (feature.attributes.itemId) {
							type = 'trackeritem';
							object = feature.attributes.itemId;
						}

						if (type && object) {
							loaded = $(container).loadInfoboxPopup({
								type: type,
								object: object,
								lonlat: lonlat,
								content: feature.attributes.content,
								close: function () {
									selectControl.unselect(feature);
								}
							});
						}
						
						if (! loaded && feature.attributes.content) {
							var popup = new OpenLayers.Popup.FramedCloud('feature', lonlat, null, feature.attributes.content, null, true, function () {
								$(container).setMapPopup(null);
							});
							popup.autoSize = true;

							$(container).setMapPopup(popup);
						}

						if (feature.clickHandler) {
							feature.clickHandler();
						}
					}
				}));

				if (layers.length > 1 && -1 !== $.inArray('overview', desiredControls)) {
					map.addControl(new OpenLayers.Control.OverviewMap({minRatio: 4096, maxRatio: 4096, maximized: true}));
				}

				container.markerIcons = {
					loadedMarker: {},
					actionQueue: {},
					loadingMarker: [],
					loadMarker: function (name, src) {
						this.loadingMarker.push(name);
						this.actionQueue[name] = [];

						var img = new Image(), me = this;
						img.onload = function () {
							var width = this.width, height = this.height, action;
							me.loadedMarker[name] = {
								intent: 'marker',
								url: src,
								width: width,
								height: height
							};

							while (action = me.actionQueue[name].pop()) {
								action();
							};
						};
						img.src = src;
					},
					createMarker: function (name, lonlat, callback) {
						if (this.loadedMarker[name]) {
							this._createMarker(name, lonlat, callback);
							return;
						}

						if (-1 === $.inArray(name, this.loadingMarker)) {
							this.loadMarker(name, name);
						}

						var me = this;
						this.actionQueue[name].push(function () {
							me._createMarker(name, lonlat, callback);
						});
					},
					_createMarker: function (name, lonlat, callback) {
						var properties = $.extend(this.loadedMarker[name], {}), marker;
						marker = new OpenLayers.Feature.Vector(
							new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat),
							properties
						);
						marker.renderIntent = properties.intent;
						callback(marker);
					}
				};

				container.getLayer = function (name) {
					var vectors;

					if (name) {
						if (! container.layers[name]) {
							vectors = container.layers[name] = new OpenLayers.Layer.Vector(name, {
								styleMap: container.defaultStyleMap
							});

							container.map.addLayer(vectors);
							vectorLayerList.push(vectors);
							setupLayerEvents(vectors);

							if (selectControl.active) {
								selectControl.deactivate();
								selectControl.activate();
							}
						}

						return container.layers[name];
					}

					return container.vectors;
				};

				container.clearLayer = function (name) {
					var vectors = container.getLayer(name);

					var toRemove = [];
					$.each(vectors.features, function (k, f) {
						if (f && f.attributes.itemId) {
							toRemove.push(f);
						} else if (f && f.attributes.type && f.attributes.object) {
							toRemove.push(f);
						}
					});
					vectors.removeFeatures(toRemove);
				};

				container.markerIcons.loadMarker('default', 'http://www.openlayers.org/dev/img/marker.png');
				container.markerIcons.loadMarker('selection', 'http://www.openlayers.org/dev/img/marker-gold.png');

				if (navigator.geolocation && navigator.geolocation.getCurrentPosition) {
					container.toMyLocation = $('<a/>')
						.css('display', 'block')
						.attr('href', '')
						.click(function () {
							navigator.geolocation.getCurrentPosition(function (position) {
								var lonlat = new OpenLayers.LonLat(position.coords.longitude, position.coords.latitude).transform(
									new OpenLayers.Projection("EPSG:4326"),
									map.getProjectionObject()
								);

								map.setCenter(lonlat);
								map.zoomToScale(position.coords.accuracy * OpenLayers.INCHES_PER_UNIT.m);

								$(container).addMapMarker({
									lat: position.coords.latitude,
									lon: position.coords.longitude,
									unique: 'selection'
								});
							});
							return false;
						})
						.text(tr('To My Location'));

					if (-1 !== $.inArray('current_location', desiredControls)) {
						$(container).after(container.toMyLocation);
					}
				}

				container.searchLocation = $('<a/>')
					.css('display', 'block')
					.attr('href', '')
					.click(function () {
						var address = prompt(tr('What address are you looking for?'));

						$(container).trigger('search', [ { address: address } ]);
						return false;
					})
					.text(tr('Search Location'));

				if (-1 !== $.inArray('search_location', desiredControls)) {
					$(container).after(container.searchLocation);
				}

				var field = $(container).data('target-field');
				var central = null, useMarker = true;

				if (field) {
					field = $($(container).closest('form')[0][field]);

					$(container).setupMapSelection({
						field: field
					});
					var value = field.val();
					central = parseCoordinates(value);
				}

				if ($(container).data('marker-filter')) {
					var filter = $(container).data('marker-filter');
					$(filter).each(function () {
						var lat = $(this).data('geo-lat')
							, lon = $(this).data('geo-lon')
							, zoom = $(this).data('geo-zoom')
							, extent = $(this).data('geo-extent')
							, icon = $(this).data('icon-src')
							, content = $(this).clone().data({}).wrap('<span/>').parent().html()
							;

						if (! extent) {
							if ($(this).hasClass('primary') || this.href === document.location.href) {
								central = {lat: lat, lon: lon, zoom: zoom ? zoom : 0};
							} else {
								$(container).addMapMarker({
									lon: lon,
									lat: lat,
									content: content,
									icon: icon ? icon : null
								});
							}
						} else if ($(this).is('img')) {
							var graphic = new OpenLayers.Layer.Image(
								$(this).attr('alt'),
								$(this).attr('src'),
								OpenLayers.Bounds.fromString(extent),
								new OpenLayers.Size($(this).width(), $(this).height())
							);

							graphic.isBaseLayer = false;
							graphic.alwaysInRange = true;
							container.map.addLayer(graphic);
						}
					});
				}

				var provided = $(container).data('geo-center');

				if (provided) {
					central = parseCoordinates(provided);
					useMarker = false;
				}

				if (central) {
					container.resetPosition = function () {
						var lonlat = new OpenLayers.LonLat(central.lon, central.lat).transform(
							new OpenLayers.Projection("EPSG:4326"),
							map.getProjectionObject()
						);

						map.setCenter(lonlat, central.zoom);
					};

					container.resetPosition();

					if (useMarker) {
						$(container).addMapMarker({
							lon: central.lon,
							lat: central.lat,
							unique: 'selection'
						});
					}
				}

				container.modeManager.addMode(defaultMode);

				if (jqueryTiki.googleStreetView) {
					container.streetview = {
						buttons: []
					};

					if (jqueryTiki.googleStreetViewOverlay) {
						container.streetview.overlay = new OpenLayers.Layer.XYZ(
							"StreetView Overlay",
							"http://cbk0.google.com/cbk?output=overlay&zoom=${z}&x=${x}&y=${y}&cb_client=api",
							{sphericalMercator: true, displayInLayerSwitcher: false}
						);
						container.map.addLayer(container.streetview.overlay);

						container.map.events.on({
							move: function () {
								if (container.streetview.overlay.visibility) {
									container.streetview.overlay.redraw();
								}
							}
						});
					}

					var StreetViewHandler = OpenLayers.Class(OpenLayers.Control, {
						defaultHandlerOptions: {
							'single': true,
							'double': false,
							'pixelTolerance': 0,
							'stopSingle': false,
							'stopDouble': false
						},
						initialize: function(options) {
							this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
							OpenLayers.Control.prototype.initialize.apply(this, arguments); 
							this.handler = new OpenLayers.Handler.Click(
								this,
								{
									'click': this.trigger
								},
								this.handlerOptions
							);
						}, 
						trigger: function(e) {
							var width = 600, height = 500;

							var lonlat = map.getLonLatFromViewPortPx(e.xy).transform(
								map.getProjectionObject(),
								new OpenLayers.Projection("EPSG:4326")
							);

							var canvas = $('<div/>')[0];
							$(canvas)
								.appendTo('body')
								.dialog({
									title: tr('Panorama'),
									width: width,
									height: height + 30,
									modal: true,
									close: function () {
										$(canvas).dialog('destroy');
									},
									buttons: container.streetview.getButtons(canvas)
								});

							canvas.getImageUrl = function () {
								var pov =  canvas.panorama.getPov();
								var pos =  canvas.panorama.getPosition();
								var base = 'http://maps.googleapis.com/maps/api/streetview?'
									+ 'size=' + width + 'x' + height + '&'
									+ 'location=' + escape(pos.toUrlValue()) + '&'
									+ 'heading=' + escape(pov.heading) + '&'
									+ 'pitch=' + escape(pov.pitch) + '&'
									+ 'sensor=false'
								;
								
								return base;
							};

							canvas.getPosition = function () {
								var pos =  canvas.panorama.getPosition();
									
								return pos.lng() + ',' + pos.lat() + ',12';
							};

							canvas.panorama = new google.maps.StreetViewPanorama(canvas, {
								position: new google.maps.LatLng(lonlat.lat, lonlat.lon),
								zoomControl: false,
								scrollwheel: false,
								disableDoubleClickZoom: true
							});
							var timeout = setTimeout(function () {
								$(canvas).dialog('close');
							}, 5000);
							google.maps.event.addListener(canvas.panorama, 'pano_changed', function () {
								if (! canvas.panorama.getPano()) {
									$(canvas).dialog('close');
								}
								clearTimeout(timeout);
							});
						}
					});

					container.modeManager.addMode({
						name: 'StreetView',
						controls: [ new StreetViewHandler(), new OpenLayers.Control.NavToolbar() ],
						activate: function () {
							if (container.streetview.overlay) {
								container.streetview.overlay.setVisibility(true);
							}
						},
						deactivate: function () {
							if (container.streetview.overlay) {
								container.streetview.overlay.setVisibility(false);
							}
						}
					});

					container.streetview.addButton = function (label, callback) {
						container.streetview.buttons.unshift({
							label: label,
							callback: callback
						});
					};

					container.streetview.getButtons = function (canvas) {
						var buttons = {};
						$.each(container.streetview.buttons, function (k, b) {
							buttons[b.label] = function () {
								b.callback(canvas);
							}
						});

						return buttons;
					}

					container.streetview.addButton('Cancel', function (canvas) {
						$(canvas).dialog('close');
					});
				}

				$(container).closest('.tab, #appframe, #tiki-center').find('form.search-box')
					.unbind('submit')
					.submit(function () {
						var form = this;
						$.post('tiki-searchindex.php?filter~geo_located=y', $(this).serialize(), function (data) {
							var layer = $(form).data('result-layer'), suffix = $(form).data('result-suffix');

							if (! form.autoLayers) {
								form.autoLayers = [];
							}

							$(form.autoLayers).each(function (k, name) {
								container.clearLayer(name);
							});

							$.each(data, function (k, i) {
								var icon = $(i.link).data('icon-src'), layerName = layer;

								if (suffix && i[suffix]) {
									layerName = layerName + i[suffix];
								}

								if (-1 === $.inArray(layerName, form.autoLayers)) {
									form.autoLayers.push(layerName);
								}

								if (i.geo_location) {
									$(container).addMapMarker({
										coordinates: i.geo_location,
										content: i.title,
										type: i.object_type,
										object: i.object_id,
										icon: icon ? icon : null,
										layer: layerName
									});
								} else if (i.geo_feature) {
									var format = new OpenLayers.Format.GeoJSON
										, wkt = new OpenLayers.Format.WKT
										, features = format.read(i.geo_feature)
										;

									$.each(features, function (k, feature) {
										var initial;
										feature.attributes.itemId = i.object_id;
										if (! feature.attributes.color) {
											feature.attributes.color = '#6699cc';
										}

										initial = wkt.write(feature) + feature.attributes.color;

										feature.executor = delayedExecutor(5000, function () {
											var fields = {}, current = wkt.write(feature) + feature.attributes.color;
											fields[i.geo_feature_field] = format.write(feature);

											if (current === initial) {
												return;
											}

											$.post($.service('tracker', 'update_item'), {
												trackerId: i.tracker_id,
												itemId: i.object_id,
												fields: fields
											}, function () {
												initial = current;
											}, 'json')
												.error(function () {
													$(container).trigger('changed');
												});
										});
									});
									container.getLayer(layerName).addFeatures(features);
								}
							});
						}, 'json');
						return false;
					})
					.each(function () {
						if ($(this).hasClass('onload')) {
							$(this).submit();
						}

						var skip = false;
						if ($(this).data('result-refresh')) {
							var form = this, refresh = parseInt($(this).data('result-refresh'), 10) * 1000;
							setInterval(function () {
								if (skip) {
									skip = false;
								} else {
									$(form).submit();
								}
							}, refresh);
						}

						$(container).bind('changed', function () {
							$(form).submit();
							skip = true;
						});
					});
				$(container).bind('search', function (e, data) {
					function markLocation (lat, lon, bounds) {
						var lonlat = new OpenLayers.LonLat(lon, lat).transform(
							new OpenLayers.Projection("EPSG:4326"),
							map.getProjectionObject()
						), toViewport;

						toViewport = function () {
							if (bounds) {
								map.zoomToExtent(bounds);
							} else {
								map.setCenter(lonlat);
								map.zoomToScale(500 * OpenLayers.INCHES_PER_UNIT.m);
							}
						};

						$(container).addMapMarker({
							lat: lat,
							lon: lon,
							unique: 'selection',
							click: toViewport
						});

						if (! container.map.getExtent().containsLonLat(lonlat)) {
							toViewport();
						}
					}

					function markGoogleLocation(result)
					{
						var loc = result.geometry.location
							, sw = result.geometry.viewport.getSouthWest()
							, ne = result.geometry.viewport.getNorthEast()
							, osw, one
							, left, bottom, right, top
							;

						osw = new OpenLayers.LonLat(sw.lng(), sw.lat()).transform(
							new OpenLayers.Projection("EPSG:4326"),
							map.getProjectionObject()
						);
						one = new OpenLayers.LonLat(ne.lng(), ne.lat()).transform(
							new OpenLayers.Projection("EPSG:4326"),
							map.getProjectionObject()
						);

						left = osw.lon;
						bottom = osw.lat;
						right = one.lon;
						top = one.lat;

						markLocation(loc.lat(), loc.lng(), new OpenLayers.Bounds(left, bottom, right, top));
					}

					function getBounds(bounds)
					{
						var osw, one
							;

						osw = new OpenLayers.LonLat(bounds.left, bounds.bottom).transform(
							map.getProjectionObject(),
							new OpenLayers.Projection("EPSG:4326")
						);
						one = new OpenLayers.LonLat(bounds.right, bounds.top).transform(
							map.getProjectionObject(),
							new OpenLayers.Projection("EPSG:4326")
						);

						return new google.maps.LatLngBounds(new google.maps.LatLng(osw.lat, osw.lon), new google.maps.LatLng(one.lat, one.lon));
					}

					if (data.address) {
						if (google && google.maps && google.maps.Geocoder) {
							var geocoder = new google.maps.Geocoder()
								, loc = $(container).getMapCenter().split(',');

							geocoder.geocode({
								bounds: getBounds(map.getExtent()),
								address: data.address
							}, function(results, status) {
								var $list = $('<ul/>');

								if (status == google.maps.GeocoderStatus.OK) {
									if (results.length === 1) {
										markGoogleLocation(results[0]);
										return;
									} else if (results.length > 0) {
										$.each(results, function (k, result) {
											var $link = $('<a href="#"/>');
											$link.text(result.formatted_address);
											$link.click(function () {
												markGoogleLocation(result);
												return false;
											});
											$('<li/>').append($link).appendTo($list);
										});
									}
								}

								$('<div/>')
									.append($list)
									.dialog({title: data.address});
							});
						} else {
							$.getJSON('tiki-ajax_services.php', {geocode: data.address}, function (data) {
								markLocation(data.lat, data.lon, 500);
							});
						}
					}
				});

				$(container).trigger('initialized');
			}, 250);
		});

		return this;
	};

	$.fn.addMapMarker = function (options) {
		this.each(function () {
			var container = this,
				lonlat,
				iconModel = "default";

			if (options.unique) {
				iconModel = options.unique;
			}

			if (options.icon) {
				iconModel = options.icon;
			}

			if (options.coordinates) {
				var parts = options.coordinates.split(',');
				if (parts.length >= 2) {
					options.lon = parts[0];
					options.lat = parts[1];
				}
			}
			
			if (options.lat && options.lon) {
				lonlat = new OpenLayers.LonLat(options.lon, options.lat).transform(
					new OpenLayers.Projection("EPSG:4326"),
					container.map.getProjectionObject()
				);
			}

			container.markerIcons.createMarker(iconModel, lonlat, function (feature) {
				if (options.type && options.object) {
					feature.attributes.type = options.type;
					feature.attributes.object = options.object;
				}

				var markerLayer = container.getLayer(options.layer), initial = writeCoordinates(lonlat.clone(), container.map, true);
				markerLayer.addFeatures([feature]);
				
				if (options.unique) {
					if (container.uniqueMarkers[options.unique]) {
						markerLayer.removeFeatures([container.uniqueMarkers[options.unique]]);
					}

					container.uniqueMarkers[options.unique] = feature;
					$(container).trigger(options.unique + 'Change', options);
				}

				if (options.type === 'trackeritem' && options.object) {
					feature.executor = delayedExecutor(5000, function () {
						var current = writeCoordinates(feature.geometry.getBounds().getCenterLonLat().clone(), container.map, true);

						if (current === initial) {
							return;
						}

						$.post($.service('tracker', 'set_location'), {
							itemId: options.object,
							location: current
						}, function () {
							initial = current;
						}, 'json')
							.error(function () {
								$(container).trigger('changed');
							});
					});
				}

				if (options.content) {
					feature.attributes.content = options.content;
				}

				if (options.click) {
					feature.clickHandler = options.click;
				}
			});
		});

		return this;
	};

	$.fn.setupMapSelection = function (options) {
		var control;
		this.each(function () {
			var container = this, field = options.field, map = this.map;

			if (! field.attr('disabled')) {
				$(container).bind('selectionChange', function (e, lonlat) {
					if (lonlat) {
						field.val(writeCoordinates(lonlat, map)).change();
					} else {
						field.val('').change();
					}
				});
				map.events.register('zoomend', map, function (e, lonlat) {
					var coords = field.val().split(","), lon = 0, lat = 0;
					if (coords.length > 1) {
						lon = coords[0];
						lat = coords[1];
					}
					field.val(lon + ',' + lat + ',' + map.getZoom()).change();
				});

				var ClickHandler = OpenLayers.Class(OpenLayers.Control, {                
					defaultHandlerOptions: {
						'single': true,
						'double': false,
						'pixelTolerance': 0,
						'stopSingle': false,
						'stopDouble': false
					},
					initialize: function(options) {
						this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
						OpenLayers.Control.prototype.initialize.apply(this, arguments); 
						this.handler = new OpenLayers.Handler.Click(
							this,
							{
								'click': this.trigger
							},
							this.handlerOptions
						);
					}, 
					trigger: function(e) {
						var lonlat = map.getLonLatFromViewPortPx(e.xy).transform(
							map.getProjectionObject(),
							new OpenLayers.Projection("EPSG:4326")
						);
						$(container).addMapMarker({
							lat: lonlat.lat,
							lon: lonlat.lon,
							unique: 'selection'
						});

						if (options.click) {
							options.click();
						}
					}
				});

				control = new ClickHandler();
				map.addControl(control);
				control.activate();
			}
		});

		return control;
	};

	$.fn.removeMapSelection = function () {
		this.each(function () {
			var container = this;

			if (container.uniqueMarkers['selection']) {
				container.vectors.removeFeatures([container.uniqueMarkers['selection']]);
			}

			$(container).trigger('selectionChange', {});
		});

		return this;
	};

	$.fn.getMapCenter = function () {
		var val;

		this.each(function () {
			var coordinates = this.map.getCenter();
			val = writeCoordinates(coordinates, this.map, true);
		});

		return val;
	};

	$.fn.setMapPopup = function (popup, old) {
		this.each(function () {
			// Replacement attempt, if not the same one, skip the operation
			if (old && old !== this.activePopup) {
				return;
			}

			if (this.activePopup) {
				if (this.activePopup.myclose) {
					var f = this.activePopup.myclose
					this.activePopup.myclose = null;
					f();
				} else {
					this.map.removePopup(this.activePopup);
				}

				this.activePopup = null;
			}

			if (popup) {
				this.activePopup = popup;
				this.map.addPopup(popup);
			}
		});
	};

	$.fn.loadInfoboxPopup = function (options) {
		if (options.type && options.object && $.inArray(options.type,  jqueryTiki.infoboxTypes) !== -1) {

			if (! options.content) {
				options.content = '';
			}

			this.each(function () {
				var container = this, popup;
				popup = new OpenLayers.Popup('marker', options.lonlat, null, options.content + '<img src="img/spinner.gif"/>');
				popup.autoSize = true;
				$(container).setMapPopup(popup);

				$.get($.service('object', 'infobox', {
					type: options.type,
					object: options.object
				}), function (data) {
					var newPopup, close = function () {
						$(container).setMapPopup(null);

						if (options.close) {
							options.close.apply([], container);
						}
					};

					var injectionId = ('popupInjection' + Math.random()).replace('.', '');

					newPopup = new OpenLayers.Popup.FramedCloud('marker', options.lonlat, null, '<div id="' + injectionId + '"></div>', options.hook, true, close);
					newPopup.myclose = close;
					$(container).setMapPopup(newPopup, popup);

					newPopup.setSize(new OpenLayers.Size(300, 260));

					$('#' + injectionId)
						.html(data)
						.find('img,svg').scaleImg({
							height: 150,
							width: 200
						});

					$('#' + injectionId).find('.svgImage')
						.css('text-align', 'center')
						.css('margin', 'auto');

					$('.service-dialog', container).click(function () {
						$(container).setMapPopup(null);

						$(this).serviceDialog({
							title: $(this).attr('title'),
							success: function () {
								$(container).trigger('changed');
							}
						});
						return false;
					});
				}, 'html');
			});

			return true;
		} else {
			return false;
		}
	};
})();

