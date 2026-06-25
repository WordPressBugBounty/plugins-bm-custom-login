<?php
/**
 * Asset-rewriting engine for the login screen preview
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Endpoint_Preview\Internal;

use Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Asset_Inliner" class
 *
 * Pure transformation logic for the preview endpoint: it reads local
 * stylesheets, scripts, and images and rewrites their references so the
 * sandboxed, null-origin preview iframe can render them. The endpoint
 * module owns the WordPress wiring (the style/script loader filters) and
 * delegates the actual rewriting here.
 */
final class Asset_Inliner {
	/**
	 * Single source of truth for traversing CSS url() references
	 *
	 * The transform callback returning null leaves that particular reference
	 * untouched (so it can decide which schemes to skip, e.g. data: or remote).
	 *
	 * @param string                   $css       The CSS to traverse.
	 * @param callable(string):?string $transform Per-reference transform; receives the decoded reference, returns its replacement or null to leave it as-is.
	 *
	 * @return string The CSS with references rewritten by the callback.
	 */
	public static function replace_css_urls( string $css, callable $transform ): string {
		return preg_replace_callback(
			// A quoted value may legitimately contain spaces or ")"; an unquoted one may not.
			'/url\(\s*(?:(["\'])(.*?)\1|([^\'")\s]+))\s*\)/i',
			static function ( array $matches ) use ( $transform ): string {
				$quote     = $matches[1];
				$reference = '' !== $quote ? $matches[2] : ( $matches[3] ?? '' );

				if ( '' === $reference ) {
					return $matches[0];
				}

				$replacement = $transform( $reference );

				return null === $replacement
					? $matches[0]
					: 'url(' . $quote . $replacement . $quote . ')';
			},
			$css,
		) ?? $css;
	}

	/**
	 * Resolve relative url() references in a stylesheet to absolute URLs
	 *
	 * So that images and fonts can still be loaded by the browser once the
	 * stylesheet is inlined. References that are already absolute (data:,
	 * http(s):, protocol-relative, or fragment-only) are left untouched.
	 *
	 * @param string $css  The stylesheet contents.
	 * @param string $href The original stylesheet URL (used to resolve relative references).
	 *
	 * @return string The stylesheet with relative url() references made absolute.
	 */
	public static function absolutize_css_urls( string $css, string $href ): string {
		$base_url   = dirname( strtok( $href, '?#' ) ?: $href ) . '/';
		$origin     = wp_parse_url( site_url( '/' ) );
		$origin_url = null;

		if ( is_array( $origin ) && isset( $origin['scheme'], $origin['host'] ) ) {
			$port       = isset( $origin['port'] ) ? ':' . $origin['port'] : '';
			$origin_url = $origin['scheme'] . '://' . $origin['host'] . $port;
		}

		return self::replace_css_urls(
			$css,
			static function ( string $reference ) use ( $base_url, $origin_url ): ?string {
				// Leave already-absolute references (any URI scheme, //, #) untouched.
				if ( 1 === preg_match( '/^(?:[a-z][a-z0-9+.-]*:|\/\/|#)/i', $reference ) ) {
					return null;
				}

				/**
				 * Root-relative path: resolve against the origin, not
				 * site_url(), which would double-prepend the path on a
				 * subdirectory install.
				 */
				if ( 0 === strpos( $reference, '/' ) ) {
					return null === $origin_url ? null : $origin_url . $reference;
				}

				// Relative path: prepend base URL of the original stylesheet.
				return $base_url . $reference;
			},
		);
	}

	/**
	 * Replace locally resolvable image references with base64 data URIs
	 *
	 * Rewrites <img src="…"> attributes and CSS url(…) references found in
	 * actual CSS contexts (<style> bodies and style="" attributes). Any
	 * reference that does not resolve to a readable local image file (remote
	 * URL, missing file, non-image extension) is left untouched, so the
	 * resolution step doubles as a safety gate.
	 *
	 * @param string $html The rendered login-page markup.
	 *
	 * @return string The markup with local images inlined as data URIs.
	 */
	public static function inline_local_images( string $html ): string {
		/**
		 * Split out <script> blocks so neither rewrite pass touches them:
		 * an <img> tag or a url() reference inside a JavaScript string
		 * literal is markup/CSS only by coincidence and must be left alone.
		 *
		 * The opening-tag pattern skips quoted attribute values so a literal
		 * ">" inside one (e.g. <script data-x="a>b">) does not end the match
		 * early and misclassify the following markup as a script block.
		 */
		$segments = preg_split( '/(<script\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*>.*?<\/script>)/is', $html, -1, PREG_SPLIT_DELIM_CAPTURE );

		if ( false === $segments ) {
			return $html;
		}

		// Memoize per request so a reference repeated across tags / rules is read and encoded once.
		$cache = [];

		/**
		 * Rewrite every url() in a CSS source to a data URI, skipping values
		 * that are already data URIs. Applied only to genuine CSS contexts by
		 * the callers below: a "url(...)" substring elsewhere in the markup —
		 * link text, an href, a data-* attribute — is not CSS and must be left
		 * untouched rather than corrupted.
		 */
		$rewrite_css_urls = static function ( string $css ) use ( &$cache ): string {
			return self::replace_css_urls(
				$css,
				static function ( string $reference ) use ( &$cache ): ?string {
					if ( 0 === stripos( $reference, 'data:' ) ) {
						return null;
					}

					/**
					 * WordPress escapes inline style="" attributes, so a quoted
					 * url("…") there arrives as url(&quot;…&quot;): replace_css_urls()
					 * sees no literal quote and leaves the entities on the reference.
					 * Decode them, then strip the now-literal wrapping quotes (a
					 * literal-quoted url() is already unwrapped by that regex) so the
					 * reference matches a local path instead of staying external.
					 */
					$reference = html_entity_decode( $reference, ENT_QUOTES );

					if ( strlen( $reference ) >= 2 ) {
						$quote = $reference[0];

						if ( ( '"' === $quote || "'" === $quote ) && substr( $reference, -1 ) === $quote ) {
							$reference = substr( $reference, 1, -1 );
						}
					}

					return self::url_to_data_uri( $reference, $cache );
				},
			);
		};

		foreach ( $segments as $index => $segment ) {
			// Odd indices are the captured <script> blocks; leave them untouched.
			if ( 1 === $index % 2 ) {
				continue;
			}

			/**
			 * Inline <img> sources, one whole tag at a time. The tag pattern
			 * skips quoted attribute values so a literal ">" inside one (e.g.
			 * alt="a > b") does not truncate the tag match.
			 */
			$segment = preg_replace_callback(
				'/<img\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*>/i',
				static function ( array $matches ) use ( &$cache ): string {
					return self::inline_img_tag( $matches[0], $cache );
				},
				$segment,
			) ?? $segment;

			// Rewrite url() inside <style> bodies.
			$segment = preg_replace_callback(
				'/(<style\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*>)(.*?)(<\/style>)/is',
				static function ( array $matches ) use ( $rewrite_css_urls ): string {
					return $matches[1] . $rewrite_css_urls( $matches[2] ) . $matches[3];
				},
				$segment,
			) ?? $segment;

			// Rewrite url() inside style="" attributes.
			$segments[ $index ] = preg_replace_callback(
				'/(\sstyle\s*=\s*)(["\'])(.*?)\2/is',
				static function ( array $matches ) use ( $rewrite_css_urls ): string {
					return $matches[1] . $matches[2] . $rewrite_css_urls( $matches[3] ) . $matches[2];
				},
				$segment,
			) ?? $segment;
		}

		return implode( '', $segments );
	}

	/**
	 * Inline the src of a single <img> tag as a base64 data URI
	 *
	 * Matches a whitespace-delimited "src" attribute (so "data-src" and
	 * other "*-src" attributes are never mistaken for it). When the src
	 * resolves to a local image, any "srcset" / "sizes" attributes are
	 * dropped as well: the browser would otherwise prefer a srcset
	 * candidate, which still points at a URL the sandboxed iframe cannot
	 * load. Tags whose src does not resolve are returned untouched.
	 *
	 * @param string                $tag   The full <img> tag.
	 * @param array<string,?string> $cache Per-request memo of reference → data URI (or null), threaded into url_to_data_uri().
	 *
	 * @return string The tag with its src inlined, or unchanged if it does not resolve.
	 */
	public static function inline_img_tag( string $tag, array &$cache = [] ): string {
		/**
		 * Match a whitespace-delimited "src", quoted or unquoted. The "<img"
		 * prefix is consumed while skipping quoted attribute values, so a
		 * "src="-like substring inside an earlier attribute (e.g.
		 * alt="src=foo") is never mistaken for the real attribute — nor is a
		 * "*-src" attribute such as a lazy-loading "data-src".
		 */
		if ( 1 !== preg_match( '/(<img\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*?\s)src\s*=\s*(?:(["\'])(.*?)\2|([^\s>]+))/i', $tag, $src_match, PREG_OFFSET_CAPTURE ) ) {
			return $tag;
		}

		$quoted_src = $src_match[3][0];
		$src        = '' !== $quoted_src ? $quoted_src : ( $src_match[4][0] ?? '' );
		$data_uri   = self::url_to_data_uri( html_entity_decode( $src, ENT_QUOTES ), $cache );

		if ( null === $data_uri ) {
			return $tag;
		}

		/**
		 * Replace exactly the matched src assignment span (by offset) before
		 * stripping srcset/sizes, so the original offset stays valid and no
		 * identical text elsewhere in the tag is touched. The span excludes
		 * the captured "<img …" prefix (group 1), starting at "src".
		 */
		$quote      = '' !== $src_match[2][0] ? $src_match[2][0] : '"';
		$src_offset = $src_match[0][1] + strlen( $src_match[1][0] );
		$src_length = strlen( $src_match[0][0] ) - strlen( $src_match[1][0] );
		$tag        = substr_replace( $tag, 'src=' . $quote . $data_uri . $quote, $src_offset, $src_length );

		/**
		 * Strip srcset/sizes (anchored past the quoted attribute values, same
		 * as the src match) so a candidate URL the sandboxed iframe cannot
		 * load is never preferred over the inlined src.
		 */
		$tag = preg_replace( '/(<img\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*?)\ssrcset\s*=\s*(?:(["\']).*?\2|[^\s>]+)/i', '$1', $tag ) ?? $tag;
		$tag = preg_replace( '/(<img\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*?)\ssizes\s*=\s*(?:(["\']).*?\2|[^\s>]+)/i', '$1', $tag ) ?? $tag;

		return $tag;
	}

	/**
	 * Convert a locally resolvable image URL into a base64 data URI
	 *
	 * @param string                $url   The image URL to convert.
	 * @param array<string,?string> $cache Per-request memo of reference → data URI (or null), so a reference repeated across the markup is read and encoded once.
	 *
	 * @return ?string The data URI, or null if the URL does not resolve to a readable local image file.
	 */
	public static function url_to_data_uri( string $url, array &$cache = [] ): ?string {
		if ( array_key_exists( $url, $cache ) ) {
			return $cache[ $url ];
		}

		$cache[ $url ] = self::resolve_data_uri( $url );

		return $cache[ $url ];
	}

	/**
	 * Resolve a reference to a base64 data URI without memoization
	 *
	 * @param string $url The image URL to convert.
	 *
	 * @return ?string The data URI, or null if the URL does not resolve to a readable local image file.
	 */
	private static function resolve_data_uri( string $url ): ?string {
		/**
		 * A root-relative path resolves against the origin (scheme/host/port),
		 * not site_url(), which would double-prepend the path on a subdirectory
		 * install.
		 */
		if ( 0 === strpos( $url, '/' ) ) {
			$origin = wp_parse_url( site_url( '/' ) );

			if ( ! is_array( $origin ) || ! isset( $origin['scheme'], $origin['host'] ) ) {
				return null;
			}

			if ( 0 === strpos( $url, '//' ) ) {
				$url = $origin['scheme'] . ':' . $url;
			} else {
				$port = isset( $origin['port'] ) ? ':' . $origin['port'] : '';
				$url  = $origin['scheme'] . '://' . $origin['host'] . $port . $url;
			}
		}

		$file_path = self::resolve_url_to_path( $url );

		if ( null === $file_path ) {
			return null;
		}

		$mime_type = self::get_image_mime_type( $file_path );

		if ( null === $mime_type ) {
			return null;
		}

		$contents = Utils\File::get_contents( $file_path );

		if ( null === $contents ) {
			return null;
		}

		// base64_encode here builds an image data URI, not obfuscated code.
		return sprintf( 'data:%s;base64,%s', $mime_type, base64_encode( $contents ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * Resolve the MIME type for a supported image file by its extension
	 *
	 * Extension-based mapping (rather than finfo) keeps SVG correct as
	 * "image/svg+xml" and restricts inlining to a known set of image
	 * formats, so non-image files are never converted to data URIs. The
	 * image subset of core's `wp_get_mime_types()` is reused for everything
	 * core knows about; SVG is supplemented because core deliberately omits
	 * it from that map (it is only added when a plugin allows SVG uploads).
	 *
	 * @param string $file_path Absolute path to the image file.
	 *
	 * @return ?string The MIME type, or null if the extension is not a supported image format.
	 */
	public static function get_image_mime_type( string $file_path ): ?string {
		$mime_types = [ 'svg' => 'image/svg+xml' ];

		foreach ( wp_get_mime_types() as $extensions => $mime_type ) {
			if ( 0 !== strpos( $mime_type, 'image/' ) ) {
				continue;
			}

			foreach ( explode( '|', $extensions ) as $extension ) {
				$mime_types[ $extension ] = $mime_type;
			}
		}

		$extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );

		return $mime_types[ $extension ] ?? null;
	}

	/**
	 * Resolve a URL to a local filesystem path
	 *
	 * Converts WordPress content, includes, and site URLs to their
	 * corresponding local filesystem paths for stylesheet, script, and
	 * image inlining in the preview endpoint.
	 *
	 * @param string $url The URL to resolve.
	 *
	 * @return ?string The local file path, or null if not resolvable.
	 */
	public static function resolve_url_to_path( string $url ): ?string {
		// Strip query string and fragment.
		$clean_url = strtok( $url, '?#' );

		if ( false === $clean_url ) {
			return null;
		}

		/**
		 * Gate resolution to the asset types this engine inlines (styles,
		 * scripts, images). Without this, a script/style enqueued with a
		 * ".php" URL would resolve under ABSPATH and have its source read
		 * and inlined verbatim, leaking server-side code into the preview.
		 */
		$allowed_extensions = [ 'avif', 'bmp', 'css', 'gif', 'ico', 'jpeg', 'jpg', 'js', 'png', 'svg', 'webp' ];
		$extension          = strtolower( pathinfo( wp_parse_url( $clean_url, PHP_URL_PATH ) ?: $clean_url, PATHINFO_EXTENSION ) );

		if ( ! in_array( $extension, $allowed_extensions, true ) ) {
			return null;
		}

		/**
		 * Map URL prefixes to filesystem paths, ordered from most
		 * specific to least specific.
		 */
		$mappings = [
			content_url( '/' )  => WP_CONTENT_DIR . '/',
			includes_url( '/' ) => ABSPATH . WPINC . '/',
			site_url( '/' )     => rtrim( ABSPATH, '/' ) . '/',
		];

		foreach ( $mappings as $url_prefix => $fs_prefix ) {
			if ( 0 === strpos( $clean_url, $url_prefix ) ) {
				/**
				 * Decode the path segment (e.g. "%20" → space) so files whose
				 * names contain reserved characters resolve on disk. Prefix
				 * matching stays on the encoded URL; the traversal guard below
				 * still runs on the canonicalised path, so this cannot escape.
				 */
				$relative_path = rawurldecode( substr( $clean_url, strlen( $url_prefix ) ) );

				// A decoded null byte would make realpath() throw on PHP 8; reject it.
				if ( false !== strpos( $relative_path, "\0" ) ) {
					return null;
				}

				$file_path = $fs_prefix . $relative_path;

				/**
				 * Resolve to canonical path and verify it stays within
				 * the allowed directory to prevent path traversal.
				 */
				$real_path   = realpath( $file_path );
				$real_prefix = realpath( $fs_prefix );

				if ( false === $real_path || false === $real_prefix ) {
					return null;
				}

				/**
				 * Compare on forward slashes: realpath() returns OS-native
				 * separators, so a Windows path (backslashes) would never
				 * prefix-match the "/"-terminated prefix otherwise.
				 */
				$normalized_path   = str_replace( '\\', '/', $real_path );
				$normalized_prefix = rtrim( str_replace( '\\', '/', $real_prefix ), '/' ) . '/';

				if ( 0 !== strpos( $normalized_path, $normalized_prefix ) ) {
					return null;
				}

				return is_file( $real_path ) ? $real_path : null;
			}
		}

		return null;
	}
}
