<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

enum DecisionTypeEnum: string
{
    case Deny = 'Deny';
    case Indeterminate = 'Indeterminate';
    case Permit = 'Permit';
}
