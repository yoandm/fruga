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

class Local
{

    private $config;

    public function __construct($config){
        $this->config = $config;
    }

    public function connect(){

        if(! isset($this->config->data['path']) || empty($this->config->data['path'])){
            return 0;
        }

        return 1;
    }

    public function mkdir($dir){
        return mkdir($this->config->data['path'] . DIRECTORY_SEPARATOR . $dir);
    }

    public function rmdir($dir){
        return rmdir($this->config->data['path'] . DIRECTORY_SEPARATOR . $dir);
    }

    public function put($src, $dst){
        return copy($src, $this->config->data['path'] . DIRECTORY_SEPARATOR . $dst);
    }


    public function delete($file){
        return unlink($this->config->data['path'] . DIRECTORY_SEPARATOR . $file);
    }

    public function disconnect(){
        return 1;
    }

    
}