<?php

$user         = Craft::$app->getUser();

$textGroup = modules\MatrixMateModule::createConfigBlock('Text', [
    'heading'           => ['adminOnly'=>false],
    'subheading'        => ['adminOnly'=>false],
    'text'              => ['adminOnly'=>false],
    'textImageBlock'    => ['adminOnly'=>false],
    'callout'            => ['adminOnly'=>false],
    'button'            => ['adminOnly'=>false],
    'noticeBlock'       => ['adminOnly'=>false],
]);

$linksGroup = modules\MatrixMateModule::createConfigBlock('Links', [
    'linksWithDescriptions' => ['adminOnly'=>false],
    'linkBlocksWithImages'  => ['adminOnly'=>true],
    'linkBlocksWithIcons'   => ['adminOnly'=>true],
    'largeEntryLinks'       => ['adminOnly'=>false],
    'smallEntryLinks'       => ['adminOnly'=>false],
    'newsEntries'           => ['adminOnly'=>false],
    'eventEntries'          => ['adminOnly'=>false],
    'meetingEntries'        => ['adminOnly'=>false],
    'callToAction'          => ['adminOnly'=>false],
]);

$pageElementsGroup = modules\MatrixMateModule::createConfigBlock('Page Elements', [
    'customTemplate'        => ['adminOnly'=>true],
    'embeddedContent'       => ['adminOnly'=>true],
    'emailSignup'           => ['adminOnly'=>true],
    'meetingsTable'         => ['adminOnly'=>true],
    'officials'             => ['adminOnly'=>true],
    'statsBlockWithIcons'   => ['adminOnly'=>true],
    'timeline'              => ['adminOnly'=>true],
]);

$imagesGroup = modules\MatrixMateModule::createConfigBlock('Images', [
    'image'     => ['adminOnly'=>false],
    'gallery'   => ['adminOnly'=>false],
]);

$tablesGroup = modules\MatrixMateModule::createConfigBlock('Tables', [
    'table2Columns' => ['adminOnly'=>false],
    'table3Columns' => ['adminOnly'=>false],
    'table4Columns' => ['adminOnly'=>false],
]);

$embedsGroup = modules\MatrixMateModule::createConfigBlock('Embeds', [
    'embed'             => ['adminOnly'=>true, 'groups'=>['digitalLeads']],
    'embedOpenForms'    => ['adminOnly'=>true, 'groups'=>['digitalLeads']],
    'embedYouTube'      => ['adminOnly'=>true, 'groups'=>['digitalLeads']],
]);

$adminBlock = [
    $textGroup,
    $linksGroup,
    $imagesGroup,
    $tablesGroup,
    $pageElementsGroup,
    $embedsGroup,
];

return [
    'fields' => [
        'contentBuilder' => [
            '*' => modules\MatrixMateModule::createConfigPermissions(( $user->isAdmin ? $adminBlock : [
                $textGroup,
                $linksGroup,
            ])),

            'section:departments, section:boardsCommissions, section:officials' => modules\MatrixMateModule::createConfigPermissions(( $user->isAdmin ? $adminBlock : [
                $textGroup,
                $linksGroup
            ])),

            'section:topics' => modules\MatrixMateModule::createConfigPermissions(( $user->isAdmin ? $adminBlock : [
                $textGroup,
                $linksGroup,
                $imagesGroup,
                $tablesGroup,
            ])),

            'section:resources' => modules\MatrixMateModule::createConfigPermissions(( $user->isAdmin ? $adminBlock : [
                $textGroup,
                $linksGroup,
                $imagesGroup,
                $tablesGroup,
                $embedsGroup,
            ])),
        ],
        'recordings' => [
            'hiddenTypes' => ($isUserAdmin ? '' : ['embed']),
        ],
    ],
];
