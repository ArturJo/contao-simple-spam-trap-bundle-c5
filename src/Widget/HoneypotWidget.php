<?php

declare(strict_types=1);

namespace SolidWork\ContaoSimpleSpamTrapBundleC5\Widget;

use Contao\System;
use Contao\Widget;
use Psr\Log\LoggerInterface;

class HoneypotWidget extends Widget
{
    /**
     * @var string
     */
    protected $strTemplate = 'form_honeypot';

    /**
     * @var bool
     */
    protected $blnSubmitInput = true;

    /**
     * @var string
     */
    protected $strPrefix = 'widget widget-honeypot';

    /**
     * Generate the honeypot field.
     */
    public function generate(): string
    {
        return sprintf(
            '<input type="text" name="%s" id="ctrl_%s" class="hp-field" value="" autocomplete="off" tabindex="-1" aria-hidden="true">',
            $this->strName,
            $this->strId,
        );
    }

    /**
     * Validate: the field must stay empty.
     */
    public function validate(): void
    {
        $value = $this->getPost($this->strName);

        if ('' !== (string) $value) {
            $this->addError($GLOBALS['TL_LANG']['ERR']['honeypot'] ?? 'Spamverdacht: Das Formular konnte nicht gesendet werden.');

            $this->getSpamLogger()?->warning('Honeypot field filled — spam submission blocked', [
                'field'      => $this->strName,
                'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'uri'        => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
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
}
