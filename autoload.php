<?php  

    define('DIR_APP', dirname(__FILE__));
    
    if(!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
    }

    spl_autoload_register(function ($class) {
        if(substr($class, 0, 6) === 'Yoandm'){
            $parts = explode('\\', $class);
            $path = '';
            for($i = 2; $i < count($parts) - 1; $i ++)
                        $path .= $parts[$i] . DS;

            $path .= $parts[$i] . '.php';

            if(file_exists(DIR_APP . DS . 'src' . DS .  $path))
                require DIR_APP . DS . 'src' . DS . $path;
        } else if($class === 'Parsedown'){
            require DIR_APP . DS . 'lib' . DS . 'parsedown/Parsedown.php';
        }
        

    });
