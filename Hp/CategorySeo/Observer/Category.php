<?php

namespace Hp\CategorySeo\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

/**
 * Category Seo Pagination class
 *
 * @author Hardik Patel - hpca1644@gmail.com
 */
class Category implements \Magento\Framework\Event\ObserverInterface {

    protected $_objectManager;
    protected $_orderFactory;
    protected $_categoryFactory;
    protected $_category;
    protected $_productCollectionFactory;
    protected $pageConfig;
    protected $urlBuilder;

    public function __construct(
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Framework\Registry $registry, \Magento\Framework\App\Request\Http $request, \Magento\Framework\View\Page\Config $pageConfig, \Magento\Framework\UrlInterface $urlBuilder
    ) {

        $this->request = $request;
        $this->_registry = $registry;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->_categoryFactory = $categoryFactory;
        $this->pageConfig = $pageConfig;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * This is the method that fires when the event runs.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        if ('catalog_category_view' == $observer->getEvent()->getFullActionName()) {

            $productListBlock = $observer->getEvent()->getLayout()->getBlock('category.products.list');
            $category = $productListBlock->getLayer()->getCurrentCategory();

            $toolbarBlock = $productListBlock->getToolbarBlock();
            /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
            $pagerBlock = $toolbarBlock->getChildBlock('product_list_toolbar_pager');
            $pagerBlock->setAvailableLimit($toolbarBlock->getAvailableLimit())
                    ->setCollection($productListBlock->getLayer()->getProductCollection());


            /**
             * Add rel canonical with page var
             */
            $this->pageConfig->addRemotePageAsset(
                    $this->getPageUrl([
                        $pagerBlock->getPageVarName() => $pagerBlock->getCurrentPage()
                    ]), 'canonical', ['attributes' => ['rel' => 'canonical']]
            );

            /**
             * Add rel prev and rel next
             */
            if (1 < $pagerBlock->getCurrentPage()) {
                $this->pageConfig->addRemotePageAsset(
                        $this->getPageUrl([
                            $pagerBlock->getPageVarName() => $pagerBlock->getCollection()->getCurPage(-1)
                        ]), 'link_rel', ['attributes' => ['rel' => 'prev']]
                );
            }
            if ($pagerBlock->getCurrentPage() < $pagerBlock->getLastPageNum()) {
                $this->pageConfig->addRemotePageAsset(
                        $this->getPageUrl([
                            $pagerBlock->getPageVarName() => $pagerBlock->getCollection()->getCurPage(+1)
                        ]), 'link_rel', ['attributes' => ['rel' => 'next']]
                );
            }
        }
    }

    /**
     * Retrieve page URL by defined parameters
     *
     * @param array $params
     * @return string
     */
    protected function getPageUrl($params = []) {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;

        return $this->urlBuilder->getUrl('*/*/*', $urlParams);
    }

}
