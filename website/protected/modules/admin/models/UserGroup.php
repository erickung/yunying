<?php

/**
 * This is the model class for table "{{user_group}}".
 *
 * The followings are the available columns in table '{{user_group}}':
 * @property string $ugid
 * @property string $ugname
 * @property string $last_modify_time
 * @property string $last_modify_user
 */
class UserGroup extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_group}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ugname, last_modify_time, last_modify_user', 'required'),
			array('ugname', 'length', 'max'=>63),
			array('last_modify_user', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ugid, ugname, last_modify_time, last_modify_user', 'safe', 'on'=>'search'),
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
			'ugid' => 'Ugid',
			'ugname' => 'Ugname',
			'last_modify_time' => 'Last Modify Time',
			'last_modify_user' => 'Last Modify User',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('ugid',$this->ugid,true);
		$criteria->compare('ugname',$this->ugname,true);
		$criteria->compare('last_modify_time',$this->last_modify_time,true);
		$criteria->compare('last_modify_user',$this->last_modify_user,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserGroup the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
