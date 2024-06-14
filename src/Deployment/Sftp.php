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

class Sftp
{

    private $config;
    private $handle;
    private $sftp;
    private $path;

    public function __construct($config){
        $this->config = $config;
    }

    public function connect(){

        if(! isset($this->config->data['host']) || ! isset($this->config->data['login']) || ! isset($this->config->data['password'])){
            return 0;
        }

        $port = 22;

        if($this->config->data['port'] && (int) $this->config->data['port']){
            $port = $this->config->data['port'];
        }

        $this->path = '';
        if($this->config->data['path'] && ! empty($this->config->data['path'])){
            $this->path = $this->config->data['path'] . '/';
        }

        $this->handle = ssh2_connect($this->config->data['host']);
        if(! $this->handle){
            return 0;
        }

        if(! ssh2_auth_password($this->handle, $this->config->data['login'], $this->config->data['password'])){
            return 0;
        }
        
        $this->sftp = ssh2_sftp($this->handle);

        if(! $this->sftp){
            return 0;
        }
        
        return 1;
    }

    public function mkdir($dir){
        if(! ssh2_sftp_mkdir($this->sftp, $this->path . $dir)){
            return 0;
        }
    }

    public function rmdir($dir){
        if(! ssh2_sftp_rmdir($this->sftp, $this->path . $dir)){
            return 0;
        }
    }

    public function put($src, $dst){
         if(! ssh2_scp_send($this->handle, $src, $this->path . $dst)){
            return 0;
        }
    }

    public function delete($file){
        if(! ssh2_sftp_unlink($this->sftp, $this->path . $file)){
            return 0;
        }
    }

    public function disconnect(){
        ssh2_disconnect($this->handle);
    }
}