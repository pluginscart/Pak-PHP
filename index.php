<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * this starts the plugin.
 *
 * @link:       	  http://pakjiddat.com
 * @since             2.0.0
 * @package           IslamCompanion
 *
 * @wordpress-plugin
 * Plugin Name:       Islam Companion
 * Plugin URI:        http://pakjiddat.com
 * Description:       The goal of this plugin is to make it easier to integrate Islam in your every day life
 * Version:           2.0.0
 * Author:            Pak Jiddat
 * Author URI:        http://pakjiddat.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       islam-companion
 * Domain Path:       /languages
 */


namespace IslamCompanion;

require("autoload.php");

$argv=isset($argv)?$argv:array();

$ic_configuration=Configuration::GetInstance($argv);
$ic_configuration->InitializeApplication($argv);
$ic_configuration->RunApplication();
