<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


declare(strict_types=1);

namespace Amasty\Fpc\Api\Data;

interface FlushesLogInterface
{
    const LOG_ID = 'log_id';
    const SOURCE = 'source';
    const DETAILS = 'details';
    const TAGS = 'tags';
    const SUBJECT = 'subject';
    const DATE = 'date';
    const BACKTRACE = 'backtrace';

    public function getLogId(): int;

    public function setLogId(int $id): self;

    public function getSource(): string;

    public function setSource(string $source): self;

    public function getDetails(): string;

    public function setDetails(string $details): self;

    public function getTags(): string;

    public function setTags(string $tags): self;

    public function getSubject(): string;

    public function setSubject(string $subject): self;

    public function getDate(): string;

    public function setDate(string $date): self;

    public function getBacktrace(): string;

    public function setBacktrace(string $backtrace): self;
}
