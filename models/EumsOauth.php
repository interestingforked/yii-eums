<?php

/**
 * This is the model class for table "eums_oauth".
 *
 * The followings are the available columns in table 'eums_oauth':
 * @property integer $id
 * @property integer $user_id
 * @property string $provider
 * @property string $key
 * @property string $secret
 * @property integer $active
 * @property string $date_created
 *
 * Relationships
 * @property EumsUser $eums_user
 */
class EumsOauth extends CActiveRecord
{
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
		return '{{eums_oauth}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
      'eums_user'=>array(self::BELONGS_TO, 'EumsUser', 'user_id'),
		);
	}
}