<?php declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Configuration\ConfigFilePathHelper;
use Symplify\Statie\DependencyInjection\ContainerFactory;

// performance boost
gc_disable();

/**
 * This allows to load "vendor/autoload.php" both from
 * "composer create-project ..." and "composer require" installation.
 */
$possibleAutoloadPaths = [__DIR__ . '/../../..', __DIR__ . '/../vendor', __DIR__ . '/../../../vendor'];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (file_exists($possibleAutoloadPath . '/autoload.php')) {
        require_once $possibleAutoloadPath . '/autoload.php';
        break;
    }
}

// 1. Detect configuration
ConfigFilePathHelper::detectFromInput('statie', new ArgvInput);

// 2. Build DI container
$containerFactory = new ContainerFactory;
$configFile = ConfigFilePathHelper::provide('statie', 'statie.neon');
if ($configFile) {
    $container = $containerFactory->createWithConfig($configFile);
} else {
    $container = $containerFactory->create();
}

// 3. Run Console Application
/** @var Application $application */
$application = $container->get(Application::class);
$application->run();
