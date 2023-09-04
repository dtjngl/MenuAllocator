<?php
// MenuAllocator.config.php

$config = array(
    'ma_menus' => array(
        'label' => 'Menus Options',
        'type' => 'Text',
        'description' => 'Define the available menus of your website. Those menus will render as checkboxes on every page under /Home/.  You can access a page\'s selected menu with $page->meta->get(\'ma_menus\')'
    ),
);

return $config;
