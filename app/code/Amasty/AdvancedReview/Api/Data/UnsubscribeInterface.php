<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Api\Data;

interface UnsubscribeInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';

    const UNSUBSCRIBED_AT = 'unsubscribed_at';

    const EMAIL = 'email';

    const IS_COMMENT = 'isComment';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getUnsubscribedAt();

    /**
     * @param string $unsubscribedAt
     *
     * @return \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface
     */
    public function setUnsubscribedAt($unsubscribedAt);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface
     */
    public function setEmail($email);

    /**
     * @return boolean
     */
    public function getIsComment();

    /**
     * @param boolean $isComment
     *
     * @return \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface
     */
    public function setIsComment($isComment);
}
