/**
 * X LIGHTS — Elementor editor helpers.
 *
 * Page Composer: a section switched off in "Sections — Visibility & Order"
 * also drops out of the panel, so only the sections that render on the page
 * have settings panels. Turning it back on brings the panel back.
 *
 * Elementor conditions cannot read values inside a repeater row, so this reads
 * the order list from the widget model and hides the matching panel sections
 * with a stylesheet (which also survives the panel re-rendering itself).
 */
( function () {
	'use strict';

	var WIDGET = 'xlights-page-composer';
	var STYLE_ID = 'xl-composer-hidden-sections';
	var bound = null;
	var lastCss = null;

	function styleEl() {
		var el = document.getElementById( STYLE_ID );
		if ( ! el ) {
			el = document.createElement( 'style' );
			el.id = STYLE_ID;
			document.head.appendChild( el );
		}
		return el;
	}

	function clear() {
		var el = document.getElementById( STYLE_ID );
		if ( el ) {
			el.textContent = '';
		}
		lastCss = null;
	}

	function rowsOf( value ) {
		if ( ! value ) {
			return [];
		}
		if ( value.models ) {
			return value.models.map( function ( m ) {
				return m.toJSON ? m.toJSON() : m.attributes;
			} );
		}
		return Array.isArray( value ) ? value : [];
	}

	// Section keys and template names are slugs; keep them that way in selectors.
	function safe( s ) {
		return String( s ).replace( /[^a-z0-9_-]/gi, '' );
	}

	function update( settings ) {
		var template = safe( settings.get( 'page_template' ) || 'home' );
		var rows = rowsOf( settings.get( template + '__order' ) );
		var rules = [];
		var hidden = 0;
		rows.forEach( function ( row ) {
			if ( ! row || ! row.section || 'yes' === row.enabled ) {
				return;
			}
			var id = template + '__' + safe( row.section );
			hidden++;
			// The section header and, if it was open, its controls.
			rules.push( '#elementor-panel .elementor-control-' + id );
			rules.push( '#elementor-panel [class*=" elementor-control-' + id + '__"]' );
		} );
		var css = rules.length ? rules.join( ',\n' ) + ' { display: none !important; }' : '';
		if ( hidden ) {
			// Small reminder under the visibility list so hidden sections are easy to find again.
			css += '\n#elementor-panel .elementor-control-' + template + '__order .elementor-repeater-fields-wrapper::after {' +
				' content: "' + hidden + ( 1 === hidden ? ' hidden section — its settings are hidden too. Switch it back on here to edit it."' : ' hidden sections — their settings are hidden too. Switch them back on here to edit them."' ) +
				'; display: block; margin-top: 8px; font-size: 11px; line-height: 1.4; color: #6d7882; font-style: italic; }';
		}
		if ( css !== lastCss ) {
			styleEl().textContent = css;
			lastCss = css;
		}
	}

	function unbind() {
		if ( bound ) {
			bound.settings.off( null, null, bound.ctx );
			if ( bound.collection ) {
				bound.collection.off( null, null, bound.ctx );
			}
			window.clearInterval( bound.timer );
			bound = null;
		}
	}

	function bind( model ) {
		unbind();
		var settings = model && model.get && model.get( 'settings' );
		if ( ! settings || ! settings.get ) {
			clear();
			return;
		}
		var ctx = {};
		bound = { settings: settings, collection: null, ctx: ctx };

		function currentCollection() {
			var col = settings.get( safe( settings.get( 'page_template' ) || 'home' ) + '__order' );
			return col && col.on ? col : null;
		}

		function watchCollection() {
			if ( bound.collection ) {
				bound.collection.off( null, null, ctx );
			}
			bound.collection = currentCollection();
			if ( bound.collection ) {
				bound.collection.on( 'change add remove reset sort update', function () {
					update( settings );
				}, ctx );
			}
		}

		// Template switches, undo/redo and pasted settings can swap the list object.
		settings.on( 'change', function () {
			if ( currentCollection() !== bound.collection ) {
				watchCollection();
			}
			update( settings );
		}, ctx );

		// Some editor actions (undo/redo, replacing the whole list) swap the list
		// object without an event, so also re-check while this panel is open.
		bound.timer = window.setInterval( function () {
			if ( currentCollection() !== bound.collection ) {
				watchCollection();
			}
			update( settings );
		}, 700 );

		watchCollection();
		update( settings );
	}

	function init() {
		if ( ! window.elementor || ! elementor.hooks ) {
			return;
		}
		// Any other element opened in the panel: drop the rules.
		elementor.hooks.addAction( 'panel/open_editor/widget', function ( panel, model ) {
			if ( ! model || WIDGET !== model.get( 'widgetType' ) ) {
				unbind();
				clear();
			}
		} );
		[ 'section', 'column', 'container' ].forEach( function ( type ) {
			elementor.hooks.addAction( 'panel/open_editor/' + type, function () {
				unbind();
				clear();
			} );
		} );
		elementor.hooks.addAction( 'panel/open_editor/widget/' + WIDGET, function ( panel, model ) {
			bind( model );
		} );
	}

	if ( window.elementor && elementor.hooks ) {
		init();
	} else {
		window.addEventListener( 'elementor/init', init );
	}
}() );
