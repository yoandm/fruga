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

namespace Yoandm\Fruga\Deployment;

use Yoandm\Fruga\Configuration\Config;
use Yoandm\Fruga\Tools\File;

class Deployer
{

    private const DS = DIRECTORY_SEPARATOR;
    private const BASE_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
    
    private $siteName;
    private $profile;
    private $method;
    private $config;

    public function __construct($siteName, $profile){

        $this->siteName = $siteName;
        $this->profile = $profile;

        $this->config = new Config();

        if(file_exists(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'configuration' . self::DS . 'deploy' . self::DS . $profile . '.json')){
            $json = json_decode(file_get_contents(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'configuration' . self::DS . 'deploy' . self::DS . $profile . '.json'), 1);
            $this->config->data = $json['config'];
        }

        $this->method = $json['type'];

    }

    public function deploy(){

        /* Loading connector */
        $class = '\Yoandm\Fruga\Deployment\\' . ucfirst($this->method);

        if(class_exists($class)){
            $obj = new $class($this->config);
            $obj->connect();

            if(! isset($this->config->data['path']) || empty($this->config->data['path'])){
                return 0;
            }

            $history = array();
            $outputPathSrc = self::BASE_DIR . '/sites/' . $this->siteName . self::DS . 'output';
            $outputPathDst = $this->config->data['path'];

            $deployed = array('dirs' => array(), 'files' => array());

            if(! file_exists(self::BASE_DIR . '/sites/' . $this->siteName . self::DS . '.deployed')){
                mkdir(self::BASE_DIR . '/sites/' . $this->siteName . self::DS . '.deployed');
            } else { // Load last deployed files history
                if(file_exists(self::BASE_DIR . '/sites/' . $this->siteName . self::DS . '.deployed' . self::DS . $this->profile . '.json')){
                    $deployed = json_decode(file_get_contents(self::BASE_DIR . '/sites/' . $this->siteName . self::DS . '.deployed' . self::DS . $this->profile . '.json'), 1);
                }
            }

            if(! file_exists(self::BASE_DIR . '/sites/' . $this->siteName . self::DS . '.generated')){
                echo '** You must generate your website first **' . "\n";
            }

            $generated = json_decode(file_get_contents(self::BASE_DIR . '/sites/' . $this->siteName . self::DS . '.generated'), 1); 
            $newDeployed = array('dirs' => array(), 'files' => array());

            /* Is there any folders / files not present in generated files but in deployed history ? Delete theme */
            $files = array();
            foreach($generated['files'] as $f){
                $files[$f['file']] = $f['md5'];
            }

            foreach($deployed['files'] as $f){
                if(! isset($files[$f['file']])){
                    echo 'Delete ' . $f['file'] . "\n";
                    $obj->delete($outputPathDst . DIRECTORY_SEPARATOR . $f['file']);
                }

            }
            
            foreach(array_reverse($deployed['dirs']) as $dir){
                if(! in_array($dir, $generated['dirs'])){
                    echo 'Delete ' . $dir . "\n";
                    $obj->rmdir($outputPathDst . DIRECTORY_SEPARATOR . $dir);
                }
            }

            /* Create new folders */

            foreach($generated['dirs'] as $dir){

                if(! in_array($dir, $deployed['dirs'])){
                    $obj->mkdir($outputPathDst  . DIRECTORY_SEPARATOR . $dir);
                    echo 'Create ' . $dir . "\n";
                    $deployed['dirs'][] = $dir;
                }

                $newDeployed['dirs'][] = $dir;

            }


            /* Create or update files */
            $files = array();
            foreach($deployed['files'] as $f){
                $files[$f['file']] = $f['md5'];
            }

            foreach($generated['files'] as $f){

                if(! isset($files[$f['file']]) || $f['md5'] !==  $files[$f['file']]){
                    if( ! isset($files[$f['file']]))
                        echo 'Create ' . $f['file'] . "\n";
                    else
                        echo 'Update ' . $f['file'] . "\n";

                    $obj->put($outputPathSrc . DIRECTORY_SEPARATOR . $f['file'], $outputPathDst . DIRECTORY_SEPARATOR . $f['file']);
                    
                    $newDeployed['files'][] = array('file' => $f['file'], 'md5' => $f['md5']);

                } else {
                    $newDeployed['files'][] = array('file' => $f['file'], 'md5' => $files[$f['file']]);
                }

            }

            $obj->disconnect();

            file_put_contents(self::BASE_DIR . '/sites/' . $this->siteName . self::DS . '.deployed' . self::DS . $this->profile . '.json', json_encode($newDeployed));

        } else {
            return 0;
        }



        return 1;
    }



}