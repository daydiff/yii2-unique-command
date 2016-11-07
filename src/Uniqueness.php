<?php

namespace Daydiff\UniqueCommand;

trait Uniqueness
{
    /**
     * Starts a command for a given pid and action.
     *
     * Can't control uniqueness on Windows systems: just logs INFO message and return to main flow.
     * Ends application with zero code if a command is already running. It's recommended to use unique
     * command identifier. For example: if you want every action of Yii console command to be unique
     * you should use different ids for them. You can make them like this: [command_name]_[action_name].
     *
     * @param string $commandId A command ID.
     * @return null
     */
    public function start($commandId)
    {
        $pid = getmypid();

        if ('\\' == DIRECTORY_SEPARATOR) {
            \Yii::info('Can\'t control uniqueness on Windows system');
            return;
        }

        if (!$this->canBeStarted($commandId)) {
            \Yii::info('Already running');
            \Yii::$app->end(0);
        }

        $this->writePid($pid, $commandId);
    }

    /**
     * Stops a command for a given actionID.
     *
     * @param string $commandId An action ID.
     */
    public function stop($commandId)
    {
        $pidFile = $this->getPidFile($commandId);
        if (file_exists($pidFile)) {
            unlink($pidFile);
        }
    }

    /**
     * @param string $commandId
     * @return boolean
     */
    private function canBeStarted($commandId)
    {
        if (file_exists($this->getPidFile($commandId)) && $this->isAlreadyRunning($commandId)) {
            return false;
        }

        return true;
    }

    /**
     * @param integer $pid
     * @param string $commandId
     */
    private function writePid($pid, $commandId)
    {
        $pidFile = $this->getPidFile($commandId);
        if (false === file_put_contents($pidFile, $pid)) {
            \Yii::error('Failed to write pid to ' . $pidFile);
            \Yii::$app->end(1);
        }
    }

    /**
     * @param string $commandId
     * @return string
     */
    private function getPidFile($commandId)
    {
        $pid = "@app/runtime/{$commandId}.pid";
        return \Yii::getAlias($pid);
    }

    /**
     * @param string $commandId
     * @return boolean
     */
    private function isAlreadyRunning($commandId)
    {
        $pidFile = $this->getPidFile($commandId);

        if (false === ($pid = file_get_contents($pidFile))) {
            \Yii::error('Can\'t read pid from ' . $pidFile);
            \Yii::$app->end(1);
        }

        $command = sprintf('ps -p%s -o pid=', escapeshellarg($pid));
        $is = exec($command);

        return (bool)$is;
    }

}