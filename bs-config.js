/**
 * BrowserSync configuration.
 *
 * Runs via `npm start`, alongside the wp-scripts watch build.
 *
 * HTTPS local sites (Local by Flywheel, Valet, ...): because the proxy target is
 * https://, BrowserSync serves HTTPS on port 3000 too. Its bundled fallback cert
 * has no SAN and modern browsers reject it, so we hand BrowserSync the site's own
 * cert and serve on the *same hostname* the cert covers (e.g. cookies.local:3000)
 * — that cert is already trusted by your OS because the site works at :443.
 *
 * Per project, point it at your site (the cert is auto-detected from Local; set
 * WP_DEV_CERT / WP_DEV_KEY to override):
 *
 *   WP_DEV_URL=https://acme.local npm start
 *
 * Then open the URL BrowserSync prints as "Local", e.g. https://cookies.local:3000
 */
const fs = require( 'fs' );
const os = require( 'os' );
const path = require( 'path' );

const WP_DEV_URL = process.env.WP_DEV_URL || 'https://starter-tema.local';
const target = new URL( WP_DEV_URL );

/**
 * Locate the Local by Flywheel router cert + key for a hostname, cross-OS.
 *
 * @param {string} hostname Site host, e.g. 'cookies.local'.
 * @return {{cert: string, key: string}|null} Readable cert/key paths, or null when none are found.
 */
function findLocalCerts( hostname ) {
	if ( process.env.WP_DEV_CERT && process.env.WP_DEV_KEY ) {
		return { cert: process.env.WP_DEV_CERT, key: process.env.WP_DEV_KEY };
	}

	let base;
	if ( process.platform === 'win32' ) {
		base = path.join( process.env.APPDATA || '', 'Local' );
	} else if ( process.platform === 'darwin' ) {
		base = path.join(
			os.homedir(),
			'Library',
			'Application Support',
			'Local'
		);
	} else {
		base = path.join( os.homedir(), '.config', 'Local' );
	}

	const dir = path.join( base, 'run', 'router', 'nginx', 'certs' );
	const cert = path.join( dir, `${ hostname }.crt` );
	const key = path.join( dir, `${ hostname }.key` );

	try {
		fs.accessSync( cert );
		fs.accessSync( key );
		return { cert, key };
	} catch {
		return null;
	}
}

const config = {
	proxy: { target: WP_DEV_URL },
	files: [ 'build/**/*.css', 'build/**/*.js', '**/*.php' ],
	ignore: [ 'node_modules', 'vendor' ],
	injectChanges: true,
	open: false,
	notify: false,
	ghostMode: false,
	reloadDelay: 150,
	reloadDebounce: 200,
};

if ( target.protocol === 'https:' ) {
	const certs = findLocalCerts( target.hostname );

	if ( certs ) {
		config.host = target.hostname;
		config.https = certs;
	} else {
		// eslint-disable-next-line no-console
		console.warn(
			`\n[bs-config] No cert found for ${ target.hostname }. BrowserSync will ` +
				`serve HTTPS with an untrusted fallback cert that browsers reject.\n` +
				`Set WP_DEV_CERT and WP_DEV_KEY to your site's cert/key paths.\n`
		);
	}
}

module.exports = config;
