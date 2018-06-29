;(function ( $, window, document, undefined ) {
	// Create the defaults once
	var pluginName = 'charts',
		defaults = {
			propertyName: "value"
		};

	// The actual plugin constructor
	function Plugin( element, type, options ) {
		this.element = element;
		this.options = $.extend( {}, defaults, options) ;
		this.type = type;






		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}

	Plugin.prototype.init = function () {
		console.log("init")






	};
	Plugin.prototype.refresh = function () {
		console.log("refresh")





	};

	Plugin.prototype.destroy = function () {
		console.log("destroy")
		delete  this.charts;
		$.removeData(this,'plugin_' + pluginName);
	};



	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function ( type, options ) {
		return this.each(function () {
			if (type=="destroy") {
				Plugin.prototype.destroy.call(this);
				return false;
			} else if (type=="refresh") {
				Plugin.prototype.refresh.call(this);
				return false;
			}
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName,
					new Plugin( this, type, options ));
			}
		});
	}

})( jQuery, window, document );