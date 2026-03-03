<?php

declare(strict_types=1);

namespace SolidWork\ContaoSimpleSpamTrapBundleC5\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use SolidWork\ContaoSimpleSpamTrapBundleC5\ContaoSimpleSpamTrapBundleC5;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoSimpleSpamTrapBundleC5::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
