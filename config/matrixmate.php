<?php

$textGroup = modules\MatrixMateModule::createConfigBlock('Text', [
    'heading' => ['adminOnly' => false],
    'subheading' => ['adminOnly' => false],
    'text' => ['adminOnly' => false],
    'textImageBlock' => ['adminOnly' => false],
    'callout' => ['adminOnly' => false],
    'button' => ['adminOnly' => false],
    'noticeBlock' => ['adminOnly' => false],
]);

$linksGroup = modules\MatrixMateModule::createConfigBlock('Links', [
    'linksWithDescriptions' => ['adminOnly' => false],
    'linkBlocksWithImages' => ['adminOnly' => true],
    'linkBlocksWithIcons' => ['adminOnly' => true],
    'largeEntryLinks' => ['adminOnly' => false],
    'smallEntryLinks' => ['adminOnly' => false],
    'newsEntries' => ['adminOnly' => false],
    'eventEntries' => ['adminOnly' => false],
    'meetingEntries' => ['adminOnly' => false],
    'callToAction' => ['adminOnly' => false],
]);

$imagesGroup = modules\MatrixMateModule::createConfigBlock('Images', [
    'image' => ['adminOnly' => false],
    'gallery' => ['adminOnly' => false],
]);

$tablesGroup = modules\MatrixMateModule::createConfigBlock('Tables', [
    'table2Columns' => ['adminOnly' => false],
    'table3Columns' => ['adminOnly' => false],
    'table4Columns' => ['adminOnly' => false],
]);

$embedsGroup = modules\MatrixMateModule::createConfigBlock('Embeds', [
    'embed' => ['adminOnly' => true, 'groups' => ['digitalLeads']],
    'embedOpenForms' => ['adminOnly' => true, 'groups' => ['digitalLeads']],
    'embedYouTube' => ['adminOnly' => true, 'groups' => ['digitalLeads']],
]);

$componentsGroup = modules\MatrixMateModule::createConfigBlock('Components', [
    'featuredProfile' => ['adminOnly' => false],
    'iconCards' => ['adminOnly' => false],
    'newsCards' => ['adminOnly' => false],
    'profileCards' => ['adminOnly' => false],
    'spotlight' => ['adminOnly' => false],
    'serviceCards' => ['adminOnly' => false],
    'textCards' => ['adminOnly' => false],
    'accordion' => ['adminOnly' => false],
]);

$pageElementsGroup = modules\MatrixMateModule::createConfigBlock('Page Elements', [
    'customTemplate' => ['adminOnly' => true],
    'embeddedContent' => ['adminOnly' => true],
    'emailSignup' => ['adminOnly' => true],
    'meetingsTable' => ['adminOnly' => true],
    'officials' => ['adminOnly' => true],
    'statsBlockWithIcons' => ['adminOnly' => true],
    'timeline' => ['adminOnly' => true],
]);

$adminBlock = [
    $textGroup,
    $linksGroup,
    $imagesGroup,
    $tablesGroup,
    $embedsGroup,
    $componentsGroup,
    $pageElementsGroup,
];

$isUserAdmin = Craft::$app->getUser()->getIsAdmin();

return [
    'fields' => [
        'contentBuilder' => [
            '*' => modules\MatrixMateModule::createConfigPermissions($isUserAdmin ? $adminBlock : [$textGroup, $linksGroup]),

            'section:departments, section:boardsCommissions, section:officials' => modules\MatrixMateModule::createConfigPermissions(
                $isUserAdmin ? $adminBlock : [$textGroup, $linksGroup, $componentsGroup]
            ),

            'section:topics' => modules\MatrixMateModule::createConfigPermissions(
                $isUserAdmin ? $adminBlock : [$textGroup, $linksGroup, $imagesGroup, $tablesGroup, $componentsGroup]
            ),

            'section:resources' => modules\MatrixMateModule::createConfigPermissions(
                $isUserAdmin ? $adminBlock : [$textGroup, $linksGroup, $imagesGroup, $tablesGroup, $embedsGroup, $componentsGroup]
            ),
        ],
        'recordings' => [
            'hiddenTypes' => !$isUserAdmin ? ['embed'] : '',
        ],
    ],
];
