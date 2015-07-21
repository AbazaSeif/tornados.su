<?php
/**
 * @link http://zenothing.com/
*/
namespace tests;


use DOMDocument;
use DOMXPath;

class Form {
    public $name;
    public $url;
    public $raw;
    public $dom;
    public $xpath;
    public $cookies;
    public $headers = [];
    public $send_headers = [
        'Accept-Language' => 'ru,en-US;q=0.8,en;q=0.6',
        'Accept' => 'text/html',
        'User-Agent' => 'wget/1.16',
        'Origin' => 'http://localhost',
        'Connection' => 'close',
        'Cache-Control' => 'max-age=0',
        'Content-Type' => 'application/x-www-form-urlencoded',
    ];

    public function __construct($url, $name = null) {
        $this->name = $name;
        $this->init($url);
    }

    public function init($url, $headers = null, $raw = null) {
        if (!$headers) {
            $raw = file_get_contents($this->origin() . $url);
            $headers = $http_response_header;
        }
        echo "$url\n";
        $this->url = $url;
        $this->raw = $raw;
        foreach($headers as $header) {
//            echo "$header\n";
            if (preg_match('|^([^:]+):\s+(.*)$|', $header, $kv)) {
                $name = strtolower($kv[1]);
                $value = $kv[2];
                if (isset($this->headers[$name])) {
                    if (!is_array($this->headers[$name])) {
                        $this->headers[$name] = array($this->headers[$name]);
                    }
                    $this->headers[$name][] = $value;
                } else {
                    $this->headers[$name] = $value;
                }
            }
            else {
//                echo 'Wrong header: ' . $header;
            }
        }
//        echo "\n";

        if ($this->headers['set-cookie']) {
            $cookies = $this->headers['set-cookie'];
            if (!is_array($cookies)) {
                $cookies = [$cookies];
            }
            foreach($cookies as $cookie) {
                if (preg_match('|^([^=]+)=([^;]+)|', $cookie, $kv)) {
                    $this->cookies[$kv[1]] = $kv[2];
                }
            }
        }

        $this->raw = preg_replace('|>\\s+<|', '><', $this->raw);
        $this->raw = preg_replace('|\\s+|', ' ', $this->raw);
        $this->dom = new DOMDocument();
        @$this->dom->loadHTML($this->raw);
        $this->xpath = new DOMXPath($this->dom);

        return $raw;
    }

    public function origin() {
        return $this->send_headers['Origin'];
    }

    /**
     * @param $query
     * @return \DOMNodeList
     */
    public function query($query) {
        return $this->xpath->query($query);
    }

    /**
     * @return \DOMElement
     */
    public function form() {
        return $this->dom->getElementsByTagName('form')->item(0);
    }

    /**
     * @param $name
     * @return \DOMElement
     */
    public function field($name) {
        return $this->query("//input[@name='$this->name[$name]']")->item(0);
    }

    /**
     * @return array
     */
    public function fields() {
        $inputs = $this->query('//input[@name]');
        $fields = [];
        foreach($inputs as $input) {
            $fields[$input->getAttribute('name')] = $input->getAttribute('value');
        }
        return $fields;
    }

    public function fill($attributes) {
        foreach($attributes as $name => $value) {
            $field = $this->field($name);
            $field->setAttribute('value', $value);
        }
    }

    public function send() {
        $content = http_build_query($this->fields());
        $headers = $this->headers([
            'Content-Length' => strlen($content),
        ]);
//        echo "$content\n\n";
        $form = $this->form();
        $url = $form->getAttribute('action');
        print_r($headers);
        $raw = file_get_contents($this->origin() . $url, false, stream_context_create([
            'http' => [
                'method' => strtoupper($form->getAttribute('method')),
                'header'  => $headers,
                'content' => $content
            ]
        ]));
        return $this->init($url, $http_response_header, $raw);
    }

    public function headers($headers = []) {
        $headers['Referer'] = $this->origin() . $this->url;
        $send_headers = array_merge($this->send_headers);
        if (!empty($this->cookies)) {
            $send_headers['Cookie'] = http_build_query($this->cookies, null, '; ');
        }
        foreach($send_headers as $name => $value)
            $headers[] = "$name: $value\r\n";
        return implode('', $headers);
    }

    public function go($url) {
        $raw = file_get_contents($this->origin() . $url, false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => $this->headers(),
            ]
        ]));
        return $this->init($url, $http_response_header, $raw);
    }
}
