<?php

namespace Daydiff\UniqueCommand;

trait Uniqueness
{
    public function start($pid, $actionId)
    {
        if ('\\' == DIRECTORY_SEPARATOR) {
            \Yii::info('Can\'t control uniqueness on Windows system');
            return;
        }

        if (!$this->canBeStarted($actionId)) {
            \Yii::info('Already running');
            \Yii::$app->end(0);
        }

        $this->writePid($pid, $actionId);
    }

    public function stop($actionId)
    {
        $pidFile = $this->getPidFile($actionId);
        if (file_exists($pidFile)) {
            unlink($pidFile);
        }
    }

    private function canBeStarted($actionId)
    {
        if (file_exists($this->getPidFile($actionId)) && $this->isAlreadyRunning($actionId)) {
            return false;
        }

        return true;
    }

    private function writePid($pid, $actionId)
    {
        $pidFile = $this->getPidFile($actionId);
        if (false === file_put_contents($pidFile, $pid)) {
            \Yii::error('Failed to write pid to ' . $pidFile);
            \Yii::$app->end(1);
        }
    }

    private function getPidFile($actionId)
    {
        $pid = "@app/runtime/{$this->id}_{$actionId}.pid";
        return \Yii::getAlias($pid);
    }

    private function isAlreadyRunning($actionId)
    {
        $pidFile = $this->getPidFile($actionId);

        if (false === ($pid = file_get_contents($pidFile))) {
            \Yii::error('Can\'t read pid from ' . $pidFile);
            \Yii::$app->end(1);
        }

        $command = sprintf('ps -p%s -o pid=', escapeshellarg($pid));
        $is = exec($command);

        return (bool) $is;
    }

}