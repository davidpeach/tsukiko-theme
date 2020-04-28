/* global tsukikoBgColors, tsukikoPreviewEls, jQuery, _, wp */
/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since Twenty Twenty 1.0
 */

( function( $, api, _ ) {
	/**
	 * Return a value for our partial refresh.
	 *
	 * @param {Object} partial  Current partial.
	 *
	 * @return {jQuery.Promise} Resolved promise.
	 */
	function returnDeferred( partial ) {
		var deferred = new $.Deferred();

		deferred.resolveWith( partial, _.map( partial.placements(), function() {
			return '';
		} ) );

		return deferred.promise();
	}

	// Selective refresh for "Fixed Background Image".
	api.selectiveRefresh.partialConstructor.cover_fixed = api.selectiveRefresh.Partial.extend( {

		/**
		 * Override the refresh method.
		 *
		 * @return {jQuery.Promise} Resolved promise.
		 */
		refresh: function() {
			var partial, cover, params;

			partial = this;
			params = partial.params;
			cover = $( params.selector );

			if ( cover.length && cover.hasClass( 'bg-image' ) ) {
				cover.toggleClass( 'bg-attachment-fixed' );
			}

			return returnDeferred( partial );
		}

	} );

	// Selective refresh for "Image Overlay Opacity".
	api.selectiveRefresh.partialConstructor.cover_opacity = api.selectiveRefresh.Partial.extend( {

		/**
		 * Input attributes.
		 *
		 * @type {Object}
		 */
		attrs: {},

		/**
		 * Override the refresh method.
		 *
		 * @return {jQuery.Promise} Resolved promise.
		 */
		refresh: function() {
			var partial, ranges, attrs, setting, params, cover, className, classNames;

			partial = this;
			attrs = partial.attrs;
			ranges = _.range( attrs.min, attrs.max + attrs.step, attrs.step );
			params = partial.params;
			setting = api( params.primarySetting );
			cover = $( params.selector );

			if ( cover.length ) {
				classNames = _.map( ranges, function( val ) {
					return 'opacity-' + val;
				} );

				className = classNames[ ranges.indexOf( parseInt( setting.get(), 10 ) ) ];

				cover.removeClass( classNames.join( ' ' ) );
				cover.addClass( className );
			}

			return returnDeferred( partial );
		}

	} );

	// Add listener for the "header_footer_background_color" control.
	api( 'header_footer_background_color', function( value ) {
		value.bind( function( to ) {
			// Add background color to header and footer wrappers.
			$( 'body:not(.overlay-header)#site-header, #site-footer' ).css( 'background-color', to );

			// Change body classes if this is the same background-color as the content background.
			if ( to.toLowerCase() === api( 'background_color' ).get().toLowerCase() ) {
				$( 'body' ).addClass( 'reduced-spacing' );
			} else {
				$( 'body' ).removeClass( 'reduced-spacing' );
			}
		} );
	} );

	// Add listener for the "background_color" control.
	api( 'background_color', function( value ) {
		value.bind( function( to ) {
			// Change body classes if this is the same background-color as the header/footer background.
			if ( to.toLowerCase() === api( 'header_footer_background_color' ).get().toLowerCase() ) {
				$( 'body' ).addClass( 'reduced-spacing' );
			} else {
				$( 'body' ).removeClass( 'reduced-spacing' );
			}
		} );
	} );

	// Add listener for the accent color.
	api( 'accent_hue', function( value ) {
		value.bind( function() {
			// Generate the styles.
			// Add a small delay to be sure the accessible colors were generated.
			setTimeout( function() {
				Object.keys( tsukikoBgColors ).forEach( function( context ) {
					tsukikoGenerateColorA11yPreviewStyles( context );
				} );
			}, 50 );
		} );
	} );

	// Add listeners for background-color settings.
	Object.keys( tsukikoBgColors ).forEach( function( context ) {
		wp.customize( tsukikoBgColors[ context ].setting, function( value ) {
			value.bind( function() {
				// Generate the styles.
				// Add a small delay to be sure the accessible colors were generated.
				setTimeout( function() {
					tsukikoGenerateColorA11yPreviewStyles( context );
				}, 50 );
			} );
		} );
	} );

	/**
	 * Add styles to elements in the preview pane.
	 *
	 * @since Twenty Twenty 1.0
	 *
	 * @param {string} context The area for which we want to generate styles. Can be for example "content", "header" etc.
	 *
	 * @return {void}
	 */
	function tsukikoGenerateColorA11yPreviewStyles( context ) {
		// Get the accessible colors option.
		var a11yColors = window.parent.wp.customize( 'accent_accessible_colors' ).get(),
			stylesheedID = 'tsukiko-customizer-styles-' + context,
			stylesheet = $( '#' + stylesheedID ),
			styles = '';
		// If the stylesheet doesn't exist, create it and append it to <head>.
		if ( ! stylesheet.length ) {
			$( '#tsukiko-style-inline-css' ).after( '<style id="' + stylesheedID + '"></style>' );
			stylesheet = $( '#' + stylesheedID );
		}
		if ( ! _.isUndefined( a11yColors[ context ] ) ) {
			// Check if we have elements defined.
			if ( tsukikoPreviewEls[ context ] ) {
				_.each( tsukikoPreviewEls[ context ], function( items, setting ) {
					_.each( items, function( elements, property ) {
						if ( ! _.isUndefined( a11yColors[ context ][ setting ] ) ) {
							styles += elements.join( ',' ) + '{' + property + ':' + a11yColors[ context ][ setting ] + ';}';
						}
					} );
				} );
			}
		}
		// Add styles.
		stylesheet.html( styles );
	}
	// Generate styles on load. Handles page-changes on the preview pane.
	$( document ).ready( function() {
		tsukikoGenerateColorA11yPreviewStyles( 'content' );
		tsukikoGenerateColorA11yPreviewStyles( 'header-footer' );
	} );



	// FROM tsukiko
	api.bind( 'preview-ready', function() {
		// Disable smooth scroll when controls sidebar is expanded
		$( 'html' ).css( 'scroll-behavior', 'auto' );

		api.preview.bind( 'tsukiko-customizer-sidebar-expanded', function( expanded ) {
			$( 'html' ).css( 'scroll-behavior', 'true' === expanded ? 'auto' : 'smooth' );
		} );
	} );

	api( 'tsukiko_text_width', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'tsukiko-text-width-medium tsukiko-text-width-wide' );
			if ( to ) {
				$( 'body' ).addClass( 'tsukiko-text-width-' + to );
			}
		} );
	} );

	api( 'tsukiko_body_font_size', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'tsukiko-site-font-small tsukiko-site-font-medium' );
			if ( to ) {
				$( 'body' ).addClass( 'tsukiko-site-font-' + to );
			}
		} );
	} );

	api( 'tsukiko_body_line_height', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'tsukiko-site-lh-medium tsukiko-site-lh-loose' );
			if ( to ) {
				$( 'body' ).addClass( 'tsukiko-site-lh-' + to );
			}
		} );
	} );

	api( 'tsukiko_heading_letter_spacing', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).toggleClass( 'tsukiko-heading-ls-normal', to === 'normal' );
		} );
	} );

	api( 'tsukiko_h1_font_size', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'tsukiko-h1-font-small tsukiko-h1-font-medium tsukiko-h1-font-large' );
			if ( to ) {
				$( 'body' ).addClass( 'tsukiko-h1-font-' + to );
			}
		} );
	} );

	api( 'tsukiko_logo_text_transform', function( value ) {
		value.bind( function( to ) {
			$( '.site-title' ).css( { 'text-transform': to ? to : 'none' } );
		} );
	} );

	api( 'tsukiko_menu_spacing', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'tsukiko-nav-spacing-medium tsukiko-nav-spacing-large' );
			if ( to ) {
				$( 'body' ).addClass( 'tsukiko-nav-spacing-' + to );
			}
		} );
	} );

	api( 'tsukiko_menu_font_size', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'tsukiko-nav-size-small tsukiko-nav-size-medium tsukiko-nav-size-larger' );
			if ( to ) {
				$( 'body' ).addClass( 'tsukiko-nav-size-' + to );
			}
		} );
	} );

	api( 'tsukiko_menu_text_transform', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( 'ul.primary-menu a, ul.modal-menu a' ).css( { 'text-transform': to, 'letter-spacing': 'uppercase' === to ? '0.0333em' : 'normal' } );
			} else {
				$( 'ul.primary-menu a, ul.modal-menu a' ).css( { 'text-transform': 'none', 'letter-spacing':'normal' } );
			}
		} );
	} );

	api( 'tsukiko_cover_page_height', function( value ) {
		value.bind( function( to ) {
			$( '.page-template-template-cover' ).removeClass( 'tsukiko-cover-medium' );
			if ( to ) {
				$( '.page-template-template-cover' ).addClass( 'tsukiko-cover-' + to );
			}
		} );
	} );

	api( 'tsukiko_cover_post_height', function( value ) {
		value.bind( function( to ) {
			$( '.post-template-template-cover' ).removeClass( 'tsukiko-cover-medium' );
			if ( to ) {
				$( '.post-template-template-cover' ).addClass( 'tsukiko-cover-' + to );
			}
		} );
	} );

	api( 'tsukiko_cover_vertical_align', function( value ) {
		value.bind( function( to ) {
			$( '.template-cover' ).removeClass( 'tsukiko-cover-center' );
			if ( to ) {
				$( '.template-cover' ).addClass( 'tsukiko-cover-' + to );
			}
		} );
	} );

	api( 'tsukiko_cover_page_scroll_indicator', function( value ) {
		value.bind( function( to ) {
			$( '.page-template-template-cover' ).toggleClass( 'tsukiko-cover-hide-arrow' );
		} );
	} );

	api( 'tsukiko_header_width', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'tsukiko-header-wide tsukiko-header-full' );
			if ( to ) {
				$( 'body' ).addClass( 'tsukiko-header-' + to );
			}
		} );
	} );

	api( 'tsukiko_footer_width', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'tsukiko-footer-wider tsukiko-footer-full' );
			if ( to ) {
				$( 'body' ).addClass( 'tsukiko-footer-' + to );
			}
		} );
	} );

	function tsukikoLoadGoogleFont( context, to ) {
		if ( to && 'sans-serif' !== to ) {
			var font, style, el, styleID, fontVariations;

			font = to.replace( / /g, '+' );
			fontVariations = 'body' === context ? '400,400i,500,600,700,800,900' : '400,500,600,700,800,900';
			styleID = 'tsukiko-customizer-font-' + context;
			style = '<link rel="stylesheet" type="text/css" id="' + styleID + '" href="https://fonts.googleapis.com/css?family=' + font + ':' + fontVariations + '">';
			el = $( '#' + styleID );

			if ( el.length ) {
				el.replaceWith( style );
			} else {
				$( 'head' ).append( style );
			}
		}
	}

	api( 'tsukiko_body_font', function( value ) {
		var onChange = function( to ) {
			tsukikoLoadGoogleFont( 'body', to );
		};
		onChange( value.get() );
		value.bind( onChange );
	} );

	api( 'tsukiko_heading_font', function( value ) {
		var onChange = function( to ) {
			tsukikoLoadGoogleFont( 'heading', to );
		};
		onChange( value.get() );
		value.bind( onChange );
	} );

	api( 'tsukiko_logo_font', function( value ) {
		var onChange = function( to ) {
			tsukikoLoadGoogleFont( 'logo', to );
		};
		onChange( value.get() );
		value.bind( onChange );
	} );

	var style = $( '#tsukiko-customizer-live-css' );
	if ( ! style.length ) {
		style = $( 'head' ).append( '<style type="text/css" id="tsukiko-customizer-live-css" />' ).find( '#tsukiko-customizer-live-css' );
	}

	api.bind( 'preview-ready', function() {
		api.preview.bind( 'update-customizer-live-css', function( css ) {
			style.text( css );
		} );
	} );


}( jQuery, wp.customize, _ ) );
