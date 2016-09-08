<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class WxController extends Controller
{
    public static $auth = false;
    public static $check = false;
    protected $connectionName = 'mysql';
    
    public function __construct()
    {

    }

    protected function getModel($model, $Connection = false)
    {
        $model = "App\\Model\\".$model;
        $model .= 'Model';
        $model = new $model;
        if(empty($Connection)){
            $Connection = $this->connectionName;
        }
        return $model->setConnection($Connection);
    }
    
  	protected function newModel($model)
    {
        $model = "App\\Model\\".$model;
        $model .= 'Model';
        return new $model;
    }

}
