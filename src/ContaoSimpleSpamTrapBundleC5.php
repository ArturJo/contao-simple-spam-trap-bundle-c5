<?php

declare(strict_types=1);

namespace SolidWork\ContaoSimpleSpamTrapBundleC5;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ContaoSimpleSpamTrapBundleC5 extends AbstractBundle implements PrependExtensionInterface
{
    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder,
    ): void {
        $containerConfigurator->import('../config/services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('monolog', [
            'channels' => ['spam_trap'],
            'handlers' => [
                'solidwork_spam_trap_file' => [
                    'type'      => 'rotating_file',
                    'path'      => '%kernel.logs_dir%/spam-trap/spam-trap.log',
                    'level'     => 'debug',
                    'max_files' => 30,
                    'channels'  => ['spam_trap'],
                ],
            ],
        ]);
    }
}
