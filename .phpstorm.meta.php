<?php
namespace PHPSTORM_META {
    /** @noinspection PhpUnusedLocalVariableInspection */ // just to have a green code below
    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    $STATIC_METHOD_TYPES = [ // we make sections for scopes
        \Magice::dig('') => [
            'request' instanceof \Symfony\Component\HttpFoundation\Request,
        ],
    ];
}