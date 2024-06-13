<?php

/*  
    Copyright (C) 2024      Yoan De Macedo  <mail@yoandm.com>                       
    web : http://yoandm.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
    require 'autoload.php';
    require 'vendor/autoload.php';

    use Yoandm\Fruga\Generation\Generator;
    use Yoandm\Fruga\Deployment\Deployer;

    if($argc < 3){
        die('*** Sorry, you must specify a command and a website name ***' . "\n");
    }

    if(! in_array($argv[1], array('generate', 'deploy'))){
        die('*** Sorry, you must specify an existing command : generate, deploy ***' . "\n");
    }

    if(! file_exists(__DIR__ . '/sites/' . $argv[2])){
        die('*** Sorry, you must specify an existing website ***' . "\n");
    }

    if($argv[1] === 'generate'){
        $profile = 'site';
        if(isset($argv[3]))
            $profile = trim($argv[3]);

        $gen = new Generator($argv[2], $profile);
        if(! $gen->generate()){
            die('*** Error during generation ***' . "\n");
        } else {
            echo 'Website ' . $argv[2] . ' successfully generated !' . "\n";
        }
    } else if($argv[1] === 'deploy'){

        if($argc < 4){
            die('*** Sorry, you must specify a deployment method ***' . "\n");
        }       

        $d = new Deployer($argv[2], $argv[3]);
        if(! $d->deploy()){
            die('*** Error during deployment ***' . "\n");
        } else {
            echo 'Website ' . $argv[2] . ' successfully deployed !' . "\n";
        }
    }



