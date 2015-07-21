<?php
/**
 * @link http://zenothing.com/
 */

namespace app\behaviors;


use app\JournalException;
use Yii;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\base\Controller;
use yii\web\ForbiddenHttpException;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class Access extends Behavior {
    public $admin;
    public $manager;
    public $plain;
    public $guest;

    public function events() {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction'
        ];
    }

    public function beforeAction(ActionEvent $event) {
        $event->handled = !$this->check($event->action->id);
        if ($event->handled) {
            throw new ForbiddenHttpException("Forbidden");
        }
    }

    public function check($action) {
        $user = Yii::$app->user;
        $roles = ['admin', 'manager', 'plain', 'guest'];
        $role = null;
        foreach($roles as $role) {
            $actions = $this->$role;
            if (is_array($actions) && in_array($action, $actions)) {
                break;
            }
        }
        if ('guest' == $role) {
            return true;
        }
        if ($user->isGuest) {
            return false;
        }
        if ('plain' == $role) {
            return true;
        }
        if ($user->identity->isAdmin()) {
            return true;
        }
        if ($user->identity->isManager() && 'manager' == $role) {
            return true;
        }
        return false;
    }
}
