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
    
namespace Yoandm\Fruga\Generation;

use Yoandm\Fruga\Tools\File;

class Page
{

	private $header;

	private $title;
	private $content;
	private $slug;
	private $link;
	private $date;
	private $pos;

	public $children;

	private $path;
	private $configSite;
	private static $cachePage;
	private static $cacheDir;
	private static $cacheMd;

	public static $relativePath;

	public function __construct($path, $configSite){
		$this->path = $path;
		$this->configSite = $configSite;
		$this->header = array();
		$this->children = array();

		if(! self::$cachePage){
			self::$cachePage = new Cache();
		}

		if(! self::$cacheDir){
			self::$cacheDir = new Cache();
		}	

		if(! self::$cacheMd){
			self::$cacheMd = new Cache();
		}				
	}

	public function get($key){

		switch($key){
			case 'content':
					return $this->content;
					break;
			case 'link': 
					return $this->link;
					break;
			case 'title': 
					return $this->title;
					break;	
			case 'date': 
					return $this->date;
					break;	
			case 'pos': 
					return $this->pos;
					break;														
			default:
					if(isset($this->header[$key])){
						return $this->header[$key];
					}
			break;
		}

	}

	public function fetch($page){

		$cache = 1;

		if(isset($this->configSite->data['cache']) && ! (int) $this->configSite->data['cache'])
			$cache = 0;

		if($cache){
			$res = self::$cachePage->get($page);
			if($res !== false){

				$this->title = $res['title'];
				$this->content = $res['content'];
				$this->slug = $res['slug'];
				$this->link = $res['link'];
				$this->date = $res['date'];
				$this->header = $res['header'];

				return 1;
			}
		}

		if(! file_exists($this->path . $page))
			return 0;

		$content = file_get_contents($this->path . $page);
		$content = str_replace("\r\n", "\n", $content);

		preg_match('/\-\-\-\n(.*)\n\-\-\-(.*)/s', $content, $res);

		$this->title = '';
		$this->date = date('Y-m-d H:i:s', filemtime($this->path . $page));

		$heads = explode("\n", $res[1]);

		foreach($heads as $head){
			if(preg_match('/^([^:]*): ([^\n]*)$/s', $head, $cut)){

				switch($cut[1]){
					case 'title':
						$this->title = trim($cut[2], '\'');
						$this->title = str_replace('\'\'', '\'', $this->title);
						break;

					case 'date':
						$this->date = trim($cut[2], '\'');
						$this->date = str_replace('\'\'', '\'', $this->date);
						break;

					case 'slug':
						$this->slug = trim($cut[2], '\'');
						break;	

					default: 
						$this->header[$cut[1]] = trim($cut[2], '\'');
						$this->header[$cut[1]] = str_replace('\'\'', '\'', $this->header[$cut[1]]);

						break;				
				}
				
				
			}
		}

		$parsedown = new \Parsedown();
		$this->content = $res[2];
		$this->content = $parsedown->text($this->content);
	
		$lstDir = $this->getAllDirInfos($this->path, $this->path);
		$this->link = '';
		if(! preg_match('/(.*\/)[^\/]*\/.*\.md/', $page, $res)){
			$this->link = '/';
		} else if($res[1] !== '/'){
			$mdFile = $this->getMdFile($this->path . DIRECTORY_SEPARATOR . $res[1]);
			$t = new Page($this->path, $this->configSite);
			$t->fetch($res[1] . $mdFile);
			$this->link = $t->link; 
		}

		preg_match('/(.*)\/.*\.md/', $page, $res);

		if(isset($this->slug) && ! empty($this->slug)){
				$this->link .= $this->slug . '/';
		} else if($res[1]){
			$cut = explode('/', $lstDir[$res[1]]);
			$this->link .=  $cut[count($cut)-1] . '/';
		}


		if($cache){
			$data = array(
				'title' => $this->title,
				'content' => $this->content,
				'slug' => $this->slug,
				'link' => $this->link,
				'date' => $this->date,				
				'header' => $this->header

			);

			self::$cachePage->set($page, $data);
		}

		return 1;
	}

	public function find($pagePath, $noChild = 0){

		$pagePath = mb_strtolower($pagePath);
		$dirs = $this->getAllDirInfos($this->path, $this->path);

		$nb = 0;

		$mdFiles = array();

		foreach($dirs as $dir => $cleanDir){

			if(strpos($cleanDir, $pagePath) === 0){
				$nb ++;

				$mdFile = $this->getMdFile($this->path . $dir);
				if($mdFile){

					$res = $dir . DIRECTORY_SEPARATOR . $mdFile;

					if($dir === '/')
						$res = '/' . ltrim($res, '/');

					$pos = 0;
					if(preg_match('/\/([0-9]*)[^\/]*$/', $dir, $search)){
						$pos = $search[1]; 
					}

					$mdFiles[] = array('file' => $res, 'path' => $cleanDir, 'pos' => $pos);					
				}

			
			}
		}
		
		$tabRes = array();

		if($nb){

			$relativeLinks = 1;

			if(isset($this->configSite->data['relativeLinks']) && ! (int) $this->configSite->data['relativeLinks'] && isset($this->configSite->data['url']))
				$relativeLinks = 0;


				if($nb === 1){
					foreach($mdFiles as $mdFile){
						$page = new Page($this->path, $this->configSite);
						$page->fetch($mdFile['file']);
						if($relativeLinks){
							$page->link = self::$relativePath . ltrim($page->link, '/');
						} else {
							$page->link = $this->configSite->data['url'] . '/' . $page->link;
						}
							$page->pos = (int) $mdFile['pos'];

							return $page;					
					}					
				} else {

					$level = substr_count($mdFiles[0]['file'], '/');

					$collection = new Collection();

					foreach($mdFiles as $mdFile){

						$page = new Page($this->path, $this->configSite);
						$page->fetch($mdFile['file']);
						
						$nbLevel = substr_count($mdFile['file'], '/');
						if($nbLevel === $level || $nbLevel == $level + 1){
							if($relativeLinks){
								$page->link = self::$relativePath . $page->link;
								
								$page->link = str_replace('//', '/', $page->link);

								if($page->link === '/')
									$page->link = './';

							} else {
								if($page->link === '/'){
									$page->link = $this->configSite->data['url'];
								}
								else {
									$page->link = $this->configSite->data['url'] . '/' . $page->link;
								}
							}

							$page->pos = (int) $mdFile['pos'];

							if($noChild)
								return $page;
						

							if($nbLevel === $level){
								if($noChild){
									return $page;
								} else {
									$resPage = clone($page);
								}
							} else {	
								$collection->add(self::find($mdFile['path'], $noChild));
							}
						}
											
					}


				}
			
				$resPage->children = $collection;
				
				return $resPage;
		} else {
			return 0;
		}
	}

	public function getAllDirInfos($path, $root, $tabDir = array()){

		if(self::$cacheDir->get($path)){
			return self::$cacheDir->get($path);
		}

		$lstDir = File::getAllDir($path, $root, $tabDir);
		$lstDir = array_reverse($lstDir);

		$tabDir = array();

		foreach($lstDir as $dir){

			$currentDir =  str_replace('/', DIRECTORY_SEPARATOR, $dir);
			if(empty($currentDir)){
				$currentDir = '/';			
			}

			$cleanCurrentDir = $currentDir;

			if(preg_match('/^\/[0-9]*\.(.*)/', $currentDir, $res)){
				$cleanCurrentDir = preg_replace('/\/[0-9]*\./', '/', $currentDir);
			}


			$tabDir[$currentDir] = mb_strtolower($cleanCurrentDir);
		}

		self::$cacheDir->set($path, $tabDir);

		return $tabDir;

	}	

	public function getMdFile($path){
		$res = self::$cacheMd->get($path);

		if($res !== false){
			return $res;
		}

		$objects = scandir($path);
		foreach ($objects as $object) { 
			if ($object != "." && $object != "..") {
				if(! is_dir($path. DIRECTORY_SEPARATOR . $object) && preg_match('/(.*).md/', $object)){
					self::$cacheMd->set($path, $object);
					return $object;
				}
			}
		}

		return 0;
	}	
}
