<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

enum DecisionTypeEnum
{
    case Deny;
    case Indeterminate;
    case Permit;
}
