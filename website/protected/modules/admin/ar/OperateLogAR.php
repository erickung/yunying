<?php
class OperateLogAR extends OperateLog
{
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'log_id' => '',
				'user_id' => '用户ID',
				'time' => '日志时间',
				'controller' => '操作行为控制器',
				'action' => '操作行为action',
				'operation_record' => '日志明细',
				'note' => '注释',
		);
	}
	
	public function editNote()
	{
		if (!$this->log_id) return false;
		
		return $this->modify();
	}
}