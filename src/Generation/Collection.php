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

class Collection implements \Iterator
{
    private $pages;

    public function __construct(){
        $this->pages = array();
    }

    public function rewind() : void{
        reset($this->pages);
    }
    public function current() : mixed{
        return current($this->pages);
    }
    public function key() : mixed{
        $return = key($this->pages);
    }
    public function next() : void{
        next($this->pages);
    }
    public function valid() : bool{
        $key = key($this->pages);
        return ($key !== null && $key !== false);
     }

    public function add($page){
        $this->pages[] = $page;
    }

    public function first(){
        return $this->pages[0];
    }

    public function last(){
        return $this->pages[count($this->pages)-1];
    }

    public function sort($type, $order){
        $collection = new Collection();
        $collection = clone($this);

        switch($type){
            case 'pos': 
                    $collection->pages = $this->sort_pos($collection->pages, $type, $order);
                    break;

            default: 
                return $collection; 
                break;
        }

        
        return $collection;

    }

    public function sort_pos($pages, $type, $order = 'asc'){
        $ar = array();
        $newPages = array();

        foreach($pages as $p){
            $ar[] = $p->get('pos');
        }

        if($order === 'asc'){
            asort($ar);
        }
        else {
            arsort($ar);
        }

        foreach($ar as $key => $value){
            $newPages[] = $pages[$key];
        }

        return $newPages;
    }

    public function slice($offset, $length = null){

        $collection = clone($this);

        $collection->pages = array_slice($collection->pages, $offset, $length);

        return  $collection;

    }
}
