<?php
/**
 * Add the plugin upgrade action link
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules;

use Teydea_Studio\Custom_Login\Dependencies\Universal_Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Module_Plugin_Upgrade_Action_Link" class
 */
final class Module_Plugin_Upgrade_Action_Link extends Universal_Modules\Module_Plugin_Upgrade_Action_Link {
	/**
	 * Plugin upgrade link
	 *
	 * @var string
	 */
	protected string $upgrade_link = 'https://wpcustomlogin.com/pricing/?utm_source=WP+Custom+Login';
}
