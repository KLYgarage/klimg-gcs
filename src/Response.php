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
}