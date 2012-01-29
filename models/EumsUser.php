<?php

/**
 * This is the model class for table "eums_user".
 *
 * The followings are the available columns in table 'eums_user':
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $salt
 * @property string $activation
 * @property integer $active
 * @property string $home
 * @property string $date_created
 * @property string $last_visited
 */
class EumsUser extends CActiveRecord
{

  public $password_confirm;
  public $captcha;

  protected $password_shadow;

  protected function afterFind() {
    parent::afterFind();
    $this->password_shadow = $this->password;
  }

  /**
   * Authenticate with Password
   *
   * @param $pasword
   * @boolean
   */
  public function authenticate($password) {
    return $this->active && (md5($password.$this->salt) == $this->password);
  }

  protected function beforeValidate() {
    if (parent::beforeValidate()) {
      if (
        $this->getScenario() != "resetpassword" &&
        $this->getScenario() != "registration" &&
        $this->getScenario() != "login" &&
        $this->password != $this->password_shadow)
      {
        $this->addError("password", "Password Changes");
        return false;
      }
      return true;
    } else return false;
  }

  protected function beforeSave() {
    if (parent::beforeSave()) {
      if ($this->getScenario() == "login") return false;
      if ($this->getIsNewRecord() && empty($this->salt)) {
        $this->salt = md5(time());
      }
      if ($this->getScenario() == "resetpassword" || $this->getScenario() == "registration") {
        $this->password = md5($this->password.$this->salt);
      }
      if ($this->getScenario() == "registration") {
        $this->active = false;
        $this->activation = md5($this->password.'registration');
      }
      if ($this->getIsNewRecord()) {
        $this->date_created = date('Y-m-d H:i:s');
      }
      return true;
    } else return false;
  }

  /**
	 * Returns the static model of the specified AR class.
	 * @return EumsUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'eums_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, first_name, last_name, email, password, password_confirm', 'required', 'on'=>'registration'),
      array('captcha', 'captcha', 'on'=>'registration', 'allowEmpty' => YII_DEBUG),
      array('password, password_confirm', 'required', 'on'=>'resetpassword'),
      array('password', 'compare', 'compareAttribute'=>'password_confirm', 'on'=>'resetpassword, registration'),
      array('email, username', 'unique', 'on'=>'resetpassword, registration, update, insert'),
      array('username, password', 'required', 'on'=>'login'),
      array('username, activation', 'safe', 'on'=>'accountactivation'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'username' => 'Username',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'email' => 'Email',
			'password' => 'Password',
			'salt' => 'Salt',
			'activation' => 'Activation',
			'active' => 'Active',
			'home' => 'Home',
			'date_created' => 'Date Created',
			'last_visited' => 'Last Visited',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('username',$this->username,true);

		$criteria->compare('first_name',$this->first_name,true);

		$criteria->compare('last_name',$this->last_name,true);

		$criteria->compare('email',$this->email,true);

		$criteria->compare('password',$this->password,true);

		$criteria->compare('salt',$this->salt,true);

		$criteria->compare('activation',$this->activation,true);

		$criteria->compare('active',$this->active);

		$criteria->compare('home',$this->home,true);

		$criteria->compare('date_created',$this->date_created,true);

		$criteria->compare('last_visited',$this->last_visited,true);

		return new CActiveDataProvider('EumsUser', array(
			'criteria'=>$criteria,
		));
	}
}