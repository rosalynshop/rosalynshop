<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Api;

/**
 * @api
 */
interface SitemapRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\XmlSitemap\Api\SitemapInterface $sitemap
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function save(\Amasty\XmlSitemap\Api\SitemapInterface $sitemap);

    /**
     * Get by id
     *
     * @param int $id
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\XmlSitemap\Api\SitemapInterface $sitemap
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\XmlSitemap\Api\SitemapInterface $sitemap);

    /**
     * Delete by id
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
