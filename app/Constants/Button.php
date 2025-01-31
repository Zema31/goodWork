<?php

namespace App\Constants;

interface Button
{
    public const string APP = 'application';
    public const string LOOK = 'look';
    public const string DOWNLOAD = 'download';
    public const string DETAILS = 'details';

    public const array INFO = [
        self::APP => 'Оставить заявку',
        self::LOOK => 'Смотреть',
        self::DOWNLOAD => 'Скачать',
        self::DETAILS => 'Подробнее',
    ];
}
