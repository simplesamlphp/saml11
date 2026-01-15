<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11;

/**
 * Various SAML 1.1 constants.
 *
 * @package simplesamlphp/saml11
 */
class Constants extends \SimpleSAML\XMLSecurity\Constants
{
    /**
     * Password authentication context.
     */
    public const string AC_PASSWORD = 'urn:oasis:names:tc:SAML:1.0:am:password';

    /**
     * Kerberos authentication context.
     */
    public const string AC_KERBEROS = 'urn:ietf:rfc:1510';

    /**
     * Secure Remote Password authentication context.
     */
    public const string AC_SECURE_REMOTE_PASSWORD = 'urn:ietf:rfc:2945';

    /**
     * Hardware token authentication context.
     */
    public const string AC_HARDWARE_TOKEN = 'urn:oasis:names:tc:SAML:1.0:am:HardwareToken';

    /**
     * Certificate based client authentication authentication context.
     */
    public const string AC_CERT_BASE_CLIENT_AUTHN = 'urn:ietf:rfc:2246';

    /**
     * X.509 Public key authentication context.
     */
    public const string AC_X509_PUBLIC_KEY = 'urn:oasis:names:tc:SAML:1.0:am:X509-PKI';

    /**
     * PGP Public key authentication context.
     */
    public const string AC_PGP_PUBLIC_KEY = 'urn:oasis:names:tc:SAML:1.0:am:PGP';

    /**
     * SPKI Public key authentication context.
     */
    public const string AC_SPKI_PUBLIC_KEY = 'urn:oasis:names:tc:SAML:1.0:am:SPKI';

    /**
     * XKMS Public key authentication context.
     */
    public const string AC_XMLS_PUBLIC_KEY = 'urn:oasis:names:tc:SAML:1.0:am:XKMS';

    /**
     * XML Digital Signature authentication context.
     */
    public const string AC_XML_DSIG = 'urn:ietf:rfc:3075';

    /**
     * Unspecified authentication context.
     */
    public const string AC_UNSPECIFIED = 'urn:oasis:names:tc:SAML:1.0:am:unspecified';

    /**
     * Artifact subject confirmation method.
     */
    public const string CM_ARTIFACT = 'urn:oasis:names:tc:SAML:1.0:cm:artifact';

    /**
     * Bearer subject confirmation method.
     */
    public const string CM_BEARER = 'urn:oasis:names:tc:SAML:1.0:cm:bearer';

    /**
     * Email address NameID format.
     */
    public const string NAMEID_EMAIL_ADDRESS = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';

    /**
     * Unspecified NameID format.
     */
    public const string NAMEID_UNSPECIFIED = 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified';

    /**
     * Windows Domain Qualifier Name NameID format.
     */
    public const string NAMEID_WINDOWS_DOMAIN_QUALIFIED_NAME =
        'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName';

    /**
     * X509 Subject Name NameID format.
     */
    public const string NAMEID_X509_SUBJECT_NAME = 'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName';

    /**
     * The namespace for the SAML 1.1 assertions.
     */
    public const string NS_SAML = 'urn:oasis:names:tc:SAML:1.0:assertion';

    /**
     * The namespace for the SAML 1.1 protocol.
     */
    public const string NS_SAMLP = 'urn:oasis:names:tc:SAML:1.0:protocol';

    /**
     * The SAML responder or SAML authority is able to process the request but has chosen not to respond.
     * This status code MAY be used when there is concern about the security context of the request message or
     * the sequence of request messages received from a particular requester.
     *
     * Second-level status code.
     */
    public const string STATUS_REQUEST_DENIED = 'samlp:RequestDenied';

    /**
     * The SAML responder cannot process any requests with the protocol version specified in the request.
     *
     * Second-level status code.
     */
    public const string STATUS_REQUEST_VERSION_DEPRECATED = 'samlp:RequestVersionDeprecated';

    /**
     * The SAML responder cannot process the request because the protocol version specified in the request message
     * is a major upgrade from the highest protocol version supported by the responder.
     *
     * Second-level status code.
     */
    public const string STATUS_REQUEST_VERSION_TOO_HIGH = 'samlp:RequestVersionTooHigh';

    /**
     * The SAML responder cannot process the request because the protocol version specified in the request message
     * is too low.
     *
     * Second-level status code.
     */
    public const string STATUS_REQUEST_VERSION_TOO_LOW = 'samlp:RequestVersionTooLow';

    /**
     * The request could not be performed due to an error on the part of the requester.
     *
     * Top-level status code.
     */
    public const string STATUS_REQUESTER = 'samlp:Requester';

    /**
     * The resource value provided in the request message is invalid or unrecognized.
     *
     * Second-level status code.
     */
    public const string STATUS_RESOURCE_NOT_RECOGNIZED = 'samlp:ResourceNotRecognized';

    /**
     * The request could not be performed due to an error on the part of the SAML responder or SAML authority.
     *
     * Top-level status code.
     */
    public const string STATUS_RESPONDER = 'samlp:Responder';

    /**
     * Top-level status code indicating successful processing of the request.
     * The request succeeded. Additional information MAY be returned in the <StatusMessage>
     * and/or <StatusDetail> elements.
     *
     * Top-level status code.
     */
    public const string STATUS_SUCCESS = 'samlp:Success';

    /**
     * The response message would contain more elements than the SAML responder is able to return.
     *
     * Second-level status code.
     */
    public const string STATUS_TOO_MANY_RESPONSES = 'samlp:TooManyResponses';

    /**
     * The SAML responder could not process the request because the version of the request message was incorrect.
     *
     * Top-level status code.
     */
    public const string STATUS_VERSION_MISMATCH = 'samlp:VersionMismatch';


    /** @var string[] */
    public static array $STATUS_CODES = [
        self::STATUS_REQUEST_DENIED,
        self::STATUS_REQUEST_VERSION_DEPRECATED,
        self::STATUS_REQUEST_VERSION_TOO_HIGH,
        self::STATUS_REQUEST_VERSION_TOO_LOW,
        self::STATUS_REQUESTER,
        self::STATUS_RESOURCE_NOT_RECOGNIZED,
        self::STATUS_RESPONDER,
        self::STATUS_SUCCESS,
        self::STATUS_TOO_MANY_RESPONSES,
        self::STATUS_VERSION_MISMATCH,
    ];
}
