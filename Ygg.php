<?php

/**
 * POC permettant de parcourir le site www.yggtorrent.com et d'en extraire les torrents recherchés
 * User: Anthony Saugrain
 * Date: 09/11/2016
 * Time: 18:08
 */

require_once('lib/simple_html_dom.php');

class Ygg
{
    const BASE_URL = "https://yggtorrent.com";
    const CATEGORY_MOVIES = 2145;
    const CATEGORY_AUDIO = 2139;
    const CATEGORY_APPS = 2144;
    const CATEGORY_VIDEOGAMES = 2142;
    const CATEGORY_EBOOKS = 2140;
    const CATEGORY_EMULATION = 2141;
    const CATEGORY_GPS = 2143;
    const CATEGORY_ADULT = 2188;

    private $up;
    private $down;
    private $torrents;
    private $html;
    private $search;
    private $pagination;
    private $login;
    private $password;

    /**
     * Ygg constructor.
     * @param string $search
     * @param int $pagination
     */
    public function __construct($search = '', $pagination = 1)
    {
        $this->up = '';
        $this->down = '';
        $this->torrents = array();
        $this->search = $search;
        $this->pagination = $pagination;
        $this->login = 'username';
        $this->password = 'password';
    }

    /**
     * Get category id by category name
     * @param $category
     * @return bool|int
     */
    public static function getCategoryId($category)
    {
        switch ($category) {
            case 'movies':
                $category_id = self::CATEGORY_MOVIES;
                break;
            case 'audio':
                $category_id = self::CATEGORY_AUDIO;
                break;
            case 'apps':
                $category_id = self::CATEGORY_APPS;
                break;
            case 'videogames':
                $category_id = self::CATEGORY_VIDEOGAMES;
                break;
            case 'ebooks':
                $category_id = self::CATEGORY_EBOOKS;
                break;
            case 'emulation':
                $category_id = self::CATEGORY_EMULATION;
                break;
            case 'gps':
                $category_id = self::CATEGORY_GPS;
                break;
            case 'adult':
                $category_id = self::CATEGORY_ADULT;
                break;
            default:
                $category_id = false;
                break;
        }

        return $category_id;
    }

    /**
     * @return string ratio up
     */
    public function getUp()
    {
        return $this->up;
    }

    /**
     * @return string ratio down
     */
    public function getDown()
    {
        return $this->down;
    }

    /**
     * @return array torrents
     */
    public function getTorrents()
    {
        return $this->torrents;
    }

    /**
     * Login and store cookie
     */
    public function login()
    {
        try {
            if ($this->call('login', '/user/login') == "") {
                if (($page = $this->call('basic', '')) !== false) {
                    $this->html = $this->open($page);
                    if ($this->findLink('/user/account')) {
                        if (!$this->findRatio()) {
                            throw new Exception('Unable to find ratio');
                        }

                        return true;
                    } else {
                        throw new Exception('Unable to login');
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Generic cURL call
     * @param $type
     * @param $path
     * @return mixed
     * @throws Exception
     */
    private function call($type, $path)
    {
        try {
            // create curl resource
            $ch = curl_init();

            $url = self::BASE_URL . $path;
            if ($type == 'login') {
                $datas = "id=" . urlencode($this->login) . "&pass=" . urlencode($this->password);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
            }

            // extra headers
            $headers[] = "Accept: */*";
            $headers[] = "Connection: Keep-Alive";

            $cookie_file_path = "/Applications/XAMPP/xamppfiles/htdocs/ygg_crawler/tmp/cookies.txt";

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);


            // $output contains the output string
            $output = curl_exec($ch);

            if ($type == 'download') {
                $destination = "/Applications/XAMPP/xamppfiles/htdocs/ygg_crawler/dl/download.torrent";
                $file = fopen($destination, "w+");
                fputs($file, $output);
                fclose($file);
            }

            // close curl resource to free up system resources
            curl_close($ch);

            return $output;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Open a html file with simple html dom
     * @param $file
     * @return simple_html_dom
     * @throws Exception
     */
    private function open($file)
    {
        try {
            $html = new simple_html_dom();
            $html->load($file);

            return $html;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Find a particular link
     * @param $term
     * @return bool
     * @throws Exception
     */
    private function findLink($term)
    {
        try {
            $links = $this->html->find('a');
            foreach ($links as $link) {
                if (strpos($link->href, $term) !== false) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Extract ratios Up / Down
     * @return bool
     * @throws Exception
     */
    private function findRatio()
    {
        try {
            $links = $this->html->find('.submenu ul li a');
            foreach ($links as $link) {
                if (strpos($link->getAttribute('style'), 'color') !== false) {
                    $this->down = trim($link->innertext);
                }

                if (strpos($link->innertext, 'GB') !== false) {
                    if (strpos($link->children(0)->getAttribute('class'), 'arrow-up') !== false) {
                        $this->up = trim($link->children(1)->innertext);
                    } else {
                        $this->down = trim($link->children(0)->innertext);
                    }
                }
            }

            if ($this->up != '' && $this->down != '') {
                return true;
            }

            return false;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Search a torrent
     * @throws Exception
     */
    public function searchTorrent()
    {
        try {
            $term = urlencode($this->search);
            $this->loopForTorrent('/engine/search?q=' . $term);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Loop and find torrent in given url
     * @param $url
     */
    private function loopForTorrent($url)
    {
        for ($i = 1; $i <= $this->pagination; $i++) {
            $page = $this->call('basic', $url . '&page=' . $i);
            $this->html = $this->open($page);
            $this->extractTorrents();
        }
    }

    /**
     * Extract founded torrents
     * @return bool
     * @throws Exception
     */
    private function extractTorrents()
    {
        try {
            $lines = $this->html->find('.content-box-large table tbody tr');
            foreach ($lines as $key => $line) {
                // Do not handle first line (header)
                if ($key > 0) {
                    // Extract torrent link & name
                    $links = $line->children(0)->find('a');
                    foreach ($links as $key => $link) {
                        if ($key == 0) {
                            $name = $link->innertext;
                        }
                        if (strpos($link->href, '?id=') !== false) {
                            $href = explode('/', $link->href);
                            $href = '/engine/' . $href[4];
                            $href = '?action=download&file=' . $href;
                        }
                    }

                    if (is_null($href) || is_null($name)) {
                        continue;
                    }

                    // Extract size
                    $size = $line->children(2)->innertext;

                    // Extract seeds
                    $seeds = $line->children(3)->innertext;

                    // Extract leechs
                    $leechs = $line->children(4)->innertext;

                    $this->torrents[] = array(
                        'name' => $name,
                        'href' => $href,
                        'size' => $size,
                        'seeds' => $seeds,
                        'leechs' => $leechs
                    );
                }
            }

            if (count($this->torrents) > 0) {
                return true;
            }

            return false;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Search torrent of the moment in given category
     * @param $category
     * @throws Exception
     */
    public function searchMoment($category)
    {
        try {
            $this->loopForTorrent('/torrents/popular?category=' . $category);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Download given torrent
     * @param $url
     * @return bool
     * @throws Exception
     */
    public function download($url)
    {
        try {
            if ($this->call('download', $url) !== false) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
