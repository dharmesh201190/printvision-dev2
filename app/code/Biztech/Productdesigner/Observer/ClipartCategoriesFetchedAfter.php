<?php

namespace Biztech\Productdesigner\Observer;

class ClipartCategoriesFetchedAfter implements \Magento\Framework\Event\ObserverInterface {
	protected $_eventManager;
	protected $_scopeConfig;
	protected $_clipartFactory;
	protected $_finalArr;
	protected $allstore;

	public function __construct(
		\Magento\Framework\Event\Manager $manager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Biztech\Productdesigner\Model\Mysql4\Clipart\CollectionFactory $clipartFactory
	) {
		$this->_eventManager = $manager;
		$this->_scopeConfig = $scopeConfig;
		$this->_clipartFactory = $clipartFactory;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {		
		$this->_finalArr = [];
		$eventResponse = $observer->getData('clipartCollection');
		$this->allstore = $observer->getData('allstore');
		$clipartCategoriesCollection = $eventResponse->getClipartCategories();
		foreach ($clipartCategoriesCollection as $clipartCat) {
			$this->_finalArr[] = array(
				'id' => $clipartCat->getClipartId(),
				'name' => $clipartCat->getClipartTitle(),
				'level' => 0
			);                

			$this->getSubCategories($clipartCat->getClipartId());
		}
		$eventResponse->setClipartCategories($this->_finalArr);
	}

	public function getSubCategories($parentClipartId) {		
		$clipartCategoriesCollection = $this->_clipartFactory->create()
		->addFieldToFilter('parent_categories', array('eq' => $parentClipartId))
		->addFieldToFilter('status', array('eq' => 1))
		->addStoreFilter($this->allstore);        
		$clipartSubCatData = array();
		if ($clipartCategoriesCollection->count() > 0) {
			foreach ($clipartCategoriesCollection as $clipartCat) {
				$clipartSubCat = array(
					'id' => $clipartCat->getClipartId(),
					'name' => $clipartCat->getClipartTitle(),
					'level' => $clipartCat->getLevel(),
				);
				$clipartSubCatData[] = $clipartSubCat;

				$this->_finalArr[] = $clipartSubCat;

				$this->getSubCategories($clipartCat->getClipartId());
			}
		}
		return $clipartSubCatData;
	}
}