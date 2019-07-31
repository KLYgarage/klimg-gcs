<?php
namespace Klimg\GCS;

/**
 * response class
 * @method bool isSuccess()
 * @method array getInfo()
 * @method array getErr()
 */
class Response
{
    private $success;
    private $info;
    private $err;

    private $result;


    public function __construct($success, $info, $err)
    {
        $this->success = $success;
        $this->info = $info;
        $this->err = $err;
    }

    public function isSuccess()
    {
        return $this->success;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getError()
    {
        return $this->err;
    }

    public function setResult($publicUrl)
    {
        $this->publicUrl = $publicUrl;
    }

    public function __toString()
    {
        if ($this->isSuccess()) {
            return $this->publicUrl;
        }

        return '';
    }
}