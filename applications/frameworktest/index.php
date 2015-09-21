<?php

namespace FrameworkTest;

use ApplicationFramework;
	
require("autoload.php");

$argv=isset($argv)?$argv:array();

$ft_configuration=Configuration::GetInstance($argv);
$ft_configuration->InitializeApplication();
$ft_configuration->RunApplication();