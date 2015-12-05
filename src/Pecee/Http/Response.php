<?php

namespace Pecee\Http;

class Response {

    /**
     * Set the http status code
     *
     * @param int $code
     * @return self $this
     */
    public function httpCode($code) {
        http_response_code($code);
        return $this;
    }

    /**
     * Redirect the response
     *
     * @param string $url
     */
    public function redirect($url) {
        $this->header('Location: ' . $url);
        die();
    }

    public function refresh() {
        $this->redirect(url());
    }

    /**
     * Add http authorisation
     * @param string $name
     * @return self $this
     */
    public function auth($name = '') {
        $this->headers([
            'WWW-Authenticate: Basic realm="' . $name . '"',
            'HTTP/1.0 401 Unauthorized'
        ]);
        return $this;
    }

    public function cache($eTag, $lastModified = 2592000) {

        $this->headers([
            'Cache-Control: public',
            'Last-Modified: ' . gmdate("D, d M Y H:i:s", $lastModified) . ' GMT',
            'Etag: ' . $eTag
        ]);

        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $lastModified ||
            isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $eTag) {

            $this->headers([
                'HTTP/1.1 304 Not Modified'
            ]);

            exit();
        }

        return $this;
    }

    /**
     * Json encode array
     * @param array $value
     */
    public function json(array $value) {
        $this->header('Content-type: application/json');
        echo json_encode($value);
        die();
    }

    /**
     * Add header to response
     * @param string $value
     * @return self $this
     */
    public function header($value) {
        header($value);
        return $this;
    }

    /**
     * Add multiple headers to response
     * @param array $headers
     * @return self $this
     */
    public function headers(array $headers) {
        foreach($headers as $header) {
            header($header);
        }
        return $this;
    }

}