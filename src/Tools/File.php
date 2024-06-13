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
    
namespace Yoandm\Fruga\Tools;

use Yoandm\Fruga\Generation\Cache;

class File
{

    public static function rmContent($dir) { 

        if(is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir.DIRECTORY_SEPARATOR.$object))
                        self::rmContent($dir. DIRECTORY_SEPARATOR .$object);
                    else
                        unlink($dir. DIRECTORY_SEPARATOR .$object); 
                } 
            }   

            rmdir($dir);   
        } 
     }

    public static function copyContent($src, $dst, $exclDir = array()) {

        $dir = opendir($src);
        $generated = array('dirs' => array(), 'files' => array());

        if(! file_exists($dst)){
            mkdir($dst);
            $generated['dirs'][] = $dst;
        }

        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                    if(! in_array($file, $exclDir)) {
                        $res = self::copyContent($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file, $exclDir);
                    
                        $generated['dirs'] = array_merge($generated['dirs'], $res['dirs']);
                        $generated['files'] = array_merge($generated['files'], $res['files']);  
                    }

                }

                else { 
                    copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                    $generated['files'][] = array('file' => $dst . DIRECTORY_SEPARATOR . $file, 'md5' => md5(file_get_contents($src . DIRECTORY_SEPARATOR . $file)));
                }
            }
        }

        closedir($dir);

        return $generated;
    }

    public static function getAllDir($path, $root, $tabDir = array()){

        if(is_dir($path)) {
            $objects = scandir($path);
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (is_dir($path. DIRECTORY_SEPARATOR . $object) && !is_link($path . DIRECTORY_SEPARATOR . $object))
                        $tabDir = self::getAllDir($path. DIRECTORY_SEPARATOR .$object, $root, $tabDir);
                } 
            }   

            $tabDir[] = str_replace($root, '', $path);

            return $tabDir;
        }       
    }
}