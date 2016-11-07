<?php

namespace Daydiff\UniqueCommand;

use yii\console\Controller;

/**
 * Description of UniqueCommandBehavior
 *
 * @author aleksandr.tabakov
 */
class UniqueCommandBehavior extends \yii\base\Behavior
{
    use Uniqueness;

    /**
     * @var array the list of actions that need to be unique
     */
    private $actions = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
            Controller::EVENT_AFTER_ACTION => 'afterAction',
        ];
    }

    /**
     * @param \yii\base\ActionEvent $event
     * @return boolean
     */
    public function beforeAction($event)
    {
        if (in_array($event->action->id, $this->actions)) {
            $this->start($this->getCommandId($event));
            return true;
        }

        return true;
    }

    /**
     * @param \yii\base\ActionEvent $event
     */
    public function afterAction($event)
    {
        if (in_array($event->action->id, $this->actions)) {
            $this->stop($this->getCommandId($event));
        }
    }

    public function setActions($actions)
    {
        $this->actions = (array)$actions;

    }

    /**
     * @param \yii\base\ActionEvent $event
     * @return string
     */
    public function getCommandId($event)
    {
        return $this->owner->id . ':' . $event->action->id;
    }
}
