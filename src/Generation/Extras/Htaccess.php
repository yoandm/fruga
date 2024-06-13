<?php

/*  
	Copyright (C) 2024		Yoan De Macedo  <mail@yoandm.com>                       
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

namespace Yoandm\Fruga\Generation\Extras;

class Htaccess extends Extra
{

	public function __construct($basePath, $configSite, $configExtra){
		parent::__construct($basePath, $configSite, $configExtra);
	}

	public function after(){


		$htaccess = '<IfModule mod_rewrite.c>

	RewriteEngine On' . "\n";
		if(isset($this->configExtra['removeIndexhtml']) && $this->configExtra['removeIndexhtml']){
			$htaccess .= '	RewriteRule (.*)index\.html$ /$1 [NS,R=301,L]' . "\n";
		}

		if(isset($this->configExtra['extraLines']) && is_array($this->configExtra['extraLines']) && count($this->configExtra['extraLines'])){

			foreach ($this->configExtra['extraLines'] as $extraLine) {
				$htaccess .= "\t" . $extraLine . "\n";
			}

		}
		$htaccess .= '
</IfModule>
';


			file_put_contents($this->basePath . 'output' . DIRECTORY_SEPARATOR . '.htaccess', $htaccess);

			$generated['files'][] = array('file' => '.htaccess', 'md5' => md5($htaccess));

			return array('generated' => $generated);
	}


}
