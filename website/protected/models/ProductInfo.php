<?php

/**
 * This is the model class for table "product_info".
 *
 * The followings are the available columns in table 'product_info':
 * @property integer $product_id
 * @property string $name
 * @property integer $type
 * @property integer $fund_size
 * @property integer $publish_start_date
 * @property integer $publish_end_date
 * @property integer $build_date
 * @property string $funds_source
 * @property integer $income_distribution_cycle
 * @property string $note
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ProductAccountInformation $productAccountInformation
 * @property ProductPublishRate[] $productPublishRates
 * @property ProductReturnRate[] $productReturnRates
 */
class ProductInfo extends RootActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'product_info';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'productAccountInformation' => array(self::HAS_ONE, 'ProductAccountInformation', 'product_id'),
			'productPublishRates' => array(self::HAS_MANY, 'ProductPublishRate', 'product_id'),
			'productReturnRates' => array(self::HAS_MANY, 'ProductReturnRate', 'product_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'product_id' => 'Product',
			'name' => 'Name',
			'type' => 'Type',
			'fund_size' => '单位为万元',
			'publish_start_date' => 'Publish Start Date',
			'publish_end_date' => 'Publish End Date',
			'build_date' => 'Build Date',
			'funds_source' => 'Funds Source',
			'income_distribution_cycle' => 'Income Distribution Cycle',
			'note' => 'Note',
			'status' => 'Status',
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

		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('fund_size',$this->fund_size);
		$criteria->compare('publish_start_date',$this->publish_start_date);
		$criteria->compare('publish_end_date',$this->publish_end_date);
		$criteria->compare('build_date',$this->build_date);
		$criteria->compare('funds_source',$this->funds_source,true);
		$criteria->compare('income_distribution_cycle',$this->income_distribution_cycle);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProductInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
