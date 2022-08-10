<?php

namespace common\models;

use Yii;
use yii\base\Model;

class UpdateForm extends Model
{
    public $username;
    public $password;

    private $_user;


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

        ['password', 'required'],
        ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
    ];
}


    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }
    public function fillForm ($user) {
        $this->username = $user->username;
    }
    public function update($id) {
        if (!$this->validate()) {
            return null;
        }

        $user = User::findIdentityWOStatus($id);
        $user->username = $this->username;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        return $user->save();
    }
}