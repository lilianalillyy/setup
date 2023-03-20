<?php

declare(strict_types=1);

namespace Liliana\Setup\Bridges\Slim\Configurator;

use Liliana\Setup\Exception\ConfiguratorException;
use Liliana\Setup\Extra\Configurator\ExtraConfigurator;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

class SlimConfigurator extends ExtraConfigurator
{
    public function addSlimDefinition(array $bootstraps = []): static
    {
        return $this->addServiceDefinition(App::class, function (ContainerInterface $container) use ($bootstraps) {
            AppFactory::setContainer($container);

            $app = AppFactory::create();

            foreach ($bootstraps as $bootstrap) {
                $callable = null;

                if (is_string($bootstrap) && is_file($bootstrap)) {
                    if (!is_readable($bootstrap)) {
                        throw new ConfiguratorException("Cannot read bootstrap file '$bootstrap'");
                    }
                    if (!is_callable($callable = @include $bootstrap)) {
                        throw new ConfiguratorException("Registered invalid bootstrap '$bootstrap'");
                    }
                } elseif (is_callable($bootstrap)) {
                    $callable = $bootstrap;
                } else {
                    $type = gettype($bootstrap);
                    throw new ConfiguratorException("Unknown bootstrap of type '$type' found");
                }

                $callable($app);
            }

            return $app;
        });
    }



}