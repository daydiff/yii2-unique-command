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
     */
    public function beforeAction($event)
    {
        if (in_array($event->action->id, $this->actions)) {
            $this->start(getmypid(), $event->action->id);
            echo "Yeah!\n";
        } else {
            echo "No :(\n";
        }
    }

    /**
     * @param \yii\base\ActionEvent $event
     */
    public function afterAction($event)
    {
        if (in_array($event->action->id, $this->actions)) {
            $this->stop($event->action->id);
        }
    }

    public function setActions($actions)
    {
        $this->actions = (array)$actions;

    }

    public function getId()
    {
        return $this->owner->id;
    }
}
