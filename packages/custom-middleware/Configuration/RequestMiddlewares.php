<?php
return [
    'frontend' => [
        'internal/custom-middlewares/dummy' => [
            'target' => \Internal\CustomMiddleware\Frontend\DummyMiddleware::class,
            'before' => [
                'typo3/cms-frontend/base-redirect-resolver',
            ],
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
    ],
];
