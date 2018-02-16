<?php

namespace Models;
use Sunra\PhpSimple\HtmlDomParser;
use Nette,
    Nette\Utils\Strings;


/**
 * Category management.
 */
class Rss extends \Nette\Object
{
	/** @var Nette\Database\Context */
    private $database;


	/**
	* @param cURL  $ch                     - uchwyt do cURL
	* @param int   $redirects              - przekierowania
	* @param bool  $curlopt_returntransfer - CURLOPT_RETURNTRANSFER
	* @param int   $curlopt_maxredirs      - CURLOPT_MAXREDIRS
	* @param bool  $curlopt_header         - CURLOPT_HEADER
	* @return mixed
	* @author Paweł Antczak
	* @url http://antczak.org/2009/12/curl-rozwiazanie-problemu-z-curlopt_followlocation/
	*/
	private function curl_redirect_exec($ch, &$redirects, $curlopt_returntransfer = false, $curlopt_maxredirs = 10, $curlopt_header = false) {
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$exceeded_max_redirects = $curlopt_maxredirs > $redirects;
		$exist_more_redirects = false;
		if ($http_code == 301 || $http_code == 302) {
			if ($exceeded_max_redirects) {
				list($header) = explode("\r\n\r\n", $data, 2);
				$matches = array();
				preg_match('/(Location:|URI:)(.*?)\n/', $header, $matches);
				$url = trim(array_pop($matches));
				$url_parsed = parse_url($url);
				if (isset($url_parsed)) {
					curl_setopt($ch, CURLOPT_URL, $url);
					$redirects++;
					return $this->curl_redirect_exec($ch, $redirects, $curlopt_returntransfer, $curlopt_maxredirs, $curlopt_header);
				}
			}
			else {
				$exist_more_redirects = true;
			}
		}
		if ($data !== false) {
			if (!$curlopt_header)
				list(,$data) = explode("\r\n\r\n", $data, 2);
			if ($exist_more_redirects) return false;
			if ($curlopt_returntransfer) {
				return $data;
			}
			else {
				echo $data;
				if (curl_errno($ch) === 0) return true;
				else return false;
			}
		}
		else {
			return false;
		}
	}

	// convert to UTF-8 by dgx
	function autoUTF($s)
	{

		// detect UTF-8
		if (preg_match('#[\x80-\x{1FF}\x{2000}-\x{3FFF}]#u', $s)) {
			return $s;
		}
		// detect binary
		if (preg_match('#[\x81\x83\x88\x90\x98]#', $s)) {
				return null;
		}
		// detect WINDOWS-1250
		if (preg_match('#[\x7F-\x9F\xBC]#', $s)) {
			return iconv('WINDOWS-1250', 'UTF-8', $s);
		}

		// assume ISO-8859-2
		//return iconv('ISO-8859-2', 'UTF-8', $s);
		// assume binary
		return null;
	}

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

	public function getChannelByKey($key) {
		return $this->database->fetch("select *"
				. " from rss_channels"
				. " where `key`=?;",$key);
	}

	public function getChannels() {
		return $this->database->fetchAll("SELECT *"
				. " FROM rss_channels;");
	}

	public function getLobChannels() {
		return $this->database->fetchAll("SELECT *"
				. " FROM lob_channels;");
	}
	public function getLobItemByPostId($id) {
		return $this->database->fetch("SELECT * FROM lob_items where post_id=?;",$id);
	}
	public function getLobs($limit, $offset) {
        return $this->database->fetchAll("SELECT *
                    FROM lob_items
                    ORDER BY post_id DESC
                    LIMIT ? OFFSET ?;",$limit, $offset);

	}
	public function loadChannels() {
		$stop = $this->database->fetchField("select max(unix_timestamp(pub_date))>(unix_timestamp(now())-100) from rss_items;");
		if ($stop) {
			return;
		}
		$channels = $this->getChannels();
		foreach ($channels as $channel) {
	        $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $channel['link']);
			$redirects = 0;
	        $page = $this->curl_redirect_exec($ch, $redirects, true);
		    curl_close($ch);
			if (empty($page)) {
				continue;
			}
			$html = HtmlDomParser::str_get_html( $page );
			foreach($html->find('.postbody .content a.postlink') as $element) {
				$exists = $this->getItemByLinkChannel($element->href, $channel->id);
				if (!empty($exists)) continue;
				$arr = array('link' => $element->href,
					'rss_channel_id' => $channel['id']);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $element->href);
				$redirects = 0;
				$data = $this->curl_redirect_exec($ch, $redirects, true);
				curl_close($ch);
				$clanek = $this->autoUTF($data);
				if (empty($clanek)) continue;
				$dom = HtmlDomParser::str_get_html($clanek);
				if (empty($dom)) continue;
				$title = $dom->find('title',0);
				if (!empty($title)) {
					$arr['title'] = $title->innertext;
				} else {
					$arr['title'] = $arr['link'];
				}
				$description = $dom->find('meta[name=description]',0);
				if (!empty($description)) {
					$arr['description'] = $description->content;
				}

				$this->database->query('insert into rss_items ',$arr);
			}
		}
	}
	public function loadLob() {
		$stop = $this->database->fetchField("select max(unix_timestamp(published))>(unix_timestamp(now())-100) from lob_items;");
		if ($stop) {
			return;
		}
		$channels = $this->getLobChannels();
		foreach ($channels as $channel) {
	        $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $channel['link']);
			$redirects = 0;
	        $page = $this->curl_redirect_exec($ch, $redirects, true);
		    curl_close($ch);
			if (empty($page)) {
				continue;
			}
			$html = HtmlDomParser::str_get_html( $page );
			foreach($html->find('#page-body .post') as $element) {
				if (isset($element->id)) {
					$id = preg_replace("/[^0-9,.]/", "", $element->id);
				} else {
					continue;
				}
				$lobItem = $this->getLobItemByPostId($id);
				if (!empty($lobItem)) {
					continue;
				}
				$arr = array();
				$content = $element->find('.content',0);
				$author = $element->find('.postprofile dt',0);
				$datestring = $element->find('.author',0);
				$arr['content'] = trim($content->innertext);
				$arr['author'] = trim($author->plaintext);
				$arr['datestring'] = trim(preg_replace("/.*&raquo;/", "", $datestring->innertext));
				$arr['post_id'] = $id;
				$this->database->query('insert into lob_items ',$arr);
			}
		}
	}


	public function getItemsByKey($key, $limit=10) {
		$channel = $this->getChannelByKey($key);
		$items = $this->database->fetchAll("select *"
				. " from rss_items"
				. " where rss_channel_id=?"
				. " ORDER BY id desc"
				. " LIMIT ?",$channel['id'],$limit);
		return $items;
	}

	public function getItemByLinkChannel($link,$channel_id) {
		return $this->database->fetch("select *"
				. " from rss_items"
				. " where link=?"
				. " and rss_channel_id=?;",$link, $channel_id);
	}
}
