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

use Yoandm\Fruga\Generation\Page;

class Rss extends Extra
{

	public function __construct($basePath, $configSite, $configExtra){
		parent::__construct($basePath, $configSite, $configExtra);
	}

	public function after(){

		$this->configSite->data['relativeLinks'] = 0;

		$page = new Page($this->basePath . 'pages', $this->configSite);

		if(! isset($this->configExtra[0]['pagePath']))
			return 0;

		foreach($this->configExtra as $config){

			$title = isset($config['title']) ? $config['title'] : 'Untitled';
			$description = isset($config['description']) ? $config['description'] : '';
			$creator = isset($config['creator']) ? $config['creator'] : 'Anonymous';

				$xml = '';
				$xml = '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
		<channel>
			<title> ' . $title . '</title>
			<link>' . $this->configSite->data['url'] . $config['pagePath'] . '</link>
			<description>'. $description .'</description>
	      
	';
				$articles = $page->find($config['pagePath'])->children->sort('pos', 'desc')->slice(0,10);

				foreach($articles as $article){
					$datetime = new \DateTime($article->get('date'));

				$xml .= '
	<item>
	  <title>' . $article->get('title') . '</title>
	  <link>' . $article->get('link') . '</link>
	  <description><![CDATA[' . $article->get('content') . ']]></description>
	  <dc:creator>'. $creator . '</dc:creator>
	  <dc:date>' . $datetime->format(\DateTime::ATOM) . '</dc:date>
	</item>';

				}


			$xml .= '	</channel>
	</rss>';

			if(isset($config['outputFilename']) && isset($config['outputDir']) && file_exists($this->basePath . 'output' . $config['outputDir'])){
					file_put_contents($this->basePath . 'output' . $config['outputDir'] . DIRECTORY_SEPARATOR . $config['outputFilename'], $xml);
					$generated['files'][] = array('file' => ltrim($config['outputDir'] . DIRECTORY_SEPARATOR . $config['outputFilename'], '/\\'), 'md5' => md5($xml));
			}

		}

			return array('generated' => $generated);

	}


}
