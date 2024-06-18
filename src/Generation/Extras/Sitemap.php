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

namespace Yoandm\Fruga\Generation\Extras;
use Yoandm\Fruga\Generation\Page;

class Sitemap extends Extra
{

    public function __construct($basePath, $configSite, $configExtra){
        parent::__construct($basePath, $configSite, $configExtra);
    }

    public function after(){

        $page = new Page($this->basePath . 'pages/', $this->configSite);

        $res = $page->find('/');

        $lst = $this->getLinks($res);

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        if(count($lst)){
            foreach($lst as $url){

                    $sitemap .= '       <url>
            <loc>' . $url . '</loc>
        </url>' . "\n";
            }

        }
        $sitemap .= '   </urlset>';


        file_put_contents($this->basePath . 'output' . DIRECTORY_SEPARATOR . 'sitemap.xml', $sitemap);
        $generated['files'][] = array('file' => 'sitemap.xml', 'md5' => md5($sitemap));

        return array('generated' => $generated);
    }

    public function getLinks($pages){

        $lst = array();

        $lst[] = $pages->get('link');
        if(isset($pages->children) && ! is_array($pages->children)){
            foreach($pages->children as $child){
                $lst = array_merge($lst, $this->getLinks($child));
            }
       }

        return $lst;
    }

}
