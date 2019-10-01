<?php

namespace App\Models;

use PDO;
use App\Config;
use \App\Token;
use \App\Mailer;
use \Core\View;
use \DateTime;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model {
  /**
   * Error messages
   * 
   * @var array
   */
  public $errors = [];
  /**
   * Class constructor
   * @param array $data Initial property values
   * 
   * @return void
   */
  public function __construct($data = []) {
    foreach ($data as $key => $value) {
      $this->$key = $value;
    };
  }
  /**
  * Save the user model with the current property values
  *
  * @return boolean True if user was saved, false if not
  */
  public function save() {
    $this->validate();
    if (empty($this->errors)) {
      $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

      $token = new Token();
      $hashed_token = $token->getHash();
      $this->activation_token = $token->getValue();
      // User credentials sql
      $sql = "INSERT INTO users (name,
                                last_name,
                                email,
                                password_hash,
                                activation_hash,
                                birth_date,
                                gender,
                                car";
      if (isset($this->carName)) {
        $sql .= ', car_name)';
      } else {
        $sql .= ')';
      }
      $sql .= "\nVALUES (:name,
                      :last_name,
                      :email,
                      :password_hash,
                      :activation_hash,
                      :birth_date,
                      :gender,
                      :car";
      if (isset($this->carName)) {
        $sql .= ', :car_name)';
      } else {
        $sql .= ')';
      }
      $db = static::getDB();
      $stmt = $db->prepare($sql);
      // echo $sql;
      $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
      $stmt->bindValue(':last_name', $this->lastName, PDO::PARAM_STR);
      $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
      $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
      $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

      $stmt->bindValue(':birth_date', $this->birthDate, PDO::PARAM_STR);
      $stmt->bindValue(':gender', $this->gender, PDO::PARAM_STR);
      $stmt->bindValue(':car', $this->car, PDO::PARAM_INT);
      if (isset($this->carName)) {
        $stmt->bindValue(':car_name', $this->carName, PDO::PARAM_STR);
      }

      return $stmt->execute();
    }
    return false;
  }
  /**
  * Validate current property values, adding validation error messages
  * to the errors array property
  *
  * @return void
  */
  public function validate() {
    // email
    if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
      $this->errors[] = 'Недійсна електронна адреса';
    }

    if ($this->emailExists($this->email, $this->id ?? null)) {
      $this->errors[] = 'E-mail вже зайнято';
      // if email is already taken,
      // even if he agrees to the terms,
      // he will not be able to register with existing email
    }
    // new email
    // check if user accepted the agreement
    if (empty($this->terms) && !$this->id) {
      $this->errors[] = 'Поставте прапорець, щоб прийняти умови.';
    }
    // Password
    if (isset($this->password)) {
      if (strlen($this->password) < 6) {
        $this->errors[] = 'Пароль повинен бути не менше 6 символів.';
      }
  
      if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
        $this->errors[] = 'Для пароля потрібен хоча б одина буква.';
      }
  
      if (preg_match('/.*\d+.*/i', $this->password) == 0) {
        $this->errors[] = 'Для пароля потрібно хоча б одна цифра.';
      }
    }
    // Name
    if ($this->name == '') {
      $this->errors[] = 'Ім\'я обов\'язково.';
    }
    // Last Name
    if ($this->lastName == '') {
      $this->errors[] = 'Прізвище обов\'язкове.';
    }
    // Birth date
    $date = DateTime::createFromFormat("Y-m-d", $this->birthDate);
    $validDate = $date !== false && !array_sum($date::getLastErrors());
    if (!$validDate) {
      $this->errors[] = 'Перевір свій день народження.';
    }
    // Gender
    if ($this->gender == '') {
      $this->errors[] = 'Перевірте свою стать.';
    }
    // Car exists
    if ($this->car == '1' && isset($this->carName) && $this->carName == '') {
      $this->errors[] = 'Ви забули марку свого автомобіля?';
    }
  }
  /**
  * See if email exists (it supposed to be unique)
  *
  * @param string $email email address to search for
  * @return boolean True if a record already exists with
  * the specified email, false otherwise
  */
  public static function emailExists($email, $ignore_id = null) {
    $user = static::findByEmail($email);
    if ($user) {
      if ($user->id != $ignore_id) {
        return true;
      }
    }
    return false;
  }
  /**
  * Find a user model by email address
  *
  * @param string $email email address to search for
  * @return mixed User object if found, false otherwise
  *
  */
  public static function findByEmail($email) {
    $sql = 'SELECT * FROM users WHERE email = :email';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

    $stmt->execute();
    return $stmt->fetch();
  }
  /**
   * Find a user model by ID
   * 
   * @param string $id The user ID
   * 
   * @return mixed User object if found, false otherwise
   */
  public static function findByID($id) {
    $sql = 'SELECT * FROM users WHERE id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
    $stmt->execute();
    return $stmt->fetch();
  }
  /**
   * Remember the login by inserting a new unique token
   * into the remembered_logins table
   * for this user record
   * 
   * @return boolean  True if login was remembered successfully,
   * false otherwise
   */
  public function rememberLogin() {
    $token = new Token();
    $hashed_token = $token->getHash();
    $this->remember_token = $token->getValue();

    $this->expiry_timestamp = time() + 60 * 60 * 24 * 30; // 30 days from now

    $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
            VALUES (:token_hash, :user_id, :expires_at)';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $this->id, PDO::PARAM_STR);
    $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

    return $stmt->execute();
  }
  /**
  * Authenticate a user by email and password
  *
  * @param string $email email address
  * @return string $password password
  *
  * @return mixed The user object or false if authentication fails
  */
  public static function authenticate($email, $password) {
    $user = static::findByEmail($email);
    if ($user && $user->is_active) {
      if (password_verify($password, $user->password_hash)) {
        return $user;
      } else {
        $user->errors[] = 'Неправильний пароль.';
        return $user;
      }
    } else if ($user && !$user->is_active) {
      $user->errors[] = 'Не активований профіль.';
      return $user;
    }
    return false;
  }
  /**
   * Send password reset instructions to the user specified
   * 
   * @param string $email The email address
   * 
   * @return void
   */
  public static function sendPasswordReset($email) {
    $user = static::findByEmail($email);
    if ($user) {
      if ($user->startPasswordReset()) {
        $user->sendPasswordResetEmail();
      }
    }
  }
  /**
   * Start the password reset process by generating a new token and expiry
   * 
   * @return void
   */
  protected function startPasswordReset() {
    $token = new Token();
    $hashed_token = $token->getHash();
    $this->password_reset_token = $token->getValue();

    $expiry_timestamp = time() + 60 * 60 * 2; // two hours from now

    $sql = 'UPDATE users
            SET password_reset_hash = :token_hash,
                password_reset_expires_at = :expires_at
            WHERE id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
    $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
    $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);

    return $stmt->execute();
  }
  /**
   * Send password reset instructions in an email to the user
   * 
   * @return void
   */
  protected function sendPasswordResetEmail() {
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;

    $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
    $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);
    Mailer::send($this->email, 'Password reset', $text, $html);
  }
  /**
   * Find a user model by password reset token and expiry
   * 
   * @param string $token Password reset token sent to user
   * 
   * @return mixed User object if found and the token hasn't expired, null otherwise
   */
  public static function findByPasswordReset($token) {
    $token = new Token($token);
    $hashed_token = $token->getHash();
    $sql = 'SELECT * FROM users
            WHERE password_reset_hash = :token_hash';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
    $stmt->execute();

    $user = $stmt->fetch();
    if ($user) {
      // check password reset token hasn't expired
      if(strtotime($user->password_reset_expires_at) > time()) {
        return $user;
      }
    }
  }
  /**
   * Reset the password
   * 
   * @param string $password The new password
   * 
   * @return boolean True if the password was updated successfully, false otherwise
   */
  public function resetPassword($password) {
    // assign the value of the argument to the password property of the user
    $this->password = $password;
    $this->validate();
    if (empty($this->errors)) {
      $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

      $sql = 'UPDATE users
              SET password_hash = :password_hash,
                  password_reset_hash = NULL,
                  password_reset_expires_at = NULL
              WHERE id = :id';
      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

      return $stmt->execute();
    }
    return false;
  }

  /**
  * Get recatcha score
  * Move this somewhere
  *
  * @return void
  */
  public static function getRecaptchaScore($captcha) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => Config::RECAPTCHA_SECRET_KEY, 'response' => $captcha);
    $options = array(
      'http' => array(
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'method'  => 'POST',
        'content' => http_build_query($data)
      )
    );

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $responseKeys = json_decode($response, true);
    if (isset($responseKeys['score'])) {
      return $responseKeys['score'];
    } else {
      return false;
    }
  }
  /**
   * Send password reset instructions in an email to the user
   * 
   * @return void
   */
  // it is public cause we need to access it outside the class
  public function sendActivationEmail() {
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;
    echo 'sending email';
    $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
    $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);
    Mailer::send($this->email, 'Account activation', $text, $html);
  }
  /**
   * Activate the user account with the specified activation token
   * 
   * @param string $value Activation token from the URL
   * 
   * @return void
   */
  public static function activate($value) {
    $token = new Token($value);
    $hashed_token = $token->getHash();

    // sql to find the user token based on the token hash
    $sql = 'UPDATE users
            SET is_active = 1,
                activation_hash = null
            WHERE activation_hash = :hashed_token';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);
    $stmt->execute();
  }
  /**
   * Update the users profile
   * 
   * @param array $data Data from the edit profile form
   * 
   * @return boolean True if the data was updated, false otherwise
   */
  public function updateProfile($data) {
    // $data is $_POST from update action in profile controller
    // What's this?
    $this->name = $data['name'];
    $this->lastName = $data['lastName'];
    if (isset($data['email'])) {
      $this->email = $data['email'];
    }
    $this->birthDate = $data['birthDate'];
    $this->gender = $data['gender'];
    $this->car = $data['car'];
    if (isset($data['carName'])) {
      $this->carName = $data['carName'];
    } else {
      $this->carName = null;
    }

    // Only validate and update the password if a value is provided
    if (isset($data['password']) && $data['password'] != '') {
      $this->password = $data['password'];
    }
    // Validate all data
    $this->validate();

    if (empty($this->errors)) {
      // sql for user credentials
      $sql = "UPDATE users
              SET name = :name,
                  last_name = :last_name,
                  email = :email,
                  birth_date = :birth_date,
                  gender = :gender,
                  car = :car,
                  car_name = :car_name";
      // Add password only if it's set
      if (isset($this->password)) {
        $sql .= ", password_hash = :password_hash";
      }
      // move the 'where' clause here so that all the set clauses are together
      $sql .= "\nWHERE id = :id";

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      // current user id
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      // user data
      $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
      $stmt->bindValue(':last_name', $this->lastName, PDO::PARAM_STR);
      $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
      $stmt->bindValue(':birth_date', $this->birthDate, PDO::PARAM_STR);
      $stmt->bindValue(':gender', $this->gender, PDO::PARAM_STR);
      $stmt->bindValue(':car', $this->car, PDO::PARAM_INT);
      if (!empty($this->carName)) {
        $stmt->bindValue(':car_name', $this->carName, PDO::PARAM_STR);
      } else {
        $stmt->bindValue(':car_name', $this->carName, PDO::PARAM_NULL);
      }
      // Add password if it's set
      if (isset($this->password)) {
        $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
      }

      return $stmt->execute();;
    }
    return false;
  }
  /**
   * Delete the users profile
   * 
   * @param integer $id User id whose profile is about to be deleted
   * 
   * @return boolean True if profile was deleted, false otherwise
   */
  public function deleteProfile($id) {
    $sql = 'DELETE FROM users
            WHERE id = :id';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
  }
}
