Pharse - Command Line Option Parser for PHP
=================
Chris Lane  
18 Mar 2012  
chris@chris-allen-lane.com  
http://chris-allen-lane.com  
http://twitter.com/#!/chrisallenlane


What it Does
------------
`Pharse` is a command-line PHP option-parser, heavily inspired by William
Morgan's excellent [Trollop option parser for Ruby](http://trollop.rubyforge.org/).
It is designed to be easy-to-use and idiomatic to PHP.


Installation
------------
Simply `require` or `include` `pharse.php` into your PHP application.


Usage Examples
--------------
Usage is simple:

1. `include` or `require` `pharse.php` into your application
2. Create an associative array of program options
3. Optionally set a help banner by invoking `Pharse::setBanner($banner)`
4. Statically invoke `Pharse::options($options)` to engage the option parser

The included `example.php` provides a working, executable demonstration of
the library's usage.


Known Issues
------------
This script will likely exhibit undesirable behavior if more than 26
options are specified. This is because each short option is automatically
chosen (if not explicitly specified) from [a-z], and the supply of 
lowercase letters will be exhausted beyond 26.

Also note that, unlike Trollop, `Pharse` does NOT support nested options
(as in `git push origin master`, `git pull origin master`, etc.) I don't
need that functionality for the overwhelming majority of my own use-cases,
and hence I did not take the time to implement it. If you find that you need
this functionality, patches and forks are always welcome!

Beyond that, let it be known that this library was hacked out in about
two hours one late night when I had nothing better to do. Unit tests have
yet to be written, and testing was informal. Be sure to thoroughly kick
the tires before choosing to deploy this library in a production environment.


Contact Me
-------------
If you have questions, concerns, bug reports, feature requests, etc,
feel free to contact me at chris@chris-allen-lane.com.


License
-------
This product is licensed under the GPL 3 (http://www.gnu.org/copyleft/gpl.html).
It comes with no warranty, expressed or implied.
