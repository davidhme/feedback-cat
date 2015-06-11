fca_delay = {
	key_frequency: 'fca_frequency',
	key_time_on_page_in_seconds: 'fca_delay_time_on_page',
	key_id: 'fca_delay_id',

	key_cookie_path: 'fca_delay_cookie_path',
	key_cookie_domain: 'fca_delay_cookie_domain',

	frequency_on_every_page_view: 'always',
	frequency_once_per_visit: 'session',
	frequency_once_per_day: 'day',
	frequency_once_per_month: 'month',
	frequency_only_once: 'once',

	run: function( delay_descriptor, callback ) {
		if ( ! delay_descriptor || ! delay_descriptor[ this.key_id ] ) {
			return;
		}

		var id = delay_descriptor[ this.key_id ];
		if ( this._cookie.has_item( this._get_prevent_cookie_key( id ) ) ) {
			return;
		}

		this._call(
			function() {
				callback();
				this.update( delay_descriptor );
			}.bind( this ),
			delay_descriptor[ this.key_time_on_page_in_seconds ]
		);
	},

	update: function( delay_descriptor ) {
		var id = delay_descriptor[ this.key_id ];

		this._set_prevent_cookie( id, delay_descriptor[ this.key_frequency ], delay_descriptor );
	},

	_call: function( callback, time_on_page_in_seconds ) {
		if ( ! callback ) {
			return;
		}

		if ( time_on_page_in_seconds ) {
			setTimeout( callback, time_on_page_in_seconds * 1000 );
		} else {
			callback();
		}
	},

	_set_prevent_cookie: function( id, frequency, cookie_descriptor ) {
		var key = this._get_prevent_cookie_key( id );

		var cookie_path = cookie_descriptor[ this.key_cookie_path ] || '/';
		var cookie_domain = cookie_descriptor[ this.key_cookie_domain ] || null;

		if ( ! frequency || frequency === this.frequency_on_every_page_view ) {
			this._cookie.remove_item( key, cookie_path, cookie_domain );
		} else {
			var expiration = this._get_cookie_expiration_by_frequency( frequency );
			if ( expiration !== undefined ) {
				this._cookie.set_item( key, true, expiration, cookie_path, cookie_domain );
			}
		}
	},

	_get_cookie_expiration_by_frequency: function( frequency ) {
		if ( frequency === this.frequency_once_per_day ) {
			return this._get_date_after_days( 1 );
		} else if ( frequency === this.frequency_once_per_month ) {
			return this._get_date_after_days( 30 );
		} else if ( frequency === this.frequency_only_once ) {
			return Infinity;
		} else if ( frequency === this.frequency_once_per_visit ) {
			return null;
		}
	},

	_get_prevent_cookie_key: function( id ) {
		return 'fca_delay_prevent_' + id;
	},

	_get_date_after_days: function( days ) {
		return this._get_date_after_hours( days * 24 );
	},

	_get_date_after_hours: function( hours ) {
		return this._get_date_after_minutes( hours * 60 );
	},

	_get_date_after_minutes: function( minutes ) {
		return this._get_date_after_seconds( minutes * 60 );
	},

	_get_date_after_seconds: function( seconds ) {
		return new Date( new Date().getTime() + ( seconds * 1000 ) );
	},

	/**
	 * A complete cookies reader/writer framework with full unicode support.
	 *
	 * Revision #1 - September 4, 2014
	 *
	 * https://developer.mozilla.org/en-US/docs/Web/API/document.cookie
	 * https://developer.mozilla.org/User:fusionchess
	 *
	 * This framework is released under the GNU Public License, version 3 or later.
	 * http://www.gnu.org/licenses/gpl-3.0-standalone.html
	 *
	 * Syntax:
	 *
	 * * set_item(name, value[, end[, path[, domain[, secure]]]])
	 * * get_item(name)
	 * * remove_item(name[, path[, domain]])
	 * * has_item(name)
	 * * keys()
	 */
	_cookie: {
		get_item: function( key ) {
			if ( ! key ) {
				return null;
			}
			return decodeURIComponent( document.cookie.replace( new RegExp( "(?:(?:^|.*;)\\s*" + encodeURIComponent( key ).replace( /[\-\.\+\*]/g, "\\$&" ) + "\\s*\\=\\s*([^;]*).*$)|^.*$" ), "$1" ) ) || null;
		},

		set_item: function( key, value, end, path, domain, secure ) {
			if ( ! key || /^(?:expires|max\-age|path|domain|secure)$/i.test( key ) ) {
				return false;
			}
			var expires = "";
			if ( end ) {
				switch ( end.constructor ) {
					case Number:
						expires = end === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + end;
						break;
					case String:
						expires = "; expires=" + end;
						break;
					case Date:
						expires = "; expires=" + end.toUTCString();
						break;
				}
			}
			document.cookie = encodeURIComponent( key ) + "=" + encodeURIComponent( value ) + expires + (domain ? "; domain=" + domain : "") + (path ? "; path=" + path : "") + (secure ? "; secure" : "");
			return true;
		},

		remove_item: function( key, path, domain ) {
			if ( ! this.has_item( key ) ) {
				return false;
			}
			document.cookie = encodeURIComponent( key ) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + (domain ? "; domain=" + domain : "") + (path ? "; path=" + path : "");
			return true;
		},

		has_item: function( key ) {
			if ( ! key ) {
				return false;
			}
			return (new RegExp( "(?:^|;\\s*)" + encodeURIComponent( key ).replace( /[\-\.\+\*]/g, "\\$&" ) + "\\s*\\=" )).test( document.cookie );
		},

		keys: function() {
			var keys = document.cookie.replace( /((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, "" ).split( /\s*(?:\=[^;]*)?;\s*/ );
			for ( var len = keys.length, i = 0; i < len; i ++ ) {
				keys[ i ] = decodeURIComponent( keys[ i ] );
			}
			return keys;
		}
	}
};
