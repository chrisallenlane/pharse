#!/usr/bin/env php
<?php

# include Pharse
include './pharse.php';

# specify some options
$options = array(
    'monkey' => array(
        'description'   => 'Use monkey mode',
        'type'          => 'string',
    ),
    'mule' => array(
        'description'   => 'Use mule mode',
        //'short'         => 'z',
        //'type'          => 'string',
    ),
    'goat' => array(
        'description'   => 'Use goat mode',
        'default'       => 'racecar!',
        'type'          => 'int',
        'required'      => true,
        //'short'         => 'z',
    ),
    'dog' => array(
        'description'   => 'Use dog mode',
    ),
);

# configure Pharse
$banner = "This program tests the functionality of the Pharse option-parsing library.";
Pharse::setBanner($banner);
Pharse::options($options);
