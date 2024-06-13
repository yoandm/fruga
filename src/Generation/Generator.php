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

use Yoandm\Fruga\Configuration\Config;
use Yoandm\Fruga\Tools\File;

class Generator
{

	private const DS = DIRECTORY_SEPARATOR;
	private const BASE_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
	public static $relativePath;
	private $configSite;
	private $configTheme;
	private $siteName;

	public function __construct($siteName, $profile = 'site'){

		$this->siteName = $siteName;

		$this->configSite = new Config();
		$this->configTheme = new Config();
		
		$this->configSite->data = json_decode(file_get_contents(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'configuration' . self::DS . 'site' . self::DS . $profile . '.json'), 1);
		$this->configTheme->data =  json_decode(file_get_contents(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'configuration' . self::DS . 'theme' . self::DS . 'theme.json'), 1);
	}

	public function generate(){

		if(! file_exists(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName)){
			return 0;
		}

		$baseDir = self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName .  self::DS . 'pages';
		$config = new Config();

		$generated = array();

		File::rmContent(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'output');
		mkdir(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'output');

		$page = new Page($baseDir, $this->configSite);
		$lstDir = $page->getAllDirInfos(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName .  self::DS . 'pages', self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName .  self::DS . 'pages');


		foreach($lstDir as $dir => $cleanDir){

			$saveDir = $dir;

			if($dir === '/'){
				$dir = '';
			}

			if($saveDir === '/'){
				$level = 0;
			}
			else{
				$level = substr_count($saveDir, '/');
			}

			$relativePath = '';
			if($level > 0)
				$relativePath = str_repeat('../', $level);

			Page::$relativePath = $relativePath;

			$tp = new Page($baseDir, $this->configSite);

			if($mdFile = $tp->getMdFile(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName .  self::DS . 'pages' . $dir)){

				$page = new Page($baseDir, $this->configSite);
				if($page->fetch($dir . self::DS . $mdFile)){

					if(! empty($dir)){
						mkdir(self::BASE_DIR . self::DS  . 'sites' . self::DS  . $this->siteName . self::DS . 'output' . self::DS  . $page->get('link'));
						$generated['dirs'][] = rtrim($page->get('link'), '/\\');
					}

					$nameFile = preg_match('/(.*).md/', $mdFile, $res);

					ob_start();
					require(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'themes' . self::DS . $this->configTheme->data['name'] . self::DS . 'templates' . self::DS . $res[1] . '.php');
					$content = ob_get_clean();

					file_put_contents(self::BASE_DIR . self::DS  . 'sites' . self::DS  . $this->siteName . self::DS . 'output' . self::DS  . $page->get('link') . 'index.html', $content);

					$link = rtrim($page->get('link'), '/\\') . self::DS . 'index.html';
					if($link === '/index.html')
						$link = 'index.html';
					
					$generated['files'][] = array('file' => $link, 'md5' => md5($content));

				}

			}

		}

		$res = File::copyContent(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'themes' . self::DS . $this->configTheme->data['name'], self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'output', array('templates'));

		if(isset($res['dirs'])){
			for($i = 0; $i < count($res['dirs']); $i++){
				$res['dirs'][$i] = str_replace(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'output' . self::DS, '', $res['dirs'][$i]);
			}
			$generated['dirs'] = array_merge($generated['dirs'], $res['dirs']);
		}

		if(isset($res['files'])){
			for($i = 0; $i < count($res['files']); $i++){
				$res['files'][$i]['file'] = str_replace(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'output' . self::DS, '', $res['files'][$i]['file']);
			}
			$generated['files'] = array_merge($generated['files'], $res['files']);
		}

		mkdir(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'output' . self::DS . 'medias');
		$generated['dirs'][] = 'medias';

		$res = File::copyContent(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'medias', self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'output' . self::DS . 'medias');

		if(isset($res['dirs'])){
			$generated['dirs'] = array_merge($generated['dirs'], $res['dirs']);
		}

		if(isset($res['files'])){
			for($i = 0; $i < count($res['files']); $i++){
				$res['files'][$i]['file'] = str_replace(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . 'output' . self::DS, '', $res['files'][$i]['file']);
			}
			$generated['files'] = array_merge($generated['files'], $res['files']);
		}


		if(isset($this->configSite->data['extras']) && is_array($this->configSite->data['extras']) &&  count($this->configSite->data['extras'])){
				foreach($this->configSite->data['extras'] as $extra){
					$class = '\Yoandm\Fruga\Generation\Extras\\' . ucfirst($extra);
					$configFile = self::BASE_DIR . self::DS  . 'sites' . self::DS  . $this->siteName . self::DS  . 'configuration' . self::DS . 'extras' . self::DS  . $extra . '.json';
				
					if(class_exists($class) && file_exists($configFile)){
						$config = json_decode(file_get_contents($configFile), 1);
						$obj = new $class(self::BASE_DIR . self::DS  . 'sites' . self::DS  . $this->siteName . self::DS, $this->configSite, $config);
						$res = $obj->after();


						if(isset($res['generated']['dirs'])){
							$generated['dirs'] = array_merge($generated['dirs'], $res['generated']['dirs']);
						}

						if(isset($res['generated']['files'])){
							$generated['files'] = array_merge($generated['files'], $res['generated']['files']);
						}

					}
				}

		}

		file_put_contents(self::BASE_DIR . self::DS . 'sites' . self::DS . $this->siteName . self::DS . '.generated', json_encode($generated));
		
		return 1;
	}	
}