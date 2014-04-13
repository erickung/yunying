<?php
class ProductApproval extends ProductInfoAR
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	protected function passProduct($info)
	{
		$ar = ProductInfoAR::model()->findByPk($info['product_id']);
		if (!$ar->checkPassPower()) return false;

		$product_roles = $ar->getProductProcessRoles();
		$product_roles = array_keys($product_roles);
		$origin_status = $ar->status;
		if (end($product_roles) == $ar->status)
		{
			$ar->status = self::STATUS_PASS;
		}
		else
		{
			$i = 0;
			$status = 1;
			foreach ($product_roles as $pr_id)
			{
				$status = $pr_id;
				if ($i == 1) break;
				if ($pr_id == $ar->status) $i = 1;
			}
			$ar->status = $status;
		}
		$ar->modifyByPk();
	
		$ProductApprovalItemAR = new ProductApprovalItemAR();
		$ProductApprovalItemAR->after_status = $status;
		$ProductApprovalItemAR->origin_status = $origin_status;
		$ProductApprovalItemAR->note = $info['note'];
		$ProductApprovalItemAR->product_id = $info['product_id'];
		$ProductApprovalItemAR->type = 1;

		$ProductApprovalItemAR->save();
	}
	
	protected function rejectProduct($info)
	{
		$ar = ProductInfoAR::model()->findByPk($info['product_id']);
		if (!$ar->checkPassPower()) return false;
	
		$product_roles = $ar->getProductProcessRoles();
		$status = self::STATUS_DRAFT;	
		$origin_status = $ar->status;
		foreach ($product_roles as $pr_id => $name)
		{
			if ($pr_id == $ar->status) break;
			$status = $pr_id;
		}
		$ar->status = $status;
		$ar->modifyByPk();
		
		$ProductApprovalItemAR = new ProductApprovalItemAR();
		$ProductApprovalItemAR->after_status = $status;
		$ProductApprovalItemAR->origin_status = $origin_status;
		$ProductApprovalItemAR->note = $info['note'];
		$ProductApprovalItemAR->product_id = $info['product_id'];
		$ProductApprovalItemAR->type = 0;
		
		$ProductApprovalItemAR->save();
	}
}