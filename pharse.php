<?php

/**
 * This is the main option parser class
 */
class Pharse{
    static $banner       = null;
    static $options      = array();
    static $return       = array();
    static $shorts       = array();

    /**
     * This is the main option parsing method
     * 
     * @param array $options            An array of options data
     * @return string $text             Text output for the CLI
     */
    static function options(array $options){
        # use the globalized $argv and unset the filename argument
        global $argv;
        unset($argv[0]);

        # manually add the default 'help' command
        $options += array(
            'help' => array(
                'short'         => 'h',
                'description'   => 'Display this help banner',
            )
        );

        # create the option specifications
        foreach($options as $option_name => $constraints){
            Pharse::$options[$option_name] = new PharseOption($option_name, $constraints);
        }

        # now do the actual option parsing
        # cheaply parse the args into $key => $val
        $arg_string = trim(implode($argv, ' '));
        $arg_string = str_replace('--', '-', $arg_string);
        $args       = explode('-', $arg_string);
        unset($args[0]);

        # assemble an array of proper options
        foreach($args as $arg){
            # null out the local variables in this loop on every iteration
            $option  = null;
            $value   = null;
            $the_opt = null;

            # separate the string on the equals sign if one exists
            if(strpos($arg, '=')){
                $option = trim(substr($arg, 0, strpos($arg, '=')));
                $value  = trim(substr($arg, strpos($arg, '=') + 1));
            }

            # otherwise, split on the first space
            else if(strpos($arg, ' ')){
                $option = trim(substr($arg, 0, strpos($arg, ' ')));
                $value  = trim(substr($arg, strpos($arg, ' ') + 1));
            }

            # if an option is set with no value, handle it
            else {
                $option = trim($arg);
            }

            # locate the option whose value needs to be set
            if(isset(Pharse::$options[$option])){
                $the_opt = Pharse::$options[$option];
            } else if(isset(Pharse::$shorts[$option])){
                $the_opt = Pharse::$options[Pharse::$shorts[$option]];
            } else {
                die("Error: option {$option} is unrecognized.\n");
            }

            # if value was unspecified, look up the default
            if($value == null){
                $value = $the_opt->default;
            }

            # use some type-punning to cast $value to the appropriate
            # PHP data-type
            if(is_numeric($value)){
                $value += 0;
            } else {
                $value .= "";
            }

            # now, set the value for the option
            $the_opt->value = $value;

            # Save the data in the array of data to be returned. If all
            # of the provided options pass validation, this data will
            # be returned to the host program.
            self::$return[$the_opt->name] = $the_opt->value;
            self::$return[$the_opt->name . "_given"] = true;
        }

        # Now that we've successfully parsed the options, simply
        # show the help banner if --help or -h has been specified.
        if(@self::$return['help_given']) self::help();

        # validate and prepare each key/value pair for return
        foreach(Pharse::$options as $key => $option){
            $option->validate();
        }

        # return the array of parsed options
        return self::$return;
    }


    /**
     * Sets the help banner
     * 
     * @param string $banner            The banner text to display on help
     */
    static function setBanner($banner){
        self::$banner = $banner;
    }


    /**
     * Displays the help text
     * 
     * @return $string $text            The help text to display
     */
    static function help(){
        # display the help banner if one has been set
        if(self::$banner != null){
            echo self::$banner . "\n";
        }

        # to get the formatting to look nice when printed, do some
        # calculations regarding help message line-length.
        $max_before_colon_pos = 0;
        foreach(self::$options as $option){
            # calculate the line-length based on the absence or presence
            # of a type requirement
            if($option->type != null){
                $t    = substr($option->type, 0, 1);
                $line = "{$option->long}, {$option->short} <$t>: {$option->description}\n";
            } else {
                $line = "{$option->long}, {$option->short}: {$option->description}\n";
            }
            $before_colon_pos = strlen(substr($line, 0, strpos($line, ':')));

            if($before_colon_pos > $max_before_colon_pos) {
                $max_before_colon_pos = $before_colon_pos;
            }
        }

        # then display the options
        echo "Options:\n";
        foreach(self::$options as $option){       
            # same as above
            if($option->type != null){
                $t    = substr($option->type, 0, 1);
                $line = "{$option->long}, {$option->short} <$t>: {$option->description}\n";
            } else {
                $line = "{$option->long}, {$option->short}: {$option->description}\n";
            }

            # now pad the string before the colon with spaces as appropriate
            $before_colon_str = substr($line, 0, strpos($line, ':'));
            $before_colon_str = str_pad($before_colon_str, $max_before_colon_pos, ' ', STR_PAD_LEFT);
            $after_colon_str  = substr($line, strpos($line, ':'));

            # and output the result
            echo "$before_colon_str$after_colon_str";
        }
        die();
    }
}

/**
 * This class encapsulates the options
 */
class PharseOption{
    # constants for type-checking
    const PHARSE_NUMBER  = 'number';
    const PHARSE_INTEGER = 'integer';
    const PHARSE_STRING  = 'string';

    public $description;
    public $default;
    public $long;
    public $name;
    public $type;
    public $required;
    public $short;
    public $value;
    
    /**
     * Construct the Pharse options
     *
     * @param string $name             The name of the option
     * @param array $data              The option data
     */
    public function __construct($name, $data){
        # map the array to object properties
        $this->description  = trim((string) $data['description']);
        $this->default      = (isset($data['default'])) ? $data['default'] : null;
        $this->long         = "--" . str_replace('_', '-', $name);
        $this->name         = $name;
        $this->type         = (isset($data['type'])) ? strtolower($data['type']) : null;
        $this->required     = @(bool) $data['required'];
        
        # determine the best short option to use
        $short_candidate    = (isset($data['short'])) ? $data['short'] : substr($this->name, 0, 1);
        $short_accepted     = false;
        do{
            if(!isset(Pharse::$shorts[$short_candidate])){
                # map the short argument to the long argument    
                Pharse::$shorts[$short_candidate] = $this->name;
                $this->short    = '-' . $short_candidate;
                $short_accepted = true;
            } else {
                # wrap to a on z
                # @bug (sort of): this will fail if all 26 lowercase letters are
                # already being used as short options
                # might want to step through capitals here too
                $ord = (ord($short_candidate) >= 122) ? 97 : ord($short_candidate) + 1;
                $short_candidate = chr($ord);
            }
        } while(!$short_accepted);
    }


    /**
     * Validates each option
     */
    public function validate(){
        # require that there is a description
        if($this->description === ''){
            die("Pharse library error: description for {$this->long} was not specified.\n");
        }

        # require that required variables have a value
        if($this->required && $this->value == null){
            die("Error: required value for {$this->long} was not specified.\n");
        }

        # if a type constraint was specified, verify that the constraint
        # itself is valid.
        if($this->type  != null && (
            $this->type != 'int' &&
            $this->type != self::PHARSE_INTEGER &&
            $this->type != self::PHARSE_NUMBER  &&
            $this->type != self::PHARSE_STRING
        )){
            die("Pharse library error: invalid type constraint set for {$this->long}. Must be int, integer, number, or string.\n");
        }

        # do type-checking on integers
        if($this->required &&  ($this->type === 'int' || $this->type === self::PHARSE_INTEGER)){
            is_int($this->value) or die("Error: option {$this->long} must be an integer.\n");
        }

        # do type-checking on numbers
        if($this->required && $this->type === self::PHARSE_NUMBER){
            is_numeric($this->value) or die("Error: option {$this->long} must be a number.\n");
        }

        # do type-checking on strings
        if($this->required && $this->type === self::PHARSE_STRING){
            is_string($this->value) or die("Error: option {$this->long} must be a string.\n");
        }
    }
}
