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

    private $publicUrl;


    public function __construct($success, $info, $err)
    {
        $this->success = $success;
        $this->info = $info;
        $this->err = $err;
    }

    /**
     * success response or not
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * info fromresponse
     *
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * error notif message
     *
     * @return array
     */
    public function getError()
    {
        return $this->err;
    }

    /**
     * set the public url result for ease of mapping
     *
     * @param string $publicUrl
     * @return void
     */
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
