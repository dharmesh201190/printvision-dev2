<?php

namespace LR\ArtworkDesign\Block;

/**
 * ArtworkDesign content block
 */
class ArtworkDesign extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_request = $request;
        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('LR ArtworkDesign Module'));
        
        return parent::_prepareLayout();
    }

    public function isHomepage()
    {
        return $this->_request->getFullActionName();
    }

    public function getControllerModule()
    {
        return $this->_request->getControllerModule();
    }
    
    public function getFullActionName()
    {
        return $this->_request->getFullActionName();
    }
    
    public function getRouteName()
    {
        return $this->_request->getRouteName();
    }
    
    public function getActionName()
    {
        return $this->_request->getActionName();
    }
    
    public function getControllerName()
    {
        return $this->_request->getControllerName();
    }
    
    public function getModuleName()
    {
        return $this->_request->getModuleName();
    }
}
