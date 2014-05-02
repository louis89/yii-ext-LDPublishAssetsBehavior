<?php

/**
 * 
 * @author Louis DaPrato <l.daprato@gmail.com>
 *
 */
class LDPublishAssetsBehavior extends CBehavior
{
	
	private $_assetsUrl;
	
	private $_assetsDir;
	
	public function setAssetsDir($assetsDir)
	{
		$this->_assetsUrl = null;
		$this->_assetsDir = $assetsDir;
	}
	
	public function getAssetsDir()
	{
		return $this->_assetsDir;
	}
	
	public function getAssetsUrl()
	{
		if($this->_assetsUrl === null && ($owner = $this->getOwner()) !== null)
		{
			if(is_dir($this->getAssetsDir()))
			{
				$this->_assetsUrl = Yii::app()->getAssetManager()->publish($this->getAssetsDir());
			}
			else
			{
				throw new CException(Yii::t(
						__CLASS__,
						'{class_name} - Error: Couldn\'t find assets to publish. Please make sure the directory "{dir_name}" exists and is readable.',
						array('{class_name}' => get_class($owner), '{dir_name}' => $this->getAssetsDir()))
				);
			}
		}
		return $this->_assetsUrl;
	}
	
}