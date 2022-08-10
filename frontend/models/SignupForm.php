<?php

namespace frontend\models;

use Exception;
use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $role;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            //['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],

            ['role', 'trim'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     * @throws Exception
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        //$user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        //$user->generateEmailVerificationToken();
        if ($user->save() /*&& $this->sendEmail($user)*/) {
            Yii::$app->authManager->assign(Yii::$app->authManager->getRole($this->role), $user->id);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function create()
    {
        if (!$this->validate()) {
            return null;
        }
        $user = new User();
        $user->username = $this->username;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        if ($user->save()) {
            if (Yii::$app->user->can('outsourceCRUD') && !Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id))
                Yii::$app->authManager->assign(Yii::$app->authManager->getRole('outsourceModerator'), $user->id);
            if (Yii::$app->user->can('clientCRUD') && !Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id))
                Yii::$app->authManager->assign(Yii::$app->authManager->getRole('clientModerator'), $user->id);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
