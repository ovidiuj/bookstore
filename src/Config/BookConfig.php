<?php


namespace App\Config;


class BookConfig extends AbstractConfig
{
    public const RESOURCE_NAME = 'book';
    protected array $sortableFields = [
        'createdAt',
        'title',
        'author',
        'cover'
    ];

    protected array $searchableFields = [
        'title',
        'author',
        'cover',
        'description'
    ];
}