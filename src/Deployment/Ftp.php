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

class Ftp
{

    private $config;
    private $handle;

    public function __construct($config){
        $this->config = $config;
    }

    public function connect(){

        if(! isset($this->config->data['host']) || ! isset($this->config->data['login']) || ! isset($this->config->data['password'])){
            return 0;
        }

        $port = 21;

        if($this->config->data['port'] && (int) $this->config->data['port']){
            $port = $this->config->data['port'];
        }

        $this->handle = ftp_connect($this->config->data['host'], $port);
        $login_result = ftp_login($this->handle, $this->config->data['login'], $this->config->data['password']);

        if ((!$this->handle) || (!$login_result)) {
            return 0;
        }

        if(isset($this->config->data['path']) && ! empty($this->config->data['path'])){
            if(! ftp_chdir($this->handle, $this->config->data['path'])){
                return 0;
            }
        }

        if(isset($this->config->data['passive']) && (int) $this->config->data['passive']){
            if(! ftp_pasv($this->handle, true)){
                return 0;
            }
        }
                   
        return 1;
    }

    public function mkdir($dir){
        if(! ftp_mkdir($this->handle, $dir)){
            return 0;
        }

        return 1;
    }

    public function rmdir($dir){
        if(! ftp_rmdir($this->handle, $dir)){
            return 0;
        } 

        return 1;
    }

    public function put($src, $dst){
         if(! ftp_put($this->handle, $dst, $src)){
            return 0;
        } 

        return 1;
    }

    public function delete($file){
        if(! ftp_delete($this->handle, $file)){
            return 0;
        }

        return 1;
    }

    public function disconnect(){
        ftp_close($this->handle);
    }
}