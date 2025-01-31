<?php

namespace App\Constants;

interface Status
{
    public const string ACTIVE = 'active';
    public const string WAIT = 'wait';
    public const string ARCHIVE = 'archive';

    public const array INFO = [
        self::ACTIVE => 'Активен',
        self::WAIT => 'В ожидании',
        self::ARCHIVE => 'В архиве',
    ];
}
