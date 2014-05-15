<?php
/**
 * LDPublishAssetsBehavior class file.
 *
 * @author Louis A. DaPrato <l.daprato@gmail.com>
 * @link https://lou-d.com
 * @copyright 2014 Louis A. DaPrato
 * @license The MIT License (MIT)
 * @since 1.0
 */

/**
 * This is a behavior to make things a little simpler for components that need to publish assets. 
 * The only required property is {@see LDPublishAssetsBehavior::$assetsDir}, the directory containing the component's assets.
 * Assets will only be published one time when the URL for the assets is needed.
 * The behavior will throw an exception if an asset manager is not configured or if the asset directory does not exist or is not readable.
 * 
 * @property string $tCategory The category to use for message translations.
 * @property string $assetManagerName The name of an asset manager component. Defaults to NULL meaning use the application's default {@see CWebApplication::getAssetManager()}.
 * @property boolean $isUsingSystemAssetManager Whether this behavior is using the system default asset manager or not (True if asset manager component name is null. False otherwise.).
 * @property CAssetManager $assetManager The asset manager instance used by this behavior.
 * @property string $assetsDir The directory containing the assets used by this behavior's owner.
 * @property string $assetsUrl The URL to the assets published for this behavior's owner.
 * 
 * @author Louis A. DaPrato <l.daprato@gmail.com>
 * @since 1.0
 */
class LDPublishAssetsBehavior extends CBehavior
{
	
	const ID = 'LDPublishAssetsBehavior';
	
	/**
	 * @var string The category to use for message translations.
	 */
	public $tCategory = self::ID;
	
	/**
	 * @var string The name of an asset manager component.
	 */
	private $_assetManagerName;
	
	/**
	 * @var string The URL to the assets published for this behavior's owner.
	 */
	private $_assetsUrl;
	
	/**
	 * @var string The directory containing the assets used by this behavior's owner.
	 */
	private $_assetsDir;
	
	/**
	 * Sets the name of the asset manager component.
	 * 
	 * @param string $assetManager The asset manager component name
	 */
	public function setAssetManagerName($assetManager)
	{
		if($this->_assetManagerName !== $assetManager)
		{
			$this->_assetsUrl = null;
			$this->_assetManagerName = $assetManager;
		}
	}
	
	/**
	 * Gets the name of the asset manager component
	 * 
	 * @return string
	 */
	public function getAssetManagerName()
	{
		return $this->_assetManagerName;
	}
	
	/**
	 * Whether this behavior is using the system default asset manager or not.
	 *  
	 * @return boolean True if asset manager component name is null. False otherwise.
	 */
	public function getIsUsingSystemAssetManager()
	{
		return $this->getAssetManagerName() === null;
	}
	
	/**
	 * Gets the asset manager instance used by this behavior.
	 * 
	 * @return CAssetManager 
	 */
	public function getAssetManager()
	{
		return $this->getIsUsingSystemAssetManager() ? Yii::app()->getAssetManager() : Yii::app()->getComponent($this->getAssetManagerName());
	}
	
	/**
	 * The directory containing the owner's assets. 
	 * This directory will be publish via this behavior's asset manager {@see LDPublishAssetsBehavior::getAssetManager()} when the asset URL is requested for the first time {@see LDPublishAssetsBehavior::getAssetsUrl()}.
	 * 
	 * @param string $assetsDir directory containing the owner's assets. Note that this parameter will be casted to a string.
	 */
	public function setAssetsDir($assetsDir)
	{
		if($this->_assetsDir !== $assetsDir)
		{
			$this->_assetsUrl = null;
			$this->_assetsDir = (string)$assetsDir;
		}
	}
	
	/**
	 * Gets the owner's asset directory.
	 * 
	 * @return string the owner's asset directory.
	 */
	public function getAssetsDir()
	{
		return $this->_assetsDir;
	}
	
	/**
	 * Gets the URL to the owner's published assets.
	 * Note assets will only be published the first time that this method is called.
	 * 
	 * @throws CException Throws an exception if no asset manager component is found or the assets directory is not a directory or is not a readable directory.
	 * @return string The URL to the published assets directory
	 */
	public function getAssetsUrl()
	{
		if($this->_assetsUrl === null && ($owner = $this->getOwner()) !== null)
		{
			if(is_dir($this->getAssetsDir()))
			{
				$assetManager = $this->getAssetManager();
				if($assetManager === null)
				{
					if($this->getIsUsingSystemAssetManager())
					{
						throw new CException(Yii::t(
								$this->tCategory,
								'System asset manager not be found for "{class_name}".',
								array('{class_name}' => get_class($owner)))
						);
					}
					else
					{
						throw new CException(Yii::t(
								$this->tCategory,
								'The asset manager named "{asset_manager}" could not be found for "{class_name}".',
								array('{class_name}' => get_class($owner), '{asset_manager}' => $this->getAssetManagerName()))
						);
					}
				}
				else
				{
					$this->_assetsUrl = $assetManager->publish($this->getAssetsDir());
				}
			}
			else
			{
				throw new CException(Yii::t(
						$this->tCategory,
						'Could not publish assets for "{class_name}". Make sure the directory "{dir_name}" exists and is readable.',
						array('{class_name}' => get_class($owner), '{dir_name}' => $this->getAssetsDir()))
				);
			}
		}
		return $this->_assetsUrl;
	}
	
}