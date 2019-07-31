<?php

namespace Klimg\GCS;

use Exception;

class Task
{
    private $file;
    private $cloudPath;

    public function __construct($file, $cloudPath)
    {
        if (! file_exists($file)) {
            throw new Exception("file $file not exist");
        }
        $this->file = $file;
        $this->cloudPath = $this->normalizeSlash($cloudPath);
    }

    /**
     * get the intended cloud path
     *
     * @return string
     */
    public function getCloudFilePath()
    {
        return $this->cloudPath . $this->file;
    }

    public function getFileName()
    {
        return $this->file;
    }

    public function dispatch($endpoint, $key)
    {
        $delimiter = '-------------' . \uniqid();
        $bodyData = $this->buildDataFiles(
            $this->file,
            $delimiter
        );

        $curlHandler = \curl_init($endpoint);
        \curl_setopt_array(
            $curlHandler,
            array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $bodyData,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: multipart/form-data; boundary=" . $delimiter,
                    "Content-Length: " . \strlen($bodyData),
                    "auth-key: " . $key,
                    "path: " . $this->cloudPath,
                ),
            )
        );

        $result = array(
            'success' => \curl_exec($curlHandler),
            'info' => \curl_getinfo($curlHandler),
            'err' => \curl_error($curlHandler)
        );

        \curl_close($curlHandler);

        return $result;
    }

    /**
     * normalize slash on path
     *
     * @param string $string
     * @return string
     */
    private function normalizeSlash($string)
    {
        if (\substr($string, -1, 1) == '/') {
            return $string;
        }

        return $string . '/';
    }

    /**
     * build the data string for transactions
     *
     * @param array $files
     * @param string $delimiter
     * @return string
     */
    private function buildDataFiles($file, $delimiter = '-------------bola')
    {
        return "--" . $delimiter . "\r\n"
        . 'Content-Disposition: form-data; name="file"; filename="' . \pathinfo($file, PATHINFO_BASENAME) . '"' . "\r\n"
        . 'Content-Transfer-Encoding: binary' . "\r\n"
        . "\r\n"
        . \file_get_contents($file) . "\r\n"
        . "--$delimiter--\r\n";
    }
}