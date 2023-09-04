<?php
// MenuAllocator.config.php

$config = array(
    'ma_menus' => array(
        'label' => 'Menus Options',
        'type' => 'Text',
        'description' => 'Enter all available menus of your website separated by an empty space. These menus will render as checkboxes on every page under /Home/.',
        'placeholder' => 'e.g. top footer …'
    ),
);

return $config;
