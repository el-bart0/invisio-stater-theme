#!/usr/bin/env node
/**
 * Rename this starter theme for a new project.
 *
 *   node bin/rename.mjs <slug> "<Theme Name>"
 *   npm run rename -- <slug> "<Theme Name>"
 *
 * <slug>        lowercase, [a-z0-9-]; becomes the text domain and (with dashes
 *              turned into underscores) the PHP function/constant prefix.
 * <Theme Name>  human-readable display name for the style.css header and docs.
 *
 * Rewrites text-domain / prefix / constant / block-namespace / package tokens
 * across the source tree and renames matching files. It does NOT touch
 * node_modules/, .git/, build/ or bin/ — run `npm install && npm run build &&
 * npm run make-pot` afterwards, then review the diff.
 */

import {
	readFileSync,
	writeFileSync,
	renameSync,
	readdirSync,
	statSync,
} from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join( dirname( fileURLToPath( import.meta.url ) ), '..' );

const SKIP_DIRS = new Set( [
	'node_modules',
	'.git',
	'build',
	'bin',
	'vendor',
] );
const TEXT_EXT = new Set( [
	'.php',
	'.js',
	'.mjs',
	'.cjs',
	'.jsx',
	'.ts',
	'.tsx',
	'.json',
	'.scss',
	'.sass',
	'.css',
	'.md',
	'.txt',
	'.pot',
	'.po',
	'.xml',
	'.yml',
	'.yaml',
	'.html',
	'.htaccess',
] );
const TEXT_NAMES = new Set( [
	'.editorconfig',
	'.gitattributes',
	'.gitignore',
	'.nvmrc',
	'style.css',
] );

const [ rawSlug, rawName ] = process.argv.slice( 2 );

if ( ! rawSlug || ! rawName ) {
	console.error( 'Usage: node bin/rename.mjs <slug> "<Theme Name>"' );
	process.exit( 1 );
}

const slug = rawSlug.trim().toLowerCase();

if ( ! /^[a-z][a-z0-9-]*$/.test( slug ) ) {
	console.error(
		`Invalid slug "${ rawSlug }". Use lowercase letters, digits and dashes, starting with a letter.`
	);
	process.exit( 1 );
}

const themeName = rawName.trim();
const phpPrefix = slug.replace( /-/g, '_' ); // acme-co -> acme_co
const constPrefix = phpPrefix.toUpperCase(); // ACME_CO

// Ordered so specific tokens are replaced before the generic word.
const REPLACEMENTS = [
	[ /INVISIO_/g, `${ constPrefix }_` ],
	[ /group_invisio_/g, `group_${ phpPrefix }_` ],
	[ /field_invisio_/g, `field_${ phpPrefix }_` ],
	[ /acf\/invisio-/g, `acf/${ slug }-` ],
	[ /\binvisio_/g, `${ phpPrefix }_` ],
	[ /@package Invisio\b/g, `@package ${ themeName.replace( /\s+/g, '' ) }` ],
	[ /\bInvisio\b/g, themeName ],
	[ /\binvisio\b/g, slug ],
];

const applyTokens = ( str ) =>
	REPLACEMENTS.reduce( ( acc, [ re, to ] ) => acc.replace( re, to ), str );

let filesChanged = 0;
let filesRenamed = 0;

/**
 * @param {string} dir Absolute directory path to walk.
 */
function walk( dir ) {
	for ( const entry of readdirSync( dir ) ) {
		const abs = join( dir, entry );
		const rel = abs.slice( ROOT.length + 1 ).replace( /\\/g, '/' );
		const stat = statSync( abs );

		if ( stat.isDirectory() ) {
			if ( SKIP_DIRS.has( entry ) ) {
				continue;
			}
			walk( abs );
			maybeRename( abs, entry, dir );
			continue;
		}

		const ext = entry.includes( '.' )
			? entry.slice( entry.lastIndexOf( '.' ) )
			: '';
		if ( TEXT_EXT.has( ext ) || TEXT_NAMES.has( entry ) ) {
			const original = readFileSync( abs, 'utf8' );
			const updated = applyTokens( original );
			if ( updated !== original ) {
				writeFileSync( abs, updated );
				filesChanged++;
				console.log( `  edited  ${ rel }` );
			}
		}

		maybeRename( abs, entry, dir );
	}
}

/**
 * Rename a file or directory whose name carries an "invisio" token.
 *
 * @param {string} abs    Current absolute path.
 * @param {string} name   Current basename.
 * @param {string} parent Parent directory.
 */
function maybeRename( abs, name, parent ) {
	const renamed = applyTokens( name );
	if ( renamed !== name ) {
		const target = join( parent, renamed );
		renameSync( abs, target );
		filesRenamed++;
		console.log(
			`  renamed ${ join( parent, name )
				.slice( ROOT.length + 1 )
				.replace( /\\/g, '/' ) } -> ${ renamed }`
		);
	}
}

console.log(
	`Renaming Invisio -> "${ themeName }" (slug: ${ slug }, prefix: ${ phpPrefix }_, const: ${ constPrefix }_)\n`
);

walk( ROOT );

console.log(
	`\nDone. ${ filesChanged } file(s) edited, ${ filesRenamed } path(s) renamed.\n` +
		'Next:\n' +
		'  1. npm install\n' +
		'  2. npm run build\n' +
		'  3. npm run make-pot\n' +
		'  4. Review the diff, set Author / URIs in style.css, update README + CHANGELOG.\n'
);
