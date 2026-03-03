<?php

declare(strict_types=1);

namespace SolidWork\ContaoSimpleSpamTrapBundleC5\Widget;

use Contao\System;
use Contao\Widget;
use Psr\Log\LoggerInterface;

class TimestampWidget extends Widget
{
    /**
     * @var string
     */
    protected $strTemplate = 'form_timestamp';

    /**
     * @var bool
     */
    protected $blnSubmitInput = true;

    /**
     * @var string
     */
    protected $strPrefix = 'widget widget-timestamp';

    /**
     * Minimum number of seconds between form display and submit.
     */
    protected int $minSeconds = 8;

    public function __set(string $key, mixed $value): void
    {
        if ('minTime' === $key) {
            $intValue = (int) $value;
            $this->minSeconds = $intValue > 0 ? $intValue : 8;
        }

        parent::__set($key, $value);
    }

    /**
     * Validate the timestamp.
     */
    public function validate(): void
    {
        $submitted = (int) $this->getPost($this->strName);
        $now = time();
        $elapsed = $submitted ? ($now - $submitted) : 0;

        if (!$submitted || $elapsed < $this->minSeconds) {
            $this->addError(
                $GLOBALS['TL_LANG']['ERR']['timestamp']
                ?? sprintf('Sie haben das Formular zu schnell abgeschickt. Bitte warten Sie mindestens %d Sekunden.', $this->minSeconds),
            );

            $this->getSpamLogger()?->warning('Form submitted too fast — spam submission blocked', [
                'field'        => $this->strName,
                'elapsed_sec'  => $elapsed,
                'min_sec'      => $this->minSeconds,
                'ip'           => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'uri'          => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'user_agent'   => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            ]);
        }

        parent::validate();
    }

    private function getSpamLogger(): ?LoggerInterface
    {
        $container = System::getContainer();

        if ($container->has('monolog.logger.spam_trap')) {
            return $container->get('monolog.logger.spam_trap');
        }

        return null;
    }

    /**
     * Generate a hidden field with the current timestamp.
     */
    public function generate(): string
    {
        return sprintf(
            '<input type="hidden" name="%s" id="ctrl_%s" value="%s">',
            $this->strName,
            $this->strId,
            time(),
        );
    }
}
