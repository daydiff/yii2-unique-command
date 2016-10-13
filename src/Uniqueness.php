<?php

namespace Daydiff\UniqueCommand;

trait Uniqueness
{
    /**
     * start - Start the uniqueness command with the given actionID.
     * End the process if the on Windows environment.
     * 
     * @param  $pid The content to write to PID file
     * @param  $actionId The ID uniqueness command to be started.
     * @return null
     */
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

    /**
     * stop - Stop the uniqueness command referring to given actionID.
     * 
     * @param  $actionId The actionID whose command is to be stopped
     */
    public function stop($actionId)
    {
        $pidFile = $this->getPidFile($actionId);
        if (file_exists($pidFile)) {
            unlink($pidFile);
        }
    }

    /**
     * canBeStarted - Check whether the uniqueness command can or cannot be started.
     * 
     * @param   $actionId The ID of the uniqueness action
     * @return  boolean
     */
    private function canBeStarted($actionId)
    {
        if (file_exists($this->getPidFile($actionId)) && $this->isAlreadyRunning($actionId)) {
            return false;
        }

        return true;
    }

    /**
     * writePid - Update the file with the given actionID
     * with the given PID.
     * Send an error and end the process if there are any errors.
     * 
     * @param  $pid The content to write into the PID file.
     * @param  $actionId The ID of the PID file to write to.
     */
    private function writePid($pid, $actionId)
    {
        $pidFile = $this->getPidFile($actionId);
        if (false === file_put_contents($pidFile, $pid)) {
            \Yii::error('Failed to write pid to ' . $pidFile);
            \Yii::$app->end(1);
        }
    }

   /**
    * getPidFile - Get the PID file that refers to the given action ID.
    * 
    * @param  $actionId The ID for which it is required to get the PID file
    * @return The PID file.
    */
   private function getPidFile($actionId)
    {
        $pid = "@app/runtime/{$this->id}_{$actionId}.pid";
        return \Yii::getAlias($pid);
    }

    /**
     * isAlreadyRunning - Check if the uniqueness commena with
     * the given actionID is already running.
     * 
     * @param  type  $actionId The ID of the uniqueness action.
     * @return boolean 
     */
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