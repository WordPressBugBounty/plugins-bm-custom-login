<?php
/**
 * Nonce utils class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Nonce" class
 */
final class Nonce {
	/**
	 * Container instance
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * Action to sign or verify with nonce
	 *
	 * @var string
	 */
	protected string $action;

	/**
	 * Constructor
	 *
	 * @param Container $container Container instance.
	 * @param string    $action    Action to sign or verify with nonce.
	 */
	public function __construct( Container $container, string $action ) {
		$this->container = $container;
		$this->action    = $action;
	}

	/**
	 * Build an array of query arguments:
	 * - first argument is an action with a given value
	 * - second argument is an associated nonce
	 *
	 * @param int|string $value Value to pass.
	 *
	 * @return array<string,string> Query arguments array.
	 */
	public function build_query_args( $value ): array {
		return [
			$this->get_action() => Type::ensure_string( $value ),
			$this->get_key()    => $this->create(),
		];
	}

	/**
	 * Create the nonce string
	 *
	 * @return string Nonce string.
	 */
	public function create(): string {
		return wp_create_nonce( $this->get_action() );
	}

	/**
	 * Get the nonce action
	 *
	 * @return string Nonce action.
	 */
	public function get_action(): string {
		return sprintf(
			'%1$s__%2$s',
			$this->container->get_data_prefix(),
			$this->action,
		);
	}

	/**
	 * Get the key under which we should expect
	 * the nonce value to be available
	 *
	 * @return string Key to access the nonce through.
	 */
	public function get_key(): string {
		return sprintf(
			'%1$s__nonce_on_%2$s',
			$this->container->get_data_prefix(),
			$this->action,
		);
	}

	/**
	 * Render the nonce field
	 *
	 * @return void
	 */
	public function render_field(): void {
		wp_nonce_field( $this->get_action(), $this->get_key() );
	}
}
