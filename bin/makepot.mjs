#!/usr/bin/env node
/**
 * Generate languages/<text-domain>.pot from the theme's PHP.
 *
 *   npm run make-pot
 *
 * Text domain and package name are read from the style.css header, so this keeps
 * working after bin/rename.mjs. No WP-CLI required (uses the wp-pot package).
 */

import { readFileSync, existsSync, mkdirSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import wpPot from 'wp-pot';

const ROOT = join( dirname( fileURLToPath( import.meta.url ) ), '..' );

const header = readFileSync( join( ROOT, 'style.css' ), 'utf8' );
const field = ( label, fallback ) => {
	const match = header.match( new RegExp( `${ label }:\\s*(.+)`, 'i' ) );
	return match ? match[ 1 ].trim() : fallback;
};

const domain = field( 'Text Domain', 'invisio' );
const name = field( 'Theme Name', domain );

if ( ! existsSync( join( ROOT, 'languages' ) ) ) {
	mkdirSync( join( ROOT, 'languages' ) );
}

wpPot( {
	destFile: join( ROOT, 'languages', `${ domain }.pot` ),
	domain,
	package: name,
	src: [ join( ROOT, '**/*.php' ) ],
	ignore: [
		join( ROOT, 'node_modules/**' ),
		join( ROOT, 'build/**' ),
		join( ROOT, 'vendor/**' ),
	],
	relativeTo: ROOT,
} );

console.log( `Wrote languages/${ domain }.pot` );
