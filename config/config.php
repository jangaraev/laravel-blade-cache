<?php

return [
    'homepage_bestselling_categories' => [
        'view' => 'blade_view',
        'ttl' => 90, // in minutes
        'variables' => fn () => [
            //'foo' => \App\Models\Foo::get()
        ]
    ],

    'homepage_recent_records' => [
        'view' => 'homepage.records',
        'ttl' => 11, // minutes
        'variables' => fn () => [
            //'title' => __('titles.newest'),
            //'records' => \App\Repositories\Blog::getRecent(),
            //'seeMore' => route('records.latest')
        ]
    ],
];
