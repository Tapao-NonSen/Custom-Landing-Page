<?php

use Flarum\Extend;
use Tapao\CustomLandingPage\LandingPageMiddleware;

return [
    (new Extend\Middleware('forum'))
        ->add(LandingPageMiddleware::class),

    (new Extend\Settings())
        ->serializeToForum('tapaoCustomLandingPage.enabled', 'tapao-custom-landing-page.enabled', 'boolval'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/less/admin.less'),

    new Extend\Locales(__DIR__ . '/locale'),
];
