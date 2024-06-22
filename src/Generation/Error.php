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
    
namespace Yoandm\Fruga\Generation;

class Error
{

    public $currentTpl;

    public function __construct(){

    }

    public function catch($level, $message, $file, $line){
        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    public function shutdown(){

        $error = error_get_last();

        if ($error === null) {
            return;
        } else {
            echo '** Error during website generation from templates. **' . "\n\n";
            echo '  ' . $this->currentTpl . "\n\n";
            echo '*****************************************************' . "\n\n";
            print_r($error['message']);
        }


    }
}
