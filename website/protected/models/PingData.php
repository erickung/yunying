<?php

/**
 * This is the model class for table "{{ping_data}}".
 *
 * The followings are the available columns in table '{{ping_data}}':
 * @property string $id
 * @property string $modify_time
 * @property string $data
 * @property string $source_ip
 * @property string $target_ip
 * @property string $loss
 * @property integer $status
 * @property integer $received
 * @property integer $resp_time
 * @property integer $transmitted
 */
class PingData extends CMSActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{ping_data}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('modify_time, data', 'required'),
			array('status, received, resp_time, transmitted', 'numerical', 'integerOnly'=>true),
			array('data', 'length', 'max'=>255),
			array('source_ip, target_ip', 'length', 'max'=>15),
			array('loss', 'length', 'max'=>4),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, modify_time, data, source_ip, target_ip, loss, status, received, resp_time, transmitted', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'modify_time' => 'Modify Time',
			'data' => 'Data',
			'source_ip' => 'Source Ip',
			'target_ip' => 'Target Ip',
			'loss' => 'Loss',
			'status' => 'Status',
			'received' => 'Received',
			'resp_time' => 'Resp Time',
			'transmitted' => 'Transmitted',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('source_ip',$this->source_ip,true);
		$criteria->compare('target_ip',$this->target_ip,true);
		$criteria->compare('loss',$this->loss,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('received',$this->received);
		$criteria->compare('resp_time',$this->resp_time);
		$criteria->compare('transmitted',$this->transmitted);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PingData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
