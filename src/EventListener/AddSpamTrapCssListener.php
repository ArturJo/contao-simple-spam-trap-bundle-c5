<?php

declare(strict_types=1);

namespace SolidWork\ContaoSimpleSpamTrapBundleC5\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;

#[AsHook('generatePage')]
class AddSpamTrapCssListener
{
    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        $GLOBALS['TL_CSS'][] = 'bundles/contaosimplespamtrapc5/css/spam-trap.css|static';
    }
}
