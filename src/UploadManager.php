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
        $endpoint,
        $key,
        $publicPrefix = 'https://cdns.klimg.com/some-gcs-bucket/'
    ) {
        $this->endpoint = $endpoint;
        $this->key = $key;
        $this->publicPrefix = $publicPrefix;
    }

    /**
     * poll the files to be send later
     *
     * @param array|string $files
     * @param string $path
     * @return UploadManager
     */
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

    /**
     * send the file syncedly, without polling
     *
     * @param array|string $files
     * @param string $path
     * @return UploadManager
     */
    public function syncSend($files, $path)
    {
        return $this->send($files, $path)->execute();
    }

    /**
     * execute the tasklist
     *
     * @return array<Response>
     */
    public function execute()
    {
        $result = array();
        $toBeDispatched = $this->tasks;
        foreach ($toBeDispatched as $filename => $task) {
            $result[$filename] = $this->dispatch($task);
        }

        return $result;
    }

    /**
     * execute the task
     *
     * @param Task $task
     * @return Response
     */
    public function dispatch($task)
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
     * @param Response $response
     * @param string $filename
     * @return Response
     */
    private function handleResponse($response, $task)
    {
        if ($response->isSuccess()) {
            $response->setResult(
                $this->publicPrefix . $task->getCloudFilePath()
            );

            if (isset($this->tasks[$task->getFileName()])) {
                unset($this->tasks[$task->getFileName()]);
            }
        }

        return $response;
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

    /**
     * clean up tasklist one last time before destruction
     */
    public function __destruct()
    {
        if (!empty($this->tasks)) {
            $this->execute();
        }

        unset($this->tasks);
    }
}
