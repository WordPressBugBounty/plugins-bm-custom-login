<?php
/**
 * Container runtime requirements checker
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Runtime_Requirements" class
 *
 * Owns the runtime requirement state (declared PHP extensions plus
 * built-in PHP and WordPress version floors), runs the checks, and
 * renders the user-facing failure messages. Container delegates to
 * an instance of this class.
 */
class Runtime_Requirements {
	/**
	 * Minimum supported PHP version
	 *
	 * @var string
	 */
	const MIN_PHP_VERSION = '7.4';

	/**
	 * Minimum supported WordPress version
	 *
	 * @var string
	 */
	const MIN_WP_VERSION = '6.6';

	/**
	 * Container the requirements apply to
	 *
	 * @var Container
	 */
	private Container $container;

	/**
	 * PHP extensions required for the container to operate
	 *
	 * @var string[]
	 */
	private array $required_extensions = [ 'mbstring' ];

	/**
	 * Failures collected by the most recent check() call
	 *
	 * @var array<int,array<string,string>>
	 */
	private array $failures = [];

	/**
	 * Constructor
	 *
	 * @param Container $container Container the requirements apply to.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Declare an additional required PHP extension
	 *
	 * Idempotent — declaring the same extension twice does not
	 * duplicate it in the internal list.
	 *
	 * @param string $extension Extension name (e.g. 'dom').
	 *
	 * @return void
	 */
	public function add_extension( string $extension ): void {
		if ( ! in_array( $extension, $this->required_extensions, true ) ) {
			$this->required_extensions[] = $extension;
		}
	}

	/**
	 * Run all declared checks and cache the failures
	 *
	 * Collects every failed requirement rather than short-circuiting
	 * on the first one, so the admin notice and activation page list
	 * all problems at once.
	 *
	 * @return array<int,array<string,string>> Failure descriptors; empty when all requirements are met.
	 */
	public function check(): array {
		$this->failures = [];

		if ( ! Environment::compare_php_version( self::MIN_PHP_VERSION, '>=' ) ) {
			$this->failures[] = [
				'type'     => 'php_version',
				'required' => self::MIN_PHP_VERSION,
				'current'  => Environment::get_php_version(),
			];
		}

		if ( ! Environment::compare_wp_version( self::MIN_WP_VERSION, '>=' ) ) {
			$this->failures[] = [
				'type'     => 'wp_version',
				'required' => self::MIN_WP_VERSION,
				'current'  => Environment::get_wp_version(),
			];
		}

		foreach ( $this->required_extensions as $extension ) {
			if ( ! Environment::is_extension_loaded( $extension ) ) {
				$this->failures[] = [
					'type'      => 'extension',
					'extension' => $extension,
				];
			}
		}

		return $this->failures;
	}

	/**
	 * Whether the most recent check() produced any failures
	 *
	 * @return bool True when failures exist; false otherwise.
	 */
	public function has_failures(): bool {
		return ! empty( $this->failures );
	}

	/**
	 * Register the WordPress hook that renders the failure notice
	 *
	 * Splitting this from check() lets Container::init() short-circuit
	 * cleanly without knowing the hook name or callback shape.
	 *
	 * @return void
	 */
	public function register_failure_notice(): void {
		// Render the unmet-requirements admin notice on every admin screen.
		add_action( 'all_admin_notices', [ $this, 'render_admin_notice' ] );
	}

	/**
	 * Render an undismissable admin error notice listing unmet requirements
	 *
	 * Skips rendering for users without the capability to act on the
	 * failure, since the notice would otherwise be a no-op for them.
	 *
	 * @return void
	 */
	public function render_admin_notice(): void {
		if ( ! current_user_can( $this->container->get_activate_capability() ) ) {
			return;
		}

		if ( empty( $this->failures ) ) {
			return;
		}

		?>
		<div class="notice notice-error">
			<p>
				<strong><?php echo esc_html( $this->container->get_name() ); ?></strong>
				<?php esc_html_e( 'cannot run on this site because the following requirements are not met:', 'bm-custom-login' ); ?>
			</p>
			<ul>
				<?php foreach ( $this->failures as $failure ) { ?>
					<li><?php echo esc_html( $this->format_failure_message( $failure ) ); ?></li>
				<?php } ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Halt plugin activation with a wp_die page listing unmet requirements
	 *
	 * @return void
	 */
	public function abort_activation(): void {
		if ( empty( $this->failures ) ) {
			return;
		}

		$items = '';

		foreach ( $this->failures as $failure ) {
			$items .= '<li>' . esc_html( $this->format_failure_message( $failure ) ) . '</li>';
		}

		wp_die(
			wp_kses(
				sprintf(
					'<p><strong>%1$s</strong> %2$s</p><ul>%3$s</ul>',
					esc_html( $this->container->get_name() ),
					esc_html__( 'cannot be activated because the following requirements are not met:', 'bm-custom-login' ),
					$items,
				),
				[
					'p'      => [],
					'strong' => [],
					'ul'     => [],
					'li'     => [],
				],
			),
			esc_html__( 'Plugin Activation Error', 'bm-custom-login' ),
			[ 'back_link' => true ],
		);

		exit; // @phpstan-ignore deadCode.unreachable
	}

	/**
	 * Format a single failure descriptor into a user-facing message
	 *
	 * @param array<string,string> $failure Failure descriptor (see check()).
	 *
	 * @return string Human-readable failure message.
	 */
	private function format_failure_message( array $failure ): string {
		$type = $failure['type'] ?? '';

		if ( 'php_version' === $type ) {
			return sprintf(
				// Translators: 1: required PHP version, 2: PHP version currently running.
				__( 'PHP %1$s or higher is required; this site runs PHP %2$s. Ask your hosting provider to upgrade PHP.', 'bm-custom-login' ),
				$failure['required'] ?? '',
				$failure['current'] ?? '',
			);
		}

		if ( 'wp_version' === $type ) {
			return sprintf(
				// Translators: 1: required WordPress version, 2: WordPress version currently running.
				__( 'WordPress %1$s or higher is required; this site runs WordPress %2$s. Update WordPress to continue.', 'bm-custom-login' ),
				$failure['required'] ?? '',
				$failure['current'] ?? '',
			);
		}

		if ( 'extension' === $type ) {
			return sprintf(
				// Translators: %s: PHP extension name.
				__( 'The PHP "%s" extension is required but not loaded. Ask your hosting provider to enable it.', 'bm-custom-login' ),
				$failure['extension'] ?? '',
			);
		}

		return '';
	}
}
