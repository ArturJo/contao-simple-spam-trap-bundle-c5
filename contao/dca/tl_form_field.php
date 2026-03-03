<?php

declare(strict_types=1);

// Palettes
$GLOBALS['TL_DCA']['tl_form_field']['palettes']['honeypot'] =
    '{type_legend},type,name;{expert_legend:hide},class,accesskey,tabindex;{invisible_legend:hide},invisible';

$GLOBALS['TL_DCA']['tl_form_field']['palettes']['timestamp'] =
    '{type_legend},type,name;{config_legend},minTime;{expert_legend:hide},class,accesskey,tabindex;{invisible_legend:hide},invisible';

// Additional field for the timestamp widget.
$GLOBALS['TL_DCA']['tl_form_field']['fields']['minTime'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form_field']['minTime'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'natural', 'mandatory' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 8],
];
