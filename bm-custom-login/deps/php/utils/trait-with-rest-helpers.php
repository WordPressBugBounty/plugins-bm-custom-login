<?php
/**
 * Set of methods to be inherited by REST endpoint modules
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "With_REST_Helpers" trait
 *
 * Bundles the nonce-argument schema, managing-permissions gate, and
 * current-actor resolution that every REST endpoint module re-implements
 * by hand. Mix it into a {@see Module} subclass; the helpers read
 * `$this->container`, which that base class always provides.
 *
 * The `$user_class` arguments default to {@see User} but accept a plugin's
 * local `User` subclass so the gate and actor resolution run that subclass'
 * overrides rather than bypassing them (see the monorepo convention on
 * instantiating local `Utils\*` subclasses).
 *
 * @phpstan-ignore trait.unused (Consumed only by endpoint modules in other packages; composer-utils itself never mixes it in, and with no abstract method to anchor standalone analysis PHPStan reports it as unused here. The body is analysed in every consumer package that uses it.)
 */
trait With_REST_Helpers {
	/**
	 * Build the REST argument schema for a nonce field
	 *
	 * The action is the short, plugin-relative name; {@see Nonce} prefixes
	 * the container's data prefix when signing and verifying.
	 *
	 * @param string $action Short nonce action (e.g. `save_settings`).
	 *
	 * @return array<string,mixed> REST argument schema for the nonce field.
	 */
	protected function get_nonce_arg( string $action ): array {
		return [
			'required'          => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',

			/**
			 * Validate the nonce value against the prefixed action
			 *
			 * @param string $value Nonce value.
			 *
			 * @return bool Whether the nonce value is valid.
			 */
			'validate_callback' => function ( string $value ) use ( $action ): bool {
				$nonce = new Nonce( $this->container, $action );
				return false !== wp_verify_nonce( $value, $nonce->get_action() );
			},
		];
	}

	/**
	 * Build the permission callback that gates an endpoint behind the
	 * plugin's managing capability
	 *
	 * Returns a closure suitable for a route's `permission_callback`: it
	 * resolves the current user and returns `true` when they hold the
	 * managing capability, a `rest_forbidden` {@see WP_Error} otherwise.
	 *
	 * @param ?string            $message    User-facing refusal message; defaults to a generic one.
	 * @param class-string<User> $user_class User class to instantiate; defaults to {@see User}.
	 *
	 * @return callable Permission callback returning `true` or a `WP_Error`.
	 */
	protected function get_managing_permissions_callback( ?string $message = null, string $user_class = User::class ): callable {
		return function () use ( $message, $user_class ) {
			$user = new $user_class( $this->container );

			if ( ! $user->has_managing_permissions() ) {
				return new WP_Error(
					'rest_forbidden',
					$message ?? __( 'Sorry, you are not allowed to do that.', 'bm-custom-login' ),
					[ 'status' => rest_authorization_required_code() ],
				);
			}

			return true;
		};
	}

	/**
	 * Resolve the current actor's user ID
	 *
	 * @param class-string<User> $user_class User class to instantiate; defaults to {@see User}.
	 *
	 * @return ?int Current user ID, or null when unresolved.
	 */
	protected function resolve_actor_id( string $user_class = User::class ): ?int {
		$user    = new $user_class( $this->container );
		$user_id = $user->get_user_id();

		return null !== $user_id && $user_id > 0 ? $user_id : null;
	}

	/**
	 * Build the REST argument schema for a string-list field
	 *
	 * Settings forms post role/user lists as JSON strings, while programmatic
	 * callers send actual arrays; this schema accepts either and normalizes
	 * to a `string[]`.
	 *
	 * @param bool $required Whether the field is required.
	 *
	 * @return array<string,mixed> REST argument schema for a string-list field.
	 */
	protected function get_string_list_arg( bool $required = true ): array {
		return [
			'required'          => $required,
			'type'              => 'array',
			'sanitize_callback' => [ $this, 'sanitize_string_list_param' ],
		];
	}

	/**
	 * Sanitize a string-list REST parameter
	 *
	 * @param mixed $value Raw field value: a JSON-encoded string, an array, or anything else.
	 *
	 * @return string[] Array of strings.
	 */
	public function sanitize_string_list_param( $value ): array {
		if ( is_string( $value ) ) {
			$value = JSON::decode( $value, [] );
		} elseif ( ! is_array( $value ) ) {
			$value = [];
		}

		/** @var string[] $value */
		return Type::ensure_array_of_strings( $value );
	}
}
