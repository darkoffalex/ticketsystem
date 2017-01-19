<?php

namespace app\modules\admin\controllers;

use app\models\User;
use Yii;
use yii\web\Controller;

class ProfileController extends Controller
{
    /**
     * Edit profile params
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        /* @var $model User */
        $model = User::findOne(Yii::$app->user->id);


        //if something coming from POST
        if (Yii::$app->request->isPost) {

            //old data
            $oldPassHash = $model->password_hash;
            $oldAuthKey = $model->auth_key;
            $oldRole = $model->role_id;

            //load POST data to model
            $model->load(Yii::$app->request->post());

            //if password was set - generate
            if(!empty($model->password)){
                $model->setPassword($model->password);
                $model->generateAuthKey();
                //if not - set old password
            }else{
                $model->password_hash = $oldPassHash;
                $model->auth_key = $oldAuthKey;
            }

            //if all data is valid
            if($model->validate()){
                //can't change role from this form
                $model->role_id = $oldRole;
                //set some statistic information
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;

                //update in database
                $model->update();
            }
        }

        //render form
        return $this->render('index',compact('model'));
    }
}