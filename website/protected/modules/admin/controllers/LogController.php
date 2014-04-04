<?php
class LogController extends CMSController
{
	public function actionList()
	{
		$OperateLogAR = new OperateLogAR();
		$condition = $OperateLogAR->getCondition(array('opt_username'=>'like'), 
					array('module_id'=>'module.module_id', 'time_begin'=>array('create_time', '>='), 'time_end'=>array('create_time', '<=')));

		$datas = OperateLogAR :: model() ->with('module') -> findAll($condition);
		$data = CMS :: convetARToList($datas, array_keys(OperateLogAR :: model() -> attributeLabels()));
		foreach ($data as $i => $d)
		{
			$data[$i]['module_name'] = $datas[$i]->module->module_name;
		}
		Response::success($data);
	}
	
	public function actionInfo()
	{
		$id = $_GET['log_id'];
		$data = OperateLogAR::model()->findAllByPk($id);
		$data = CMS::convetARToArray($data);
		Response::success($data);
	}
}