<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\Enumeration;

enum DecisionTypeEnum: string
{
    case Deny = 'Deny';
    case Indeterminate = 'Indeterminate';
    case Permit = 'Permit';
}
