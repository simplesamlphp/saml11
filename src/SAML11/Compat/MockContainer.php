<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Compat;

use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class \SimpleSAML\SAML11\Compat\MockContainer
 */
class MockContainer extends AbstractContainer
{
    /** @var \Psr\Clock\ClockInterface */
    private ClockInterface $clock;


    /**
     * Get a PSR-3 compatible logger.
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return new NullLogger();
    }


    /**
     * Set the system clock
     *
     * @param \Psr\Clock\ClockInterface $clock
     */
    public function setClock(ClockInterface $clock): void
    {
        $this->clock = $clock;
    }


    /**
     * Get the system clock
     *
     * @return \Psr\Clock\ClockInterface
     */
    public function getClock(): ClockInterface
    {
        return $this->clock;
    }
}
