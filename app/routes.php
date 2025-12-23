<?php
$routes = [
    'GET' => [
        '/' => ['DashboardController', 'index'],
        '/login' => ['AuthController', 'showLogin'],
        '/register' => ['AuthController', 'showRegister'],
        '/logout' => ['AuthController', 'logout'],
        '/password/forgot' => ['AuthController', 'showForgot'],
        '/password/reset' => ['AuthController', 'showReset'],
        '/profile' => ['ProfileController', 'show'],
        '/weighins' => ['WeighInController', 'index'],
        '/weighins/export' => ['ExportImportController', 'export'],
        '/import' => ['ExportImportController', 'showImport'],
        '/badges' => ['BadgeController', 'index'],
        '/leaderboards' => ['LeaderboardController', 'index'],
        '/groups' => ['GroupController', 'index'],
        '/challenges' => ['ChallengeController', 'index'],
        '/photos' => ['PhotoController', 'index'],
    ],
    'POST' => [
        '/login' => ['AuthController', 'login'],
        '/register' => ['AuthController', 'register'],
        '/password/forgot' => ['AuthController', 'forgot'],
        '/password/reset' => ['AuthController', 'reset'],
        '/profile/update' => ['ProfileController', 'update'],
        '/profile/password' => ['ProfileController', 'changePassword'],
        '/profile/delete' => ['ProfileController', 'delete'],
        '/weighins/create' => ['WeighInController', 'create'],
        '/weighins/import' => ['ExportImportController', 'import'],
        '/groups/create' => ['GroupController', 'create'],
        '/groups/join' => ['GroupController', 'join'],
        '/groups/{id}/remove' => ['GroupController', 'removeMember'],
        '/challenges/join' => ['ChallengeController', 'join'],
        '/photos/upload' => ['PhotoController', 'upload'],
    ],
];

$protectedPaths = ['/','/profile','/weighins','/weighins/create','/weighins/export','/import','/weighins/import','/badges','/leaderboards','/groups','/groups/create','/groups/join','/groups/{id}/remove','/challenges','/challenges/join','/photos','/photos/upload'];
$csrfMethods = ['POST'];
$rateLimited = ['/login'];
