<?php
/**
 * @link http://zenothing.com/
*/
namespace tests;


use DOMDocument;
use DOMXPath;

/**
 * @property \DOMDocument $dom
 * @property \DOMXPath $xpath
 */
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
        'Accept' => '*/*',
        'User-Agent' => 'wget/1.16',
        'Origin' => 'http://localhost',
        'Connection' => 'close',
        'Cache-Control' => 'max-age=0',
    ];

    public function __construct($url, $name = null) {
        $this->name = $name;
        $this->go($url);
    }

    public function init($url, $headers = null, $raw = null) {
        echo $url . "\n";
        $this->url = $url;
        $this->raw = $raw;
        foreach($headers as $header) {
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
        }

        if (isset($this->headers['set-cookie'])) {
            if (is_string($this->headers['set-cookie'])) {
                $this->headers['set-cookie'] = [$this->headers['set-cookie']];
            }
            foreach ($this->headers['set-cookie'] as $cookie) {
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
        /**
         * @var $inputs \DOMElement[]
         */
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
            if ($field) {
                $field->setAttribute('value', $value);
            }
        }
    }

    public function send() {
        $content = http_build_query($this->fields());
        $headers = $this->headers([
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Content-Length' => strlen($content),
        ]);
        $form = $this->form();
        if ($form) {
            $url = $form->hasAttribute('action') ? $form->getAttribute('action') : $this->url;
            $isPOST = $form->hasAttribute('method') && 'GET' != strtoupper($form->getAttribute('method'));
        }
        else {
            $url = $this->url;
            $isPOST = false;
        }
        if (!$isPOST && !empty($content)) {
            $url .= '?' . $content;
        }
        $curl = curl_init($this->origin() . $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, true);
        $response_headers = [];
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $line) use ($response_headers) {
            $response_headers[] = $line;
        });
        if ($isPOST) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        }
        $raw = curl_exec($curl);
        curl_close($curl);
        return $this->init($url, $response_headers, $raw);
    }

    public function headers($headers = []) {
        $headers['Referer'] = $this->origin() . $this->url;
        $send_headers = array_merge($this->send_headers, $headers);
        if (!empty($this->cookies)) {
            $send_headers['Cookie'] = http_build_query($this->cookies, null, '; ');
        }
        $lines = [];
        foreach($send_headers as $name => $value)
            $lines[] = "$name: $value";
        return $lines;
    }

    public function go($url) {
        $curl = curl_init($this->origin() . $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        $response_headers = [];
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $line) use ($response_headers) {
            $response_headers[] = $line;
        });
        $raw = curl_exec($curl);
        curl_close($curl);
        return $this->init($url, $response_headers, $raw);
    }
}
