<?php

namespace Klimg\GCS;

class UploadManager
{
    private $key;
    private $endpoint;
    private $publicPrefix;

    /**
     * list of task to be send
     *
     * @var array<Task>
     */
    private $tasks;

    public function __construct(
        $endpoint = self::DEFAULT_ENDPOINT,
        $key = self::DEFAULT_KEY,
        $publicPrefix = ''
    ) {
        $this->endpoint = $endpoint;
        $this->key = $key;
        $this->publicPrefix = $publicPrefix;
    }

    public function send($files, $path)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                $this->registerTask($file, $path);
            }

            return $this;
        }

        $this->registerTask($files, $path);

        return $this;
    }

    public function syncSend($files, $path)
    {
        return $this->send($files, $path)->executeAll();
    }

    public function executeAll()
    {
        $result = array();
        $toBeDispatched = $this->tasks;
        foreach ($toBeDispatched as $filename => $task) {
            $result[$filename] = $this->execute($task);
        }

        return $result;
    }

    /**
     * execute the task
     *
     * @param Task $task
     * @return array
     */
    public function execute($task)
    {
        return $this->handleResponse(
            $task->dispatch(
                $this->endpoint,
                $this->key
            ),
            $task->getFilename()
        );
    }

    /**
     * handle and filter out task response
     *
     * @param array $response
     * @param string $filename
     * @return array
     */
    private function handleResponse($response, $task)
    {
        if ($response['success']) {
            $cloudPath = $task->getCloudFilePath();

            if (isset($this->tasks[$task->getFileName()])) {
                unset($this->tasks[$task->getFileName()]);
            }

            return array(
                'success' => true,
                'public_url' => $this->publicPrefix . $cloudPath
            );
        }

        return array(
            'success' => false,
            'err' => $response['err']
        );
    }

    /**
     * register task to tasklist
     *
     * @param string $file
     * @param string $path
     * @return Task
     */
    private function registerTask($file, $path)
    {
        try {
            return $this->task[$file] = new Task($file, $path);
        } catch (\Exception $err) {
            throw $err;
        }
    }
}
