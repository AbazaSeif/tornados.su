<?php
/**
 * @link http://zenothing.com/
*/

namespace app\widgets;


use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

class AjaxComplete extends AutoComplete {
    public $route;

    public function init() {
        $url = Url::to($this->route);
        $this->clientOptions['source'] = new JsExpression("function(request, response) {
            $.getJSON('$url', {
                search: request.term
            }, response);
        }");
        parent::init();
    }
}
