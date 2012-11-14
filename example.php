#!/usr/bin/env php
<?php

# include Pharse
include './pharse.php';

/*
Options must be specified thusly:

# an outermost array of options
$options = array(
	# the option name (key), and an array of option parameters (val)
	# the option name is the option's long form (ie, --option-name)
	# @note: underscores will be replaced with hyphens when displayed to/
	# read from the command line
	'option_name'	=> array(
		# This is a verbose description of the option
		'description'	=> 'This option is an example',
		
		# Should this be required? Specify true or false (boolean), as appropriate
		'required'	    => true, 
		
		# specify the short option flag (eg, -x). If a short option is
		# not explicitly specified, one will be chosen programatically
		'short'			=> 'x',
		
		# The type may be specified. Pharse will perform basic validations
		# on the inputs. Valid settings are 'int', 'integer', 'number',
		# and 'string'
		'type'			=> 'string', 
		
		# A default value that should be assumed if an option is not
		# explicitly passed a value
		'default'		=> 'the-default',
	),
);
*/

# specify some options
$options = array(
    'user_name' => array(
		'description'   => 'Your username',
        'default'       => 'admin',
        'type'          => 'string',
        'required'      => true,
		'short'			=> 'u',
    ),
    'password' => array(
        'description'   => 'Your password',
        'default'       => 'sexsecretlovegod',
        'type'          => 'string',
        'required'      => true,
    ),
);

# You may specify a program banner thusly:
$banner = "Logs into the ultra-secure Sony Playstation Network servers.";
Pharse::setBanner($banner);

# After you've configured Pharse, run it like so:
$opts = Pharse::options($options);

/*

Presume you invoked this script from the shell like this:
 $ ./example.php -u chris -p

At this point, $opts looks like this:
Array
(
     [user_name] => chris
     [user_name_given] => 1
     [password] => sexsecretlovegod
     [password_given] => 1
)

Note that, in order to use the default password, you must invoke the -p
option, though you need not specify an actual value for the option. In
other words, this will NOT work:

 $ ./example.php -u chris

*/

# Now, just some obvious sample usage here.
if($opts['user_name'] === 'root'){
	if($opts['password'] === 'Password+1'){
		die("Welcome back, trusted administrator. To which pastebin service would you like to upload our source code and user data?\n");
	} else {
		die("You entered the wrong password. Hint: your password is 'Password+1.'\n");
	}
} else {
	die("Only root is allowed to log in. Disconnect immediately or we will do literally nothing about it.\n");
}




