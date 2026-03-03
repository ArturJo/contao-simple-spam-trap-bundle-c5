<?php

declare(strict_types=1);

use SolidWork\ContaoSimpleSpamTrapBundleC5\Widget\HoneypotWidget;
use SolidWork\ContaoSimpleSpamTrapBundleC5\Widget\TimestampWidget;

// Register front-end form fields.
$GLOBALS['TL_FFL']['honeypot'] = HoneypotWidget::class;
$GLOBALS['TL_FFL']['timestamp'] = TimestampWidget::class;
