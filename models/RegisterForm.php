<?php

namespace app\models;

class RegisterForm extends \yii\base\Model
{
    public $username;
    public $password;
    public $password_repeat;
    public $confirm;
    public $email;

    public function rules()
    {
        return [
            [['username', 'password', 'password_repeat'], 'required'],
            ['email', 'uniqueEmail'],

            ['password', 'compare', 'compareAttribute' => 'password_repeat', 'message' => 'Hasła nie są idententyczne'],
            [['password', 'password_repeat'], 'string', 'length' => [5, 30], 'tooShort' => 'Hasło powinno mieć więcej niż 5 znaków', 'tooLong' => 'Hasło powinno mieć mniej niż 30 znaków'],

            ['confirm', 'boolean'],
            ['confirm', 'in', 'range' => [true], 'message' => 'Proszę zaakceptować regulamin'],
        ];
    }

    public function login()
    {
        if ($this->validate()) {
            return \Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function register()
    {
        $user = new User();
        $user->password = password_hash($this->password, PASSWORD_BCRYPT);
        $user->username = $this->username;
        $user->email = $this->email;
        if ($this->validate() && $user->save(false)) {
            return $user;
        }
        return null;
    }

    public function uniqueEmail($attribute, $email)
    {
        if (!$this->hasErrors() && !empty(User::findByEmail($this->$attribute))) {
            $this->addError($attribute, 'Taki email już istnieje');
        }
    }
}