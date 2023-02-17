<?php

namespace App\Auth;

use Neoan\Routing\Attributes\Get;
use Neoan\Routing\Interfaces\Routable;

#[Get('/auth/generate-certificate')]
class CertificateCreate implements Routable
{
    public function __invoke(): array
    {
        // new private key
        $privateKey = openssl_pkey_new([
//            "config" => "C:/xampp/apache/bin/openssl.cnf",
            "digest_alg" => "sha512",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA
        ]);

        //export private key

        openssl_pkey_export($privateKey, $keyFile);
        file_put_contents(__DIR__ . '/privKey.pem', $keyFile);


        $dn = [
            'countryName' => 'US',
            'emailAddress' => 'verify@microbillr.online',
            'organizationName' => 'MICRO Billr'
        ];

        // sign request
        $csr = openssl_csr_new($dn, $privateKey);
        // sign certificate with privateKey
        $signedCert = openssl_csr_sign($csr, null, $privateKey, $days=365);

        // export certificate
        openssl_x509_export($signedCert, $cert);
        file_put_contents(__DIR__ . '/cert.pem', $cert);

        // export public key
        $public_key_pem = openssl_pkey_get_details($privateKey)['key'];
        file_put_contents(__DIR__ . '/public.pem', $public_key_pem);

        return ['publicKey' => $public_key_pem];
    }
}