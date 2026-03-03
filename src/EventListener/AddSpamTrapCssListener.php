<?php

declare(strict_types=1);

namespace SolidWork\ContaoSimpleSpamTrapBundleC5\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook('generatePage')]
class AddSpamTrapCssListener
{
    public function __invoke(): void
    {
        $GLOBALS['TL_CSS'][] = 'bundles/contaosimplespamtrapc5/css/spam-trap.css';
    }
}
