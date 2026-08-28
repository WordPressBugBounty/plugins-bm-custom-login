<?php
/**
 * Add the plugin upgrade action link
 *
 * @package Teydea_Studio\Login_Customizer
 */

namespace Teydea_Studio\Login_Customizer\Modules\Plugin_Upgrade_Action_Link;

use Teydea_Studio\Login_Customizer\Dependencies\Universal_Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Module_Plugin_Upgrade_Action_Link" class
 */
final class Module_Plugin_Upgrade_Action_Link extends Universal_Modules\Plugin_Upgrade_Action_Link\Module_Plugin_Upgrade_Action_Link {
	/**
	 * Plugin upgrade link
	 *
	 * @var string
	 */
	protected string $upgrade_link = 'https://teydeastudio.com/plugins/login-customizer/pricing/?utm_source=Teydea+Login+Customizer';
}
