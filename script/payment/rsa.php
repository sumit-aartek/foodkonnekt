<?php

//include('Math_BigInteger.php');

class RSA
{
   
    const ENCRYPTION_OAEP = 1;
   
    const ENCRYPTION_PKCS1 = 2;
       const ENCRYPTION_NONE = 3;
     const SIGNATURE_PSS = 1;
    /**
     * Use the PKCS#1 scheme by default.
     *
     * Although self::SIGNATURE_PSS offers more security, including PKCS#1 signing is necessary for purposes of backwards
     * compatibility with protocols (like SSH-2) written before PSS's introduction.
    */
    const SIGNATURE_PKCS1 = 2;
    /**#@-*/
    /**#@+
     * @access private
     * @see \phpseclib\Crypt\RSA::createKey()
    */
    /**
     * ASN1 Integer
    */
    const ASN1_INTEGER = 2;
    /**
     * ASN1 Bit String
    */
    const ASN1_BITSTRING = 3;
    /**
     * ASN1 Octet String
    */
    const ASN1_OCTETSTRING = 4;
    /**
     * ASN1 Object Identifier
    */
    const ASN1_OBJECT = 6;
    /**
     * ASN1 Sequence (with the constucted bit set)
    */
    const ASN1_SEQUENCE = 48;
    /**#@-*/
    /**#@+
     * @access private
     * @see \phpseclib\Crypt\RSA::__construct()
    */
    /**
     * To use the pure-PHP implementation
    */
    const MODE_INTERNAL = 1;
    /**
     * To use the OpenSSL library
     *
     * (if enabled; otherwise, the internal implementation will be used)
    */
    const MODE_OPENSSL = 2;
    /**#@-*/
    /**#@+
     * @access public
     * @see \phpseclib\Crypt\RSA::createKey()
     * @see \phpseclib\Crypt\RSA::setPrivateKeyFormat()
    */
    /**
     * PKCS#1 formatted private key
     *
     * Used by OpenSSH
    */
    const PRIVATE_FORMAT_PKCS1 = 0;
    /**
     * PuTTY formatted private key
    */
    const PRIVATE_FORMAT_PUTTY = 1;
    /**
     * XML formatted private key
    */
    const PRIVATE_FORMAT_XML = 2;
    /**
     * PKCS#8 formatted private key
    */
    const PRIVATE_FORMAT_PKCS8 = 3;
    /**#@-*/
    /**#@+
     * @access public
     * @see \phpseclib\Crypt\RSA::createKey()
     * @see \phpseclib\Crypt\RSA::setPublicKeyFormat()
    */
    /**
     * Raw public key
     *
     * An array containing two \phpseclib\Math\BigInteger objects.
     *
     * The exponent can be indexed with any of the following:
     *
     * 0, e, exponent, publicExponent
     *
     * The modulus can be indexed with any of the following:
     *
     * 1, n, modulo, modulus
    */
    const PUBLIC_FORMAT_RAW = 3;
    /**
     * PKCS#1 formatted public key (raw)
     *
     * Used by File/X509.php
     *
     * Has the following header:
     *
     * -----BEGIN RSA PUBLIC KEY-----
     *
     * Analogous to ssh-keygen's pem format (as specified by -m)
    */
    const PUBLIC_FORMAT_PKCS1 = 4;
    const PUBLIC_FORMAT_PKCS1_RAW = 4;
    /**
     * XML formatted public key
    */
    const PUBLIC_FORMAT_XML = 5;
    /**
     * OpenSSH formatted public key
     *
     * Place in $HOME/.ssh/authorized_keys
    */
    const PUBLIC_FORMAT_OPENSSH = 6;
    /**
     * PKCS#1 formatted public key (encapsulated)
     *
     * Used by PHP's openssl_public_encrypt() and openssl's rsautl (when -pubin is set)
     *
     * Has the following header:
     *
     * -----BEGIN PUBLIC KEY-----
     *
     * Analogous to ssh-keygen's pkcs8 format (as specified by -m). Although PKCS8
     * is specific to private keys it's basically creating a DER-encoded wrapper
     * for keys. This just extends that same concept to public keys (much like ssh-keygen)
    */
    const PUBLIC_FORMAT_PKCS8 = 7;
    /**#@-*/
    /**
     * Precomputed Zero
     *
     * @var Array
     * @access private
     */
    var $zero;
    /**
     * Precomputed One
     *
     * @var Array
     * @access private
     */
    var $one;
    /**
     * Private Key Format
     *
     * @var Integer
     * @access private
     */
    var $privateKeyFormat = self::PRIVATE_FORMAT_PKCS1;
    /**
     * Public Key Format
     *
     * @var Integer
     * @access public
     */
    var $publicKeyFormat = self::PUBLIC_FORMAT_PKCS8;
    /**
     * Modulus (ie. n)
     *
     * @var \phpseclib\Math\BigInteger
     * @access private
     */
    var $modulus;
    /**
     * Modulus length
     *
     * @var \phpseclib\Math\BigInteger
     * @access private
     */
    var $k;
    /**
     * Exponent (ie. e or d)
     *
     * @var \phpseclib\Math\BigInteger
     * @access private
     */
    var $exponent;
    /**
     * Primes for Chinese Remainder Theorem (ie. p and q)
     *
     * @var Array
     * @access private
     */
    var $primes;
    /**
     * Exponents for Chinese Remainder Theorem (ie. dP and dQ)
     *
     * @var Array
     * @access private
     */
    var $exponents;
    /**
     * Coefficients for Chinese Remainder Theorem (ie. qInv)
     *
     * @var Array
     * @access private
     */
    var $coefficients;
    /**
     * Hash name
     *
     * @var String
     * @access private
     */
    var $hashName;
    /**
     * Hash function
     *
     * @var \phpseclib\Crypt\Hash
     * @access private
     */
    var $hash;
    /**
     * Length of hash function output
     *
     * @var Integer
     * @access private
     */
    var $hLen;
    /**
     * Length of salt
     *
     * @var Integer
     * @access private
     */
    var $sLen;
    /**
     * Hash function for the Mask Generation Function
     *
     * @var \phpseclib\Crypt\Hash
     * @access private
     */
    var $mgfHash;
    /**
     * Length of MGF hash function output
     *
     * @var Integer
     * @access private
     */
    var $mgfHLen;
    /**
     * Encryption mode
     *
     * @var Integer
     * @access private
     */
    var $encryptionMode = self::ENCRYPTION_OAEP;
    /**
     * Signature mode
     *
     * @var Integer
     * @access private
     */
    var $signatureMode = self::SIGNATURE_PSS;
    /**
     * Public Exponent
     *
     * @var Mixed
     * @access private
     */
    var $publicExponent = false;
    /**
     * Password
     *
     * @var String
     * @access private
     */
    var $password = false;
    /**
     * Components
     *
     * For use with parsing XML formatted keys.  PHP's XML Parser functions use utilized - instead of PHP's DOM functions -
     * because PHP's XML Parser functions work on PHP4 whereas PHP's DOM functions - although surperior - don't.
     *
     * @see \phpseclib\Crypt\RSA::_start_element_handler()
     * @var Array
     * @access private
     */
    var $components = array();
    /**
     * Current String
     *
     * For use with parsing XML formatted keys.
     *
     * @see \phpseclib\Crypt\RSA::_character_handler()
     * @see \phpseclib\Crypt\RSA::_stop_element_handler()
     * @var Mixed
     * @access private
     */
    var $current;
    /**
     * OpenSSL configuration file name.
     *
     * Set to null to use system configuration file.
     * @see \phpseclib\Crypt\RSA::createKey()
     * @var Mixed
     * @Access public
     */
    var $configFile;
    /**
     * Public key comment field.
     *
     * @var String
     * @access private
     */
    var $comment = 'phpseclib-generated-key';
    /**
     * The constructor
     *
     * If you want to make use of the openssl extension, you'll need to set the mode manually, yourself.  The reason
     * \phpseclib\Crypt\RSA doesn't do it is because OpenSSL doesn't fail gracefully.  openssl_pkey_new(), in particular, requires
     * openssl.cnf be present somewhere and, unfortunately, the only real way to find out is too late.
     *
     * @return \phpseclib\Crypt\RSA
     * @access public
     */
    function __construct()
    {
        $this->configFile = dirname(__FILE__) . '/../openssl.cnf';
        if ( !defined('CRYPT_RSA_MODE') ) {
            switch (true) {
                // Math/BigInteger's openssl requirements are a little less stringent than Crypt/RSA's. in particular,
                // Math/BigInteger doesn't require an openssl.cfg file whereas Crypt/RSA does. so if Math/BigInteger
                // can't use OpenSSL it can be pretty trivially assumed, then, that Crypt/RSA can't either.
                case defined('_OPENSSL_DISABLE'):
                    define('CRYPT_RSA_MODE', self::MODE_INTERNAL);
                    break;
                // openssl_pkey_get_details - which is used in the only place Crypt/RSA.php uses OpenSSL - was introduced in PHP 5.2.0
                case !function_exists('openssl_pkey_get_details'):
                    define('CRYPT_RSA_MODE', self::MODE_INTERNAL);
                    break;
                case extension_loaded('openssl') && version_compare(PHP_VERSION, '4.2.0', '>=') && file_exists($this->configFile):
                    // some versions of XAMPP have mismatched versions of OpenSSL which causes it not to work
                    ob_start();
                    @phpinfo();
                    $content = ob_get_contents();
                    ob_end_clean();
                    preg_match_all('#OpenSSL (Header|Library) Version(.*)#im', $content, $matches);
                    $versions = array();
                    if (!empty($matches[1])) {
                        for ($i = 0; $i < count($matches[1]); $i++) {
                            $fullVersion = trim(str_replace('=>', '', strip_tags($matches[2][$i])));
                            // Remove letter part in OpenSSL version
                            if (!preg_match('/(\d+\.\d+\.\d+)/i', $fullVersion, $m)) {
                                $versions[$matches[1][$i]] = $fullVersion;
                            } else {
                                $versions[$matches[1][$i]] = $m[0];
                            }
                        }
                    }
                    // it doesn't appear that OpenSSL versions were reported upon until PHP 5.3+
                    switch (true) {
                        case !isset($versions['Header']):
                        case !isset($versions['Library']):
                        case $versions['Header'] == $versions['Library']:
                            define('CRYPT_RSA_MODE', self::MODE_OPENSSL);
                            break;
                        default:
                            define('CRYPT_RSA_MODE', self::MODE_INTERNAL);
                            define('_OPENSSL_DISABLE', true);
                    }
                    break;
                default:
                    define('CRYPT_RSA_MODE', self::MODE_INTERNAL);
            }
        }
       

     $this->zero = new BigInteger();
        $this->one = new BigInteger(1);
        $this->hash = new Hash('sha1');
        $this->hLen = $this->hash->getLength();
        $this->hashName = 'sha1';
        $this->mgfHash = new Hash('sha1');
        $this->mgfHLen = $this->mgfHash->getLength();
    }
    /**
     * Create public / private key pair
     *
     * Returns an array with the following three elements:
     *  - 'privatekey': The private key.
     *  - 'publickey':  The public key.
     *  - 'partialkey': A partially computed key (if the execution time exceeded $timeout).
     *                  Will need to be passed back to \phpseclib\Crypt\RSA::createKey() as the third parameter for further processing.
     *
     * @access public
     * @param optional Integer $bits
     * @param optional Integer $timeout
     * @param optional array $p
     */
    function createKey($bits = 1024, $timeout = false, $partial = array())
    {
        if (!defined('CRYPT_RSA_EXPONENT')) {
            // http://en.wikipedia.org/wiki/65537_%28number%29
            define('CRYPT_RSA_EXPONENT', '65537');
        }
        // per <http://cseweb.ucsd.edu/~hovav/dist/survey.pdf#page=5>, this number ought not result in primes smaller
        // than 256 bits. as a consequence if the key you're trying to create is 1024 bits and you've set CRYPT_RSA_SMALLEST_PRIME
        // to 384 bits then you're going to get a 384 bit prime and a 640 bit prime (384 + 1024 % 384). at least if
        // CRYPT_RSA_MODE is set to self::MODE_INTERNAL. if CRYPT_RSA_MODE is set to self::MODE_OPENSSL then
        // CRYPT_RSA_SMALLEST_PRIME is ignored (ie. multi-prime RSA support is more intended as a way to speed up RSA key
        // generation when there's a chance neither gmp nor OpenSSL are installed)
        if (!defined('CRYPT_RSA_SMALLEST_PRIME')) {
            define('CRYPT_RSA_SMALLEST_PRIME', 4096);
        }
        // OpenSSL uses 65537 as the exponent and requires RSA keys be 384 bits minimum
        if ( CRYPT_RSA_MODE == self::MODE_OPENSSL && $bits >= 384 && CRYPT_RSA_EXPONENT == 65537) {
            $config = array();
            if (isset($this->configFile)) {
                $config['config'] = $this->configFile;
            }
            $rsa = openssl_pkey_new(array('private_key_bits' => $bits) + $config);
            openssl_pkey_export($rsa, $privatekey, null, $config);
            $publickey = openssl_pkey_get_details($rsa);
            $publickey = $publickey['key'];
            $privatekey = call_user_func_array(array($this, '_convertPrivateKey'), array_values($this->_parseKey($privatekey, self::PRIVATE_FORMAT_PKCS1)));
            $publickey = call_user_func_array(array($this, '_convertPublicKey'), array_values($this->_parseKey($publickey, self::PUBLIC_FORMAT_PKCS1)));
            // clear the buffer of error strings stemming from a minimalistic openssl.cnf
            while (openssl_error_string() !== false);
            return array(
                'privatekey' => $privatekey,
                'publickey' => $publickey,
                'partialkey' => false
            );
        }
        static $e;
        if (!isset($e)) {
            $e = new BigInteger(CRYPT_RSA_EXPONENT);
        }
        extract($this->_generateMinMax($bits));
        $absoluteMin = $min;
        $temp = $bits >> 1; // divide by two to see how many bits P and Q would be
        if ($temp > CRYPT_RSA_SMALLEST_PRIME) {
            $num_primes = floor($bits / CRYPT_RSA_SMALLEST_PRIME);
            $temp = CRYPT_RSA_SMALLEST_PRIME;
        } else {
            $num_primes = 2;
        }
        extract($this->_generateMinMax($temp + $bits % $temp));
        $finalMax = $max;
        extract($this->_generateMinMax($temp));
        $generator = new BigInteger();
        $n = $this->one->copy();
        if (!empty($partial)) {
            extract(unserialize($partial));
        } else {
            $exponents = $coefficients = $primes = array();
            $lcm = array(
                'top' => $this->one->copy(),
                'bottom' => false
            );
        }
        $start = time();
        $i0 = count($primes) + 1;
        do {
            for ($i = $i0; $i <= $num_primes; $i++) {
                if ($timeout !== false) {
                    $timeout-= time() - $start;
                    $start = time();
                    if ($timeout <= 0) {
                        return array(
                            'privatekey' => '',
                            'publickey'  => '',
                            'partialkey' => serialize(array(
                                'primes' => $primes,
                                'coefficients' => $coefficients,
                                'lcm' => $lcm,
                                'exponents' => $exponents
                            ))
                        );
                    }
                }
                if ($i == $num_primes) {
                    list($min, $temp) = $absoluteMin->divide($n);
                    if (!$temp->equals($this->zero)) {
                        $min = $min->add($this->one); // ie. ceil()
                    }
                    $primes[$i] = $generator->randomPrime($min, $finalMax, $timeout);
                } else {
                    $primes[$i] = $generator->randomPrime($min, $max, $timeout);
                }
                if ($primes[$i] === false) { // if we've reached the timeout
                    if (count($primes) > 1) {
                        $partialkey = '';
                    } else {
                        array_pop($primes);
                        $partialkey = serialize(array(
                            'primes' => $primes,
                            'coefficients' => $coefficients,
                            'lcm' => $lcm,
                            'exponents' => $exponents
                        ));
                    }
                    return array(
                        'privatekey' => '',
                        'publickey'  => '',
                        'partialkey' => $partialkey
                    );
                }
                // the first coefficient is calculated differently from the rest
                // ie. instead of being $primes[1]->modInverse($primes[2]), it's $primes[2]->modInverse($primes[1])
                if ($i > 2) {
                    $coefficients[$i] = $n->modInverse($primes[$i]);
                }
                $n = $n->multiply($primes[$i]);
                $temp = $primes[$i]->subtract($this->one);
                // textbook RSA implementations use Euler's totient function instead of the least common multiple.
                // see http://en.wikipedia.org/wiki/Euler%27s_totient_function
                $lcm['top'] = $lcm['top']->multiply($temp);
                $lcm['bottom'] = $lcm['bottom'] === false ? $temp : $lcm['bottom']->gcd($temp);
                $exponents[$i] = $e->modInverse($temp);
            }
            list($temp) = $lcm['top']->divide($lcm['bottom']);
            $gcd = $temp->gcd($e);
            $i0 = 1;
        } while (!$gcd->equals($this->one));
        $d = $e->modInverse($temp);
        $coefficients[2] = $primes[2]->modInverse($primes[1]);
        // from <http://tools.ietf.org/html/rfc3447#appendix-A.1.2>:
        // RSAPrivateKey ::= SEQUENCE {
        //     version           Version,
        //     modulus           INTEGER,  -- n
        //     publicExponent    INTEGER,  -- e
        //     privateExponent   INTEGER,  -- d
        //     prime1            INTEGER,  -- p
        //     prime2            INTEGER,  -- q
        //     exponent1         INTEGER,  -- d mod (p-1)
        //     exponent2         INTEGER,  -- d mod (q-1)
        //     coefficient       INTEGER,  -- (inverse of q) mod p
        //     otherPrimeInfos   OtherPrimeInfos OPTIONAL
        // }
        return array(
            'privatekey' => $this->_convertPrivateKey($n, $e, $d, $primes, $exponents, $coefficients),
            'publickey'  => $this->_convertPublicKey($n, $e),
            'partialkey' => false
        );
    }
    /**
     * Convert a private key to the appropriate format.
     *
     * @access private
     * @see setPrivateKeyFormat()
     * @param String $RSAPrivateKey
     * @return String
     */
    function _convertPrivateKey($n, $e, $d, $primes, $exponents, $coefficients)
    {
        $signed = $this->privateKeyFormat != self::PRIVATE_FORMAT_XML;
        $num_primes = count($primes);
        $raw = array(
            'version' => $num_primes == 2 ? chr(0) : chr(1), // two-prime vs. multi
            'modulus' => $n->toBytes($signed),
            'publicExponent' => $e->toBytes($signed),
            'privateExponent' => $d->toBytes($signed),
            'prime1' => $primes[1]->toBytes($signed),
            'prime2' => $primes[2]->toBytes($signed),
            'exponent1' => $exponents[1]->toBytes($signed),
            'exponent2' => $exponents[2]->toBytes($signed),
            'coefficient' => $coefficients[2]->toBytes($signed)
        );
        // if the format in question does not support multi-prime rsa and multi-prime rsa was used,
        // call _convertPublicKey() instead.
        switch ($this->privateKeyFormat) {
            case self::PRIVATE_FORMAT_XML:
                if ($num_primes != 2) {
                    return false;
                }
                return "<RSAKeyValue>\r\n" .
                       '  <Modulus>' . base64_encode($raw['modulus']) . "</Modulus>\r\n" .
                       '  <Exponent>' . base64_encode($raw['publicExponent']) . "</Exponent>\r\n" .
                       '  <P>' . base64_encode($raw['prime1']) . "</P>\r\n" .
                       '  <Q>' . base64_encode($raw['prime2']) . "</Q>\r\n" .
                       '  <DP>' . base64_encode($raw['exponent1']) . "</DP>\r\n" .
                       '  <DQ>' . base64_encode($raw['exponent2']) . "</DQ>\r\n" .
                       '  <InverseQ>' . base64_encode($raw['coefficient']) . "</InverseQ>\r\n" .
                       '  <D>' . base64_encode($raw['privateExponent']) . "</D>\r\n" .
                       '</RSAKeyValue>';
                break;
            case self::PRIVATE_FORMAT_PUTTY:
                if ($num_primes != 2) {
                    return false;
                }
                $key = "PuTTY-User-Key-File-2: ssh-rsa\r\nEncryption: ";
                $encryption = (!empty($this->password) || is_string($this->password)) ? 'aes256-cbc' : 'none';
                $key.= $encryption;
                $key.= "\r\nComment: " . $this->comment . "\r\n";
                $public = pack('Na*Na*Na*',
                    strlen('ssh-rsa'), 'ssh-rsa', strlen($raw['publicExponent']), $raw['publicExponent'], strlen($raw['modulus']), $raw['modulus']
                );
                $source = pack('Na*Na*Na*Na*',
                    strlen('ssh-rsa'), 'ssh-rsa', strlen($encryption), $encryption,
                    strlen($this->comment), $this->comment, strlen($public), $public
                );
                $public = base64_encode($public);
                $key.= "Public-Lines: " . ((strlen($public) + 63) >> 6) . "\r\n";
                $key.= chunk_split($public, 64);
                $private = pack('Na*Na*Na*Na*',
                    strlen($raw['privateExponent']), $raw['privateExponent'], strlen($raw['prime1']), $raw['prime1'],
                    strlen($raw['prime2']), $raw['prime2'], strlen($raw['coefficient']), $raw['coefficient']
                );
                if (empty($this->password) && !is_string($this->password)) {
                    $source.= pack('Na*', strlen($private), $private);
                    $hashkey = 'putty-private-key-file-mac-key';
                } else {
                    $private.= Random::string(16 - (strlen($private) & 15));
                    $source.= pack('Na*', strlen($private), $private);
                    $sequence = 0;
                    $symkey = '';
                    while (strlen($symkey) < 32) {
                        $temp = pack('Na*', $sequence++, $this->password);
                        $symkey.= pack('H*', sha1($temp));
                    }
                    $symkey = substr($symkey, 0, 32);
                    $crypto = new AES();
                    $crypto->setKey($symkey);
                    $crypto->disablePadding();
                    $private = $crypto->encrypt($private);
                    $hashkey = 'putty-private-key-file-mac-key' . $this->password;
                }
                $private = base64_encode($private);
                $key.= 'Private-Lines: ' . ((strlen($private) + 63) >> 6) . "\r\n";
                $key.= chunk_split($private, 64);
                $hash = new Hash('sha1');
                $hash->setKey(pack('H*', sha1($hashkey)));
                $key.= 'Private-MAC: ' . bin2hex($hash->hash($source)) . "\r\n";
                return $key;
            default: // eg. self::PRIVATE_FORMAT_PKCS1
                $components = array();
                foreach ($raw as $name => $value) {
                    $components[$name] = pack('Ca*a*', self::ASN1_INTEGER, $this->_encodeLength(strlen($value)), $value);
                }
                $RSAPrivateKey = implode('', $components);
                if ($num_primes > 2) {
                    $OtherPrimeInfos = '';
                    for ($i = 3; $i <= $num_primes; $i++) {
                        // OtherPrimeInfos ::= SEQUENCE SIZE(1..MAX) OF OtherPrimeInfo
                        //
                        // OtherPrimeInfo ::= SEQUENCE {
                        //     prime             INTEGER,  -- ri
                        //     exponent          INTEGER,  -- di
                        //     coefficient       INTEGER   -- ti
                        // }
                        $OtherPrimeInfo = pack('Ca*a*', self::ASN1_INTEGER, $this->_encodeLength(strlen($primes[$i]->toBytes(true))), $primes[$i]->toBytes(true));
                        $OtherPrimeInfo.= pack('Ca*a*', self::ASN1_INTEGER, $this->_encodeLength(strlen($exponents[$i]->toBytes(true))), $exponents[$i]->toBytes(true));
                        $OtherPrimeInfo.= pack('Ca*a*', self::ASN1_INTEGER, $this->_encodeLength(strlen($coefficients[$i]->toBytes(true))), $coefficients[$i]->toBytes(true));
                        $OtherPrimeInfos.= pack('Ca*a*', self::ASN1_SEQUENCE, $this->_encodeLength(strlen($OtherPrimeInfo)), $OtherPrimeInfo);
                    }
                    $RSAPrivateKey.= pack('Ca*a*', self::ASN1_SEQUENCE, $this->_encodeLength(strlen($OtherPrimeInfos)), $OtherPrimeInfos);
                }
                $RSAPrivateKey = pack('Ca*a*', self::ASN1_SEQUENCE, $this->_encodeLength(strlen($RSAPrivateKey)), $RSAPrivateKey);
                if ($this->privateKeyFormat == self::PRIVATE_FORMAT_PKCS8) {
                    $rsaOID = pack('H*', '300d06092a864886f70d0101010500'); // hex version of MA0GCSqGSIb3DQEBAQUA
                    $RSAPrivateKey = pack('Ca*a*Ca*a*',
                        self::ASN1_INTEGER, "\01\00", $rsaOID, 4, $this->_encodeLength(strlen($RSAPrivateKey)), $RSAPrivateKey
                    );
                    $RSAPrivateKey = pack('Ca*a*', self::ASN1_SEQUENCE, $this->_encodeLength(strlen($RSAPrivateKey)), $RSAPrivateKey);
                    if (!empty($this->password) || is_string($this->password)) {
                        $salt = Random::string(8);
                        $iterationCount = 2048;
                        $crypto = new DES();
                        $crypto->setPassword($this->password, 'pbkdf1', 'md5', $salt, $iterationCount);
                        $RSAPrivateKey = $crypto->encrypt($RSAPrivateKey);
                        $parameters = pack('Ca*a*Ca*N',
                            self::ASN1_OCTETSTRING, $this->_encodeLength(strlen($salt)), $salt,
                            self::ASN1_INTEGER, $this->_encodeLength(4), $iterationCount
                        );
                        $pbeWithMD5AndDES_CBC = "\x2a\x86\x48\x86\xf7\x0d\x01\x05\x03";
                        $encryptionAlgorithm = pack('Ca*a*Ca*a*',
                            self::ASN1_OBJECT, $this->_encodeLength(strlen($pbeWithMD5AndDES_CBC)), $pbeWithMD5AndDES_CBC,
                            self::ASN1_SEQUENCE, $this->_encodeLength(strlen($parameters)), $parameters
                        );
                        $RSAPrivateKey = pack('Ca*a*Ca*a*',
                            self::ASN1_SEQUENCE, $this->_encodeLength(strlen($encryptionAlgorithm)), $encryptionAlgorithm,
                            self::ASN1_OCTETSTRING, $this->_encodeLength(strlen($RSAPrivateKey)), $RSAPrivateKey
                        );
                        $RSAPrivateKey = pack('Ca*a*', self::ASN1_SEQUENCE, $this->_encodeLength(strlen($RSAPrivateKey)), $RSAPrivateKey);
                        $RSAPrivateKey = "-----BEGIN ENCRYPTED PRIVATE KEY-----\r\n" .
                                         chunk_split(base64_encode($RSAPrivateKey), 64) .
                                         '-----END ENCRYPTED PRIVATE KEY-----';
                    } else {
                        $RSAPrivateKey = "-----BEGIN PRIVATE KEY-----\r\n" .
                                         chunk_split(base64_encode($RSAPrivateKey), 64) .
                                         '-----END PRIVATE KEY-----';
                    }
                    return $RSAPrivateKey;
                }
                if (!empty($this->password) || is_string($this->password)) {
                    $iv = Random::string(8);
                    $symkey = pack('H*', md5($this->password . $iv)); // symkey is short for symmetric key
                    $symkey.= substr(pack('H*', md5($symkey . $this->password . $iv)), 0, 8);
                    $des = new TripleDES();
                    $des->setKey($symkey);
                    $des->setIV($iv);
                    $iv = strtoupper(bin2hex($iv));
                    $RSAPrivateKey = "-----BEGIN RSA PRIVATE KEY-----\r\n" .
                                     "Proc-Type: 4,ENCRYPTED\r\n" .
                                     "DEK-Info: DES-EDE3-CBC,$iv\r\n" .
                                     "\r\n" .
                                     chunk_split(base64_encode($des->encrypt($RSAPrivateKey)), 64) .
                                     '-----END RSA PRIVATE KEY-----';
                } else {
                    $RSAPrivateKey = "-----BEGIN RSA PRIVATE KEY-----\r\n" .
                                     chunk_split(base64_encode($RSAPrivateKey), 64) .
                                     '-----END RSA PRIVATE KEY-----';
                }
                return $RSAPrivateKey;
        }
    }
    /**
     * Convert a public key to the appropriate format
     *
     * @access private
     * @see setPublicKeyFormat()
     * @param String $RSAPrivateKey
     * @return String
     */
    function _convertPublicKey($n, $e)
    {
        $signed = $this->publicKeyFormat != self::PUBLIC_FORMAT_XML;
        $modulus = $n->toBytes($signed);
        $publicExponent = $e->toBytes($signed);
        switch ($this->publicKeyFormat) {
            case self::PUBLIC_FORMAT_RAW:
                return array('e' => $e->copy(), 'n' => $n->copy());
            case self::PUBLIC_FORMAT_XML:
                return "<RSAKeyValue>\r\n" .
                       '  <Modulus>' . base64_encode($modulus) . "</Modulus>\r\n" .
                       '  <Exponent>' . base64_encode($publicExponent) . "</Exponent>\r\n" .
                       '</RSAKeyValue>';
                break;
            case self::PUBLIC_FORMAT_OPENSSH:
                // from <http://tools.ietf.org/html/rfc4253#page-15>:
                // string    "ssh-rsa"
                // mpint     e
                // mpint     n
                $RSAPublicKey = pack('Na*Na*Na*', strlen('ssh-rsa'), 'ssh-rsa', strlen($publicExponent), $publicExponent, strlen($modulus), $modulus);
                $RSAPublicKey = 'ssh-rsa ' . base64_encode($RSAPublicKey) . ' ' . $this->comment;
                return $RSAPublicKey;
            default: // eg. self::PUBLIC_FORMAT_PKCS1_RAW or self::PUBLIC_FORMAT_PKCS1
                // from <http://tools.ietf.org/html/rfc3447#appendix-A.1.1>:
                // RSAPublicKey ::= SEQUENCE {
                //     modulus           INTEGER,  -- n
                //     publicExponent    INTEGER   -- e
                // }
                $components = array(
                    'modulus' => pack('Ca*a*', self::ASN1_INTEGER, $this->_encodeLength(strlen($modulus)), $modulus),
                    'publicExponent' => pack('Ca*a*', self::ASN1_INTEGER, $this->_encodeLength(strlen($publicExponent)), $publicExponent)
                );
                $RSAPublicKey = pack('Ca*a*a*',
                    self::ASN1_SEQUENCE, $this->_encodeLength(strlen($components['modulus']) + strlen($components['publicExponent'])),
                    $components['modulus'], $components['publicExponent']
                );
                if ($this->publicKeyFormat == self::PUBLIC_FORMAT_PKCS1_RAW) {
                    $RSAPublicKey = "-----BEGIN RSA PUBLIC KEY-----\r\n" .
                                    chunk_split(base64_encode($RSAPublicKey), 64) .
                                    '-----END RSA PUBLIC KEY-----';
                } else {
                    // sequence(oid(1.2.840.113549.1.1.1), null)) = rsaEncryption.
                    $rsaOID = pack('H*', '300d06092a864886f70d0101010500'); // hex version of MA0GCSqGSIb3DQEBAQUA
                    $RSAPublicKey = chr(0) . $RSAPublicKey;
                    $RSAPublicKey = chr(3) . $this->_encodeLength(strlen($RSAPublicKey)) . $RSAPublicKey;
                    $RSAPublicKey = pack('Ca*a*',
                        self::ASN1_SEQUENCE, $this->_encodeLength(strlen($rsaOID . $RSAPublicKey)), $rsaOID . $RSAPublicKey
                    );
                    $RSAPublicKey = "-----BEGIN PUBLIC KEY-----\r\n" .
                                     chunk_split(base64_encode($RSAPublicKey), 64) .
                                     '-----END PUBLIC KEY-----';
                }
                return $RSAPublicKey;
        }
    }
    /**
     * Break a public or private key down into its constituant components
     *
     * @access private
     * @see _convertPublicKey()
     * @see _convertPrivateKey()
     * @param String $key
     * @param Integer $type
     * @return Array
     */
    function _parseKey($key, $type)
    {
        if ($type != self::PUBLIC_FORMAT_RAW && !is_string($key)) {
            return false;
        }
        switch ($type) {
            case self::PUBLIC_FORMAT_RAW:
                if (!is_array($key)) {
                    return false;
                }
                $components = array();
                switch (true) {
                    case isset($key['e']):
                        $components['publicExponent'] = $key['e']->copy();
                        break;
                    case isset($key['exponent']):
                        $components['publicExponent'] = $key['exponent']->copy();
                        break;
                    case isset($key['publicExponent']):
                        $components['publicExponent'] = $key['publicExponent']->copy();
                        break;
                    case isset($key[0]):
                        $components['publicExponent'] = $key[0]->copy();
                }
                switch (true) {
                    case isset($key['n']):
                        $components['modulus'] = $key['n']->copy();
                        break;
                    case isset($key['modulo']):
                        $components['modulus'] = $key['modulo']->copy();
                        break;
                    case isset($key['modulus']):
                        $components['modulus'] = $key['modulus']->copy();
                        break;
                    case isset($key[1]):
                        $components['modulus'] = $key[1]->copy();
                }
                return isset($components['modulus']) && isset($components['publicExponent']) ? $components : false;
            case self::PRIVATE_FORMAT_PKCS1:
            case self::PRIVATE_FORMAT_PKCS8:
            case self::PUBLIC_FORMAT_PKCS1:
                /* Although PKCS#1 proposes a format that public and private keys can use, encrypting them is
                   "outside the scope" of PKCS#1.  PKCS#1 then refers you to PKCS#12 and PKCS#15 if you're wanting to
                   protect private keys, however, that's not what OpenSSL* does.  OpenSSL protects private keys by adding
                   two new "fields" to the key - DEK-Info and Proc-Type.  These fields are discussed here:
                   http://tools.ietf.org/html/rfc1421#section-4.6.1.1
                   http://tools.ietf.org/html/rfc1421#section-4.6.1.3
                   DES-EDE3-CBC as an algorithm, however, is not discussed anywhere, near as I can tell.
                   DES-CBC and DES-EDE are discussed in RFC1423, however, DES-EDE3-CBC isn't, nor is its key derivation
                   function.  As is, the definitive authority on this encoding scheme isn't the IETF but rather OpenSSL's
                   own implementation.  ie. the implementation *is* the standard and any bugs that may exist in that
                   implementation are part of the standard, as well.
                   * OpenSSL is the de facto standard.  It's utilized by OpenSSH and other projects */
                if (preg_match('#DEK-Info: (.+),(.+)#', $key, $matches)) {
                    $iv = pack('H*', trim($matches[2]));
                    $symkey = pack('H*', md5($this->password . substr($iv, 0, 8))); // symkey is short for symmetric key
                    $symkey.= pack('H*', md5($symkey . $this->password . substr($iv, 0, 8)));
                    // remove the Proc-Type / DEK-Info sections as they're no longer needed
                    $key = preg_replace('#^(?:Proc-Type|DEK-Info): .*#m', '', $key);
                    $ciphertext = $this->_extractBER($key);
                    if ($ciphertext === false) {
                        $ciphertext = $key;
                    }
                    switch ($matches[1]) {
                        case 'AES-256-CBC':
                            $crypto = new AES();
                            break;
                        case 'AES-128-CBC':
                            $symkey = substr($symkey, 0, 16);
                            $crypto = new AES();
                            break;
                        case 'DES-EDE3-CFB':
                            $crypto = new TripleDES(Base::MODE_CFB);
                            break;
                        case 'DES-EDE3-CBC':
                            $symkey = substr($symkey, 0, 24);
                            $crypto = new TripleDES();
                            break;
                        case 'DES-CBC':
                            $crypto = new DES();
                            break;
                        default:
                            return false;
                    }
                    $crypto->setKey($symkey);
                    $crypto->setIV($iv);
                    $decoded = $crypto->decrypt($ciphertext);
                } else {
                    $decoded = $this->_extractBER($key);
                }
                if ($decoded !== false) {
                    $key = $decoded;
                }
                $components = array();
                if (ord($this->_string_shift($key)) != self::ASN1_SEQUENCE) {
                    return false;
                }
                if ($this->_decodeLength($key) != strlen($key)) {
                    return false;
                }
                $tag = ord($this->_string_shift($key));
                /* intended for keys for which OpenSSL's asn1parse returns the following:
                    0:d=0  hl=4 l= 631 cons: SEQUENCE
                    4:d=1  hl=2 l=   1 prim:  INTEGER           :00
                    7:d=1  hl=2 l=  13 cons:  SEQUENCE
                    9:d=2  hl=2 l=   9 prim:   OBJECT            :rsaEncryption
                   20:d=2  hl=2 l=   0 prim:   NULL
                   22:d=1  hl=4 l= 609 prim:  OCTET STRING
                   ie. PKCS8 keys*/
                if ($tag == self::ASN1_INTEGER && substr($key, 0, 3) == "\x01\x00\x30") {
                    $this->_string_shift($key, 3);
                    $tag = self::ASN1_SEQUENCE;
                }
                if ($tag == self::ASN1_SEQUENCE) {
                    $temp = $this->_string_shift($key, $this->_decodeLength($key));
                    if (ord($this->_string_shift($temp)) != self::ASN1_OBJECT) {
                        return false;
                    }
                    $length = $this->_decodeLength($temp);
                    switch ($this->_string_shift($temp, $length)) {
                        case "\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01": // rsaEncryption
                            break;
                        case "\x2a\x86\x48\x86\xf7\x0d\x01\x05\x03": // pbeWithMD5AndDES-CBC
                            /*
                               PBEParameter ::= SEQUENCE {
                                   salt OCTET STRING (SIZE(8)),
                                   iterationCount INTEGER }
                            */
                            if (ord($this->_string_shift($temp)) != self::ASN1_SEQUENCE) {
                                return false;
                            }
                            if ($this->_decodeLength($temp) != strlen($temp)) {
                                return false;
                            }
                            $this->_string_shift($temp); // assume it's an octet string
                            $salt = $this->_string_shift($temp, $this->_decodeLength($temp));
                            if (ord($this->_string_shift($temp)) != self::ASN1_INTEGER) {
                                return false;
                            }
                            $this->_decodeLength($temp);
                            list(, $iterationCount) = unpack('N', str_pad($temp, 4, chr(0), STR_PAD_LEFT));
                            $this->_string_shift($key); // assume it's an octet string
                            $length = $this->_decodeLength($key);
                            if (strlen($key) != $length) {
                                return false;
                            }
                            $crypto = new DES();
                            $crypto->setPassword($this->password, 'pbkdf1', 'md5', $salt, $iterationCount);
                            $key = $crypto->decrypt($key);
                            if ($key === false) {
                                return false;
                            }
                            return $this->_parseKey($key, self::PRIVATE_FORMAT_PKCS1);
                        default:
                            return false;
                    }
                    /* intended for keys for which OpenSSL's asn1parse returns the following:
                        0:d=0  hl=4 l= 290 cons: SEQUENCE
                        4:d=1  hl=2 l=  13 cons:  SEQUENCE
                        6:d=2  hl=2 l=   9 prim:   OBJECT            :rsaEncryption
                       17:d=2  hl=2 l=   0 prim:   NULL
                       19:d=1  hl=4 l= 271 prim:  BIT STRING */
                    $tag = ord($this->_string_shift($key)); // skip over the BIT STRING / OCTET STRING tag
                    $this->_decodeLength($key); // skip over the BIT STRING / OCTET STRING length
                    // "The initial octet shall encode, as an unsigned binary integer wtih bit 1 as the least significant bit, the number of
                    //  unused bits in the final subsequent octet. The number shall be in the range zero to seven."
                    //  -- http://www.itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf (section 8.6.2.2)
                    if ($tag == self::ASN1_BITSTRING) {
                        $this->_string_shift($key);
                    }
                    if (ord($this->_string_shift($key)) != self::ASN1_SEQUENCE) {
                        return false;
                    }
                    if ($this->_decodeLength($key) != strlen($key)) {
                        return false;
                    }
                    $tag = ord($this->_string_shift($key));
                }
                if ($tag != self::ASN1_INTEGER) {
                    return false;
                }
                $length = $this->_decodeLength($key);
                $temp = $this->_string_shift($key, $length);
                if (strlen($temp) != 1 || ord($temp) > 2) {
                    $components['modulus'] = new BigInteger($temp, 256);
                    $this->_string_shift($key); // skip over self::ASN1_INTEGER
                    $length = $this->_decodeLength($key);
                    $components[$type == self::PUBLIC_FORMAT_PKCS1 ? 'publicExponent' : 'privateExponent'] = new BigInteger($this->_string_shift($key, $length), 256);
                    return $components;
                }
                if (ord($this->_string_shift($key)) != self::ASN1_INTEGER) {
                    return false;
                }
                $length = $this->_decodeLength($key);
                $components['modulus'] = new BigInteger($this->_string_shift($key, $length), 256);
                $this->_string_shift($key);
                $length = $this->_decodeLength($key);
                $components['publicExponent'] = new BigInteger($this->_string_shift($key, $length), 256);
                $this->_string_shift($key);
                $length = $this->_decodeLength($key);
                $components['privateExponent'] = new BigInteger($this->_string_shift($key, $length), 256);
                $this->_string_shift($key);
                $length = $this->_decodeLength($key);
                $components['primes'] = array(1 => new BigInteger($this->_string_shift($key, $length), 256));
                $this->_string_shift($key);
                $length = $this->_decodeLength($key);
                $components['primes'][] = new BigInteger($this->_string_shift($key, $length), 256);
                $this->_string_shift($key);
                $length = $this->_decodeLength($key);
                $components['exponents'] = array(1 => new BigInteger($this->_string_shift($key, $length), 256));
                $this->_string_shift($key);
                $length = $this->_decodeLength($key);
                $components['exponents'][] = new BigInteger($this->_string_shift($key, $length), 256);
                $this->_string_shift($key);
                $length = $this->_decodeLength($key);
                $components['coefficients'] = array(2 => new BigInteger($this->_string_shift($key, $length), 256));
                if (!empty($key)) {
                    if (ord($this->_string_shift($key)) != self::ASN1_SEQUENCE) {
                        return false;
                    }
                    $this->_decodeLength($key);
                    while (!empty($key)) {
                        if (ord($this->_string_shift($key)) != self::ASN1_SEQUENCE) {
                            return false;
                        }
                        $this->_decodeLength($key);
                        $key = substr($key, 1);
                        $length = $this->_decodeLength($key);
                        $components['primes'][] = new BigInteger($this->_string_shift($key, $length), 256);
                        $this->_string_shift($key);
                        $length = $this->_decodeLength($key);
                        $components['exponents'][] = new BigInteger($this->_string_shift($key, $length), 256);
                        $this->_string_shift($key);
                        $length = $this->_decodeLength($key);
                        $components['coefficients'][] = new BigInteger($this->_string_shift($key, $length), 256);
                    }
                }
                return $components;
            case self::PUBLIC_FORMAT_OPENSSH:
                $parts = explode(' ', $key, 3);
                $key = isset($parts[1]) ? base64_decode($parts[1]) : false;
                if ($key === false) {
                    return false;
                }
                $comment = isset($parts[2]) ? $parts[2] : false;
                $cleanup = substr($key, 0, 11) == "\0\0\0\7ssh-rsa";
                if (strlen($key) <= 4) {
                    return false;
                }
                extract(unpack('Nlength', $this->_string_shift($key, 4)));
                $publicExponent = new BigInteger($this->_string_shift($key, $length), -256);
                if (strlen($key) <= 4) {
                    return false;
                }
                extract(unpack('Nlength', $this->_string_shift($key, 4)));
                $modulus = new BigInteger($this->_string_shift($key, $length), -256);
                if ($cleanup && strlen($key)) {
                    if (strlen($key) <= 4) {
                        return false;
                    }
                    extract(unpack('Nlength', $this->_string_shift($key, 4)));
                    $realModulus = new BigInteger($this->_string_shift($key, $length), -256);
                    return strlen($key) ? false : array(
                        'modulus' => $realModulus,
                        'publicExponent' => $modulus,
                        'comment' => $comment
                    );
                } else {
                    return strlen($key) ? false : array(
                        'modulus' => $modulus,
                        'publicExponent' => $publicExponent,
                        'comment' => $comment
                    );
                }
            // http://www.w3.org/TR/xmldsig-core/#sec-RSAKeyValue
            // http://en.wikipedia.org/wiki/XML_Signature
            case self::PRIVATE_FORMAT_XML:
            case self::PUBLIC_FORMAT_XML:
                $this->components = array();
                $xml = xml_parser_create('UTF-8');
                xml_set_object($xml, $this);
                xml_set_element_handler($xml, '_start_element_handler', '_stop_element_handler');
                xml_set_character_data_handler($xml, '_data_handler');
                // add <xml></xml> to account for "dangling" tags like <BitStrength>...</BitStrength> that are sometimes added
                if (!xml_parse($xml, '<xml>' . $key . '</xml>')) {
                    return false;
                }
                return isset($this->components['modulus']) && isset($this->components['publicExponent']) ? $this->components : false;
            // from PuTTY's SSHPUBK.C
            case self::PRIVATE_FORMAT_PUTTY:
                $components = array();
                $key = preg_split('#\r\n|\r|\n#', $key);
                $type = trim(preg_replace('#PuTTY-User-Key-File-2: (.+)#', '$1', $key[0]));
                if ($type != 'ssh-rsa') {
                    return false;
                }
                $encryption = trim(preg_replace('#Encryption: (.+)#', '$1', $key[1]));
                $comment = trim(preg_replace('#Comment: (.+)#', '$1', $key[2]));
                $publicLength = trim(preg_replace('#Public-Lines: (\d+)#', '$1', $key[3]));
                $public = base64_decode(implode('', array_map('trim', array_slice($key, 4, $publicLength))));
                $public = substr($public, 11);
                extract(unpack('Nlength', $this->_string_shift($public, 4)));
                $components['publicExponent'] = new BigInteger($this->_string_shift($public, $length), -256);
                extract(unpack('Nlength', $this->_string_shift($public, 4)));
                $components['modulus'] = new BigInteger($this->_string_shift($public, $length), -256);
                $privateLength = trim(preg_replace('#Private-Lines: (\d+)#', '$1', $key[$publicLength + 4]));
                $private = base64_decode(implode('', array_map('trim', array_slice($key, $publicLength + 5, $privateLength))));
                switch ($encryption) {
                    case 'aes256-cbc':
                        $symkey = '';
                        $sequence = 0;
                        while (strlen($symkey) < 32) {
                            $temp = pack('Na*', $sequence++, $this->password);
                            $symkey.= pack('H*', sha1($temp));
                        }
                        $symkey = substr($symkey, 0, 32);
                        $crypto = new AES();
                }
                if ($encryption != 'none') {
                    $crypto->setKey($symkey);
                    $crypto->disablePadding();
                    $private = $crypto->decrypt($private);
                    if ($private === false) {
                        return false;
                    }
                }
                extract(unpack('Nlength', $this->_string_shift($private, 4)));
                if (strlen($private) < $length) {
                    return false;
                }
                $components['privateExponent'] = new BigInteger($this->_string_shift($private, $length), -256);
                extract(unpack('Nlength', $this->_string_shift($private, 4)));
                if (strlen($private) < $length) {
                    return false;
                }
                $components['primes'] = array(1 => new BigInteger($this->_string_shift($private, $length), -256));
                extract(unpack('Nlength', $this->_string_shift($private, 4)));
                if (strlen($private) < $length) {
                    return false;
                }
                $components['primes'][] = new BigInteger($this->_string_shift($private, $length), -256);
                $temp = $components['primes'][1]->subtract($this->one);
                $components['exponents'] = array(1 => $components['publicExponent']->modInverse($temp));
                $temp = $components['primes'][2]->subtract($this->one);
                $components['exponents'][] = $components['publicExponent']->modInverse($temp);
                extract(unpack('Nlength', $this->_string_shift($private, 4)));
                if (strlen($private) < $length) {
                    return false;
                }
                $components['coefficients'] = array(2 => new BigInteger($this->_string_shift($private, $length), -256));
                return $components;
        }
    }
    /**
     * Returns the key size
     *
     * More specifically, this returns the size of the modulo in bits.
     *
     * @access public
     * @return Integer
     */
    function getSize()
    {
        return !isset($this->modulus) ? 0 : strlen($this->modulus->toBits());
    }
    /**
     * Start Element Handler
     *
     * Called by xml_set_element_handler()
     *
     * @access private
     * @param Resource $parser
     * @param String $name
     * @param Array $attribs
     */
    function _start_element_handler($parser, $name, $attribs)
    {
        //$name = strtoupper($name);
        switch ($name) {
            case 'MODULUS':
                $this->current = &$this->components['modulus'];
                break;
            case 'EXPONENT':
                $this->current = &$this->components['publicExponent'];
                break;
            case 'P':
                $this->current = &$this->components['primes'][1];
                break;
            case 'Q':
                $this->current = &$this->components['primes'][2];
                break;
            case 'DP':
                $this->current = &$this->components['exponents'][1];
                break;
            case 'DQ':
                $this->current = &$this->components['exponents'][2];
                break;
            case 'INVERSEQ':
                $this->current = &$this->components['coefficients'][2];
                break;
            case 'D':
                $this->current = &$this->components['privateExponent'];
        }
        $this->current = '';
    }
    /**
     * Stop Element Handler
     *
     * Called by xml_set_element_handler()
     *
     * @access private
     * @param Resource $parser
     * @param String $name
     */
    function _stop_element_handler($parser, $name)
    {
        if (isset($this->current)) {
            $this->current = new BigInteger(base64_decode($this->current), 256);
            unset($this->current);
        }
    }
    /**
     * Data Handler
     *
     * Called by xml_set_character_data_handler()
     *
     * @access private
     * @param Resource $parser
     * @param String $data
     */
    function _data_handler($parser, $data)
    {
        if (!isset($this->current) || is_object($this->current)) {
            return;
        }
        $this->current.= trim($data);
    }
    /**
     * Loads a public or private key
     *
     * Returns true on success and false on failure (ie. an incorrect password was provided or the key was malformed)
     *
     * @access public
     * @param String $key
     * @param Integer $type optional
     */
    function loadKey($key, $type = false)
    {
        if ($key instanceof RSA) {
            $this->privateKeyFormat = $key->privateKeyFormat;
            $this->publicKeyFormat = $key->publicKeyFormat;
            $this->k = $key->k;
            $this->hLen = $key->hLen;
            $this->sLen = $key->sLen;
            $this->mgfHLen = $key->mgfHLen;
            $this->encryptionMode = $key->encryptionMode;
            $this->signatureMode = $key->signatureMode;
            $this->password = $key->password;
            $this->configFile = $key->configFile;
            $this->comment = $key->comment;
            if (is_object($key->hash)) {
                $this->hash = new Hash($key->hash->getHash());
            }
            if (is_object($key->mgfHash)) {
                $this->mgfHash = new Hash($key->mgfHash->getHash());
            }
            if (is_object($key->modulus)) {
                $this->modulus = $key->modulus->copy();
            }
            if (is_object($key->exponent)) {
                $this->exponent = $key->exponent->copy();
            }
            if (is_object($key->publicExponent)) {
                $this->publicExponent = $key->publicExponent->copy();
            }
            $this->primes = array();
            $this->exponents = array();
            $this->coefficients = array();
            foreach ($this->primes as $prime) {
                $this->primes[] = $prime->copy();
            }
            foreach ($this->exponents as $exponent) {
                $this->exponents[] = $exponent->copy();
            }
            foreach ($this->coefficients as $coefficient) {
                $this->coefficients[] = $coefficient->copy();
            }
            return true;
        }
        if ($type === false) {
            $types = array(
                self::PUBLIC_FORMAT_RAW,
                self::PRIVATE_FORMAT_PKCS1,
                self::PRIVATE_FORMAT_XML,
                self::PRIVATE_FORMAT_PUTTY,
                self::PUBLIC_FORMAT_OPENSSH
            );
            foreach ($types as $type) {
                $components = $this->_parseKey($key, $type);
                if ($components !== false) {
                    break;
                }
            }
        } else {
            $components = $this->_parseKey($key, $type);
        }
        if ($components === false) {
            return false;
        }
        if (isset($components['comment']) && $components['comment'] !== false) {
            $this->comment = $components['comment'];
        }
        $this->modulus = $components['modulus'];
        $this->k = strlen($this->modulus->toBytes());
        $this->exponent = isset($components['privateExponent']) ? $components['privateExponent'] : $components['publicExponent'];
        if (isset($components['primes'])) {
            $this->primes = $components['primes'];
            $this->exponents = $components['exponents'];
            $this->coefficients = $components['coefficients'];
            $this->publicExponent = $components['publicExponent'];
        } else {
            $this->primes = array();
            $this->exponents = array();
            $this->coefficients = array();
            $this->publicExponent = false;
        }
        switch ($type) {
            case self::PUBLIC_FORMAT_OPENSSH:
            case self::PUBLIC_FORMAT_RAW:
                $this->setPublicKey();
                break;
            case self::PRIVATE_FORMAT_PKCS1:
                switch (true) {
                    case strpos($key, '-BEGIN PUBLIC KEY-') !== false:
                    case strpos($key, '-BEGIN RSA PUBLIC KEY-') !== false:
                        $this->setPublicKey();
                }
        }
        return true;
    }
    /**
     * Sets the password
     *
     * Private keys can be encrypted with a password.  To unset the password, pass in the empty string or false.
     * Or rather, pass in $password such that empty($password) && !is_string($password) is true.
     *
     * @see createKey()
     * @see loadKey()
     * @access public
     * @param String $password
     */
    function setPassword($password = false)
    {
        $this->password = $password;
    }
    /**
     * Defines the public key
     *
     * Some private key formats define the public exponent and some don't.  Those that don't define it are problematic when
     * used in certain contexts.  For example, in SSH-2, RSA authentication works by sending the public key along with a
     * message signed by the private key to the server.  The SSH-2 server looks the public key up in an index of public keys
     * and if it's present then proceeds to verify the signature.  Problem is, if your private key doesn't include the public
     * exponent this won't work unless you manually add the public exponent. phpseclib tries to guess if the key being used
     * is the public key but in the event that it guesses incorrectly you might still want to explicitly set the key as being
     * public.
     *
     * Do note that when a new key is loaded the index will be cleared.
     *
     * Returns true on success, false on failure
     *
     * @see getPublicKey()
     * @access public
     * @param String $key optional
     * @param Integer $type optional
     * @return Boolean
     */
    function setPublicKey($key = false, $type = false)
    {
        // if a public key has already been loaded return false
        if (!empty($this->publicExponent)) {
            return false;
        }
        if ($key === false && !empty($this->modulus)) {
            $this->publicExponent = $this->exponent;
            return true;
        }
        if ($type === false) {
            $types = array(
                self::PUBLIC_FORMAT_RAW,
                self::PUBLIC_FORMAT_PKCS1,
                self::PUBLIC_FORMAT_XML,
                self::PUBLIC_FORMAT_OPENSSH
            );
            foreach ($types as $type) {
                $components = $this->_parseKey($key, $type);
                if ($components !== false) {
                    break;
                }
            }
        } else {
            $components = $this->_parseKey($key, $type);
        }
        if ($components === false) {
            return false;
        }
        if (empty($this->modulus) || !$this->modulus->equals($components['modulus'])) {
            $this->modulus = $components['modulus'];
            $this->exponent = $this->publicExponent = $components['publicExponent'];
            return true;
        }
        $this->publicExponent = $components['publicExponent'];
        return true;
    }
    /**
     * Defines the private key
     *
     * If phpseclib guessed a private key was a public key and loaded it as such it might be desirable to force
     * phpseclib to treat the key as a private key. This function will do that.
     *
     * Do note that when a new key is loaded the index will be cleared.
     *
     * Returns true on success, false on failure
     *
     * @see getPublicKey()
     * @access public
     * @param String $key optional
     * @param Integer $type optional
     * @return Boolean
     */
    function setPrivateKey($key = false, $type = false)
    {
        if ($key === false && !empty($this->publicExponent)) {
            unset($this->publicExponent);
            return true;
        }
        $rsa = new RSA();
        if (!$rsa->loadKey($key, $type)) {
            return false;
        }
        unset($rsa->publicExponent);
        // don't overwrite the old key if the new key is invalid
        $this->loadKey($rsa);
        return true;
    }
    /**
     * Returns the public key
     *
     * The public key is only returned under two circumstances - if the private key had the public key embedded within it
     * or if the public key was set via setPublicKey().  If the currently loaded key is supposed to be the public key this
     * function won't return it since this library, for the most part, doesn't distinguish between public and private keys.
     *
     * @see getPublicKey()
     * @access public
     * @param String $key
     * @param Integer $type optional
     */
    function getPublicKey($type = self::PUBLIC_FORMAT_PKCS8)
    {
        if (empty($this->modulus) || empty($this->publicExponent)) {
            return false;
        }
        $oldFormat = $this->publicKeyFormat;
        $this->publicKeyFormat = $type;
        $temp = $this->_convertPublicKey($this->modulus, $this->publicExponent);
        $this->publicKeyFormat = $oldFormat;
        return $temp;
    }
    /**
     * Returns the public key's fingerprint
     *
     * The public key's fingerprint is returned, which is equivalent to running `ssh-keygen -lf rsa.pub`. If there is
     * no public key currently loaded, false is returned.
     * Example output (md5): "c1:b1:30:29:d7:b8:de:6c:97:77:10:d7:46:41:63:87" (as specified by RFC 4716)
     *
     * @access public
     * @param String $algorithm The hashing algorithm to be used. Valid options are 'md5' and 'sha256'. False is returned
     * for invalid values.
     */
    public function getPublicKeyFingerprint($algorithm = 'md5')
    {
        if (empty($this->modulus) || empty($this->publicExponent)) {
            return false;
        }
        $modulus = $this->modulus->toBytes(true);
        $publicExponent = $this->publicExponent->toBytes(true);
        $RSAPublicKey = pack('Na*Na*Na*', strlen('ssh-rsa'), 'ssh-rsa', strlen($publicExponent), $publicExponent, strlen($modulus), $modulus);
        switch($algorithm)
        {
            case 'sha256':
                $hash = new Hash('sha256');
                $base = base64_encode($hash->hash($RSAPublicKey));
                return substr($base, 0, strlen($base) - 1);
            case 'md5':
                return substr(chunk_split(md5($RSAPublicKey), 2, ':'), 0, -1);
            default:
                return false;
        }
    }
    /**
     * Returns the private key
     *
     * The private key is only returned if the currently loaded key contains the constituent prime numbers.
     *
     * @see getPublicKey()
     * @access public
     * @param String $key
     * @param Integer $type optional
     */
    function getPrivateKey($type = self::PUBLIC_FORMAT_PKCS1)
    {
        if (empty($this->primes)) {
            return false;
        }
        $oldFormat = $this->privateKeyFormat;
        $this->privateKeyFormat = $type;
        $temp = $this->_convertPrivateKey($this->modulus, $this->publicExponent, $this->exponent, $this->primes, $this->exponents, $this->coefficients);
        $this->privateKeyFormat = $oldFormat;
        return $temp;
    }
    /**
     * Returns a minimalistic private key
     *
     * Returns the private key without the prime number constituants.  Structurally identical to a public key that
     * hasn't been set as the public key
     *
     * @see getPrivateKey()
     * @access private
     * @param String $key
     * @param Integer $type optional
     */
    function _getPrivatePublicKey($mode = self::PUBLIC_FORMAT_PKCS8)
    {
        if (empty($this->modulus) || empty($this->exponent)) {
            return false;
        }
        $oldFormat = $this->publicKeyFormat;
        $this->publicKeyFormat = $mode;
        $temp = $this->_convertPublicKey($this->modulus, $this->exponent);
        $this->publicKeyFormat = $oldFormat;
        return $temp;
    }
    /**
     *  __toString() magic method
     *
     * @access public
     */
    function __toString()
    {
        $key = $this->getPrivateKey($this->privateKeyFormat);
        if ($key !== false) {
            return $key;
        }
        $key = $this->_getPrivatePublicKey($this->publicKeyFormat);
        return $key !== false ? $key : '';
    }
    /**
     *  __clone() magic method
     *
     * @access public
     */
    function __clone()
    {
        $key = new RSA();
        $key->loadKey($this);
        return $key;
    }
    /**
     * Generates the smallest and largest numbers requiring $bits bits
     *
     * @access private
     * @param Integer $bits
     * @return Array
     */
    function _generateMinMax($bits)
    {
        $bytes = $bits >> 3;
        $min = str_repeat(chr(0), $bytes);
        $max = str_repeat(chr(0xFF), $bytes);
        $msb = $bits & 7;
        if ($msb) {
            $min = chr(1 << ($msb - 1)) . $min;
            $max = chr((1 << $msb) - 1) . $max;
        } else {
            $min[0] = chr(0x80);
        }
        return array(
            'min' => new BigInteger($min, 256),
            'max' => new BigInteger($max, 256)
        );
    }
    /**
     * DER-decode the length
     *
     * DER supports lengths up to (2**8)**127, however, we'll only support lengths up to (2**8)**4.  See
     * {@link http://itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf#p=13 X.690 paragraph 8.1.3} for more information.
     *
     * @access private
     * @param String $string
     * @return Integer
     */
    function _decodeLength(&$string)
    {
        $length = ord($this->_string_shift($string));
        if ( $length & 0x80 ) { // definite length, long form
            $length&= 0x7F;
            $temp = $this->_string_shift($string, $length);
            list(, $length) = unpack('N', substr(str_pad($temp, 4, chr(0), STR_PAD_LEFT), -4));
        }
        return $length;
    }
    /**
     * DER-encode the length
     *
     * DER supports lengths up to (2**8)**127, however, we'll only support lengths up to (2**8)**4.  See
     * {@link http://itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf#p=13 X.690 paragraph 8.1.3} for more information.
     *
     * @access private
     * @param Integer $length
     * @return String
     */
    function _encodeLength($length)
    {
        if ($length <= 0x7F) {
            return chr($length);
        }
        $temp = ltrim(pack('N', $length), chr(0));
        return pack('Ca*', 0x80 | strlen($temp), $temp);
    }
    /**
     * String Shift
     *
     * Inspired by array_shift
     *
     * @param String $string
     * @param optional Integer $index
     * @return String
     * @access private
     */
    function _string_shift(&$string, $index = 1)
    {
        $substr = substr($string, 0, $index);
        $string = substr($string, $index);
        return $substr;
    }
    /**
     * Determines the private key format
     *
     * @see createKey()
     * @access public
     * @param Integer $format
     */
    function setPrivateKeyFormat($format)
    {
        $this->privateKeyFormat = $format;
    }
    /**
     * Determines the public key format
     *
     * @see createKey()
     * @access public
     * @param Integer $format
     */
    function setPublicKeyFormat($format)
    {
        $this->publicKeyFormat = $format;
    }
    /**
     * Determines which hashing function should be used
     *
     * Used with signature production / verification and (if the encryption mode is self::ENCRYPTION_OAEP) encryption and
     * decryption.  If $hash isn't supported, sha1 is used.
     *
     * @access public
     * @param String $hash
     */
    function setHash($hash)
    {
        // \phpseclib\Crypt\Hash supports algorithms that PKCS#1 doesn't support.  md5-96 and sha1-96, for example.
        switch ($hash) {
            case 'md2':
            case 'md5':
            case 'sha1':
            case 'sha256':
            case 'sha384':
            case 'sha512':
                $this->hash = new Hash($hash);
                $this->hashName = $hash;
                break;
            default:
                $this->hash = new Hash('sha1');
                $this->hashName = 'sha1';
        }
        $this->hLen = $this->hash->getLength();
    }
    /**
     * Determines which hashing function should be used for the mask generation function
     *
     * The mask generation function is used by self::ENCRYPTION_OAEP and self::SIGNATURE_PSS and although it's
     * best if Hash and MGFHash are set to the same thing this is not a requirement.
     *
     * @access public
     * @param String $hash
     */
    function setMGFHash($hash)
    {
        // \phpseclib\Crypt\Hash supports algorithms that PKCS#1 doesn't support.  md5-96 and sha1-96, for example.
        switch ($hash) {
            case 'md2':
            case 'md5':
            case 'sha1':
            case 'sha256':
            case 'sha384':
            case 'sha512':
                $this->mgfHash = new Hash($hash);
                break;
            default:
                $this->mgfHash = new Hash('sha1');
        }
        $this->mgfHLen = $this->mgfHash->getLength();
    }
    /**
     * Determines the salt length
     *
     * To quote from {@link http://tools.ietf.org/html/rfc3447#page-38 RFC3447#page-38}:
     *
     *    Typical salt lengths in octets are hLen (the length of the output
     *    of the hash function Hash) and 0.
     *
     * @access public
     * @param Integer $format
     */
    function setSaltLength($sLen)
    {
        $this->sLen = $sLen;
    }
    /**
     * Integer-to-Octet-String primitive
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-4.1 RFC3447#section-4.1}.
     *
     * @access private
     * @param \phpseclib\Math\BigInteger $x
     * @param Integer $xLen
     * @return String
     */
    function _i2osp($x, $xLen)
    {
        $x = $x->toBytes();
        if (strlen($x) > $xLen) {
            user_error('Integer too large');
            return false;
        }
        return str_pad($x, $xLen, chr(0), STR_PAD_LEFT);
    }
    /**
     * Octet-String-to-Integer primitive
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-4.2 RFC3447#section-4.2}.
     *
     * @access private
     * @param String $x
     * @return \phpseclib\Math\BigInteger
     */
    function _os2ip($x)
    {
        return new BigInteger($x, 256);
    }
    /**
     * Exponentiate with or without Chinese Remainder Theorem
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-5.1.1 RFC3447#section-5.1.2}.
     *
     * @access private
     * @param \phpseclib\Math\BigInteger $x
     * @return \phpseclib\Math\BigInteger
     */
    function _exponentiate($x)
    {
        if (empty($this->primes) || empty($this->coefficients) || empty($this->exponents)) {
            return $x->modPow($this->exponent, $this->modulus);
        }
        $num_primes = count($this->primes);
        if (defined('CRYPT_RSA_DISABLE_BLINDING')) {
            $m_i = array(
                1 => $x->modPow($this->exponents[1], $this->primes[1]),
                2 => $x->modPow($this->exponents[2], $this->primes[2])
            );
            $h = $m_i[1]->subtract($m_i[2]);
            $h = $h->multiply($this->coefficients[2]);
            list(, $h) = $h->divide($this->primes[1]);
            $m = $m_i[2]->add($h->multiply($this->primes[2]));
            $r = $this->primes[1];
            for ($i = 3; $i <= $num_primes; $i++) {
                $m_i = $x->modPow($this->exponents[$i], $this->primes[$i]);
                $r = $r->multiply($this->primes[$i - 1]);
                $h = $m_i->subtract($m);
                $h = $h->multiply($this->coefficients[$i]);
                list(, $h) = $h->divide($this->primes[$i]);
                $m = $m->add($r->multiply($h));
            }
        } else {
            $smallest = $this->primes[1];
            for ($i = 2; $i <= $num_primes; $i++) {
                if ($smallest->compare($this->primes[$i]) > 0) {
                    $smallest = $this->primes[$i];
                }
            }
            $one = new BigInteger(1);
            $r = $one->random($one, $smallest->subtract($one));
            $m_i = array(
                1 => $this->_blind($x, $r, 1),
                2 => $this->_blind($x, $r, 2)
            );
            $h = $m_i[1]->subtract($m_i[2]);
            $h = $h->multiply($this->coefficients[2]);
            list(, $h) = $h->divide($this->primes[1]);
            $m = $m_i[2]->add($h->multiply($this->primes[2]));
            $r = $this->primes[1];
            for ($i = 3; $i <= $num_primes; $i++) {
                $m_i = $this->_blind($x, $r, $i);
                $r = $r->multiply($this->primes[$i - 1]);
                $h = $m_i->subtract($m);
                $h = $h->multiply($this->coefficients[$i]);
                list(, $h) = $h->divide($this->primes[$i]);
                $m = $m->add($r->multiply($h));
            }
        }
        return $m;
    }
    /**
     * Performs RSA Blinding
     *
     * Protects against timing attacks by employing RSA Blinding.
     * Returns $x->modPow($this->exponents[$i], $this->primes[$i])
     *
     * @access private
     * @param \phpseclib\Math\BigInteger $x
     * @param \phpseclib\Math\BigInteger $r
     * @param Integer $i
     * @return \phpseclib\Math\BigInteger
     */
    function _blind($x, $r, $i)
    {
        $x = $x->multiply($r->modPow($this->publicExponent, $this->primes[$i]));
        $x = $x->modPow($this->exponents[$i], $this->primes[$i]);
        $r = $r->modInverse($this->primes[$i]);
        $x = $x->multiply($r);
        list(, $x) = $x->divide($this->primes[$i]);
        return $x;
    }
    /**
     * Performs blinded RSA equality testing
     *
     * Protects against a particular type of timing attack described.
     *
     * See {@link http://codahale.com/a-lesson-in-timing-attacks/ A Lesson In Timing Attacks (or, Don't use MessageDigest.isEquals)}
     *
     * Thanks for the heads up singpolyma!
     *
     * @access private
     * @param String $x
     * @param String $y
     * @return Boolean
     */
    function _equals($x, $y)
    {
        if (strlen($x) != strlen($y)) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < strlen($x); $i++) {
            $result |= ord($x[$i]) ^ ord($y[$i]);
        }
        return $result == 0;
    }
    /**
     * RSAEP
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-5.1.1 RFC3447#section-5.1.1}.
     *
     * @access private
     * @param \phpseclib\Math\BigInteger $m
     * @return \phpseclib\Math\BigInteger
     */
    function _rsaep($m)
    {
        if ($m->compare($this->zero) < 0 || $m->compare($this->modulus) > 0) {
            user_error('Message representative out of range');
            return false;
        }
        return $this->_exponentiate($m);
    }
    /**
     * RSADP
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-5.1.2 RFC3447#section-5.1.2}.
     *
     * @access private
     * @param \phpseclib\Math\BigInteger $c
     * @return \phpseclib\Math\BigInteger
     */
    function _rsadp($c)
    {
        if ($c->compare($this->zero) < 0 || $c->compare($this->modulus) > 0) {
            user_error('Ciphertext representative out of range');
            return false;
        }
        return $this->_exponentiate($c);
    }
    /**
     * RSASP1
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-5.2.1 RFC3447#section-5.2.1}.
     *
     * @access private
     * @param \phpseclib\Math\BigInteger $m
     * @return \phpseclib\Math\BigInteger
     */
    function _rsasp1($m)
    {
        if ($m->compare($this->zero) < 0 || $m->compare($this->modulus) > 0) {
            user_error('Message representative out of range');
            return false;
        }
        return $this->_exponentiate($m);
    }
    /**
     * RSAVP1
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-5.2.2 RFC3447#section-5.2.2}.
     *
     * @access private
     * @param \phpseclib\Math\BigInteger $s
     * @return \phpseclib\Math\BigInteger
     */
    function _rsavp1($s)
    {
        if ($s->compare($this->zero) < 0 || $s->compare($this->modulus) > 0) {
            user_error('Signature representative out of range');
            return false;
        }
        return $this->_exponentiate($s);
    }
    /**
     * MGF1
     *
     * See {@link http://tools.ietf.org/html/rfc3447#appendix-B.2.1 RFC3447#appendix-B.2.1}.
     *
     * @access private
     * @param String $mgfSeed
     * @param Integer $mgfLen
     * @return String
     */
    function _mgf1($mgfSeed, $maskLen)
    {
        // if $maskLen would yield strings larger than 4GB, PKCS#1 suggests a "Mask too long" error be output.
        $t = '';
        $count = ceil($maskLen / $this->mgfHLen);
        for ($i = 0; $i < $count; $i++) {
            $c = pack('N', $i);
            $t.= $this->mgfHash->hash($mgfSeed . $c);
        }
        return substr($t, 0, $maskLen);
    }
    /**
     * RSAES-OAEP-ENCRYPT
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-7.1.1 RFC3447#section-7.1.1} and
     * {http://en.wikipedia.org/wiki/Optimal_Asymmetric_Encryption_Padding OAES}.
     *
     * @access private
     * @param String $m
     * @param String $l
     * @return String
     */
    function _rsaes_oaep_encrypt($m, $l = '')
    {
        $mLen = strlen($m);
        // Length checking
        // if $l is larger than two million terrabytes and you're using sha1, PKCS#1 suggests a "Label too long" error
        // be output.
        if ($mLen > $this->k - 2 * $this->hLen - 2) {
            user_error('Message too long');
            return false;
        }
        // EME-OAEP encoding
        $lHash = $this->hash->hash($l);
        $ps = str_repeat(chr(0), $this->k - $mLen - 2 * $this->hLen - 2);
        $db = $lHash . $ps . chr(1) . $m;
        $seed = Random::string($this->hLen);
        $dbMask = $this->_mgf1($seed, $this->k - $this->hLen - 1);
        $maskedDB = $db ^ $dbMask;
        $seedMask = $this->_mgf1($maskedDB, $this->hLen);
        $maskedSeed = $seed ^ $seedMask;
        $em = chr(0) . $maskedSeed . $maskedDB;
        // RSA encryption
        $m = $this->_os2ip($em);
        $c = $this->_rsaep($m);
        $c = $this->_i2osp($c, $this->k);
        // Output the ciphertext C
        return $c;
    }
    /**
     * RSAES-OAEP-DECRYPT
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-7.1.2 RFC3447#section-7.1.2}.  The fact that the error
     * messages aren't distinguishable from one another hinders debugging, but, to quote from RFC3447#section-7.1.2:
     *
     *    Note.  Care must be taken to ensure that an opponent cannot
     *    distinguish the different error conditions in Step 3.g, whether by
     *    error message or timing, or, more generally, learn partial
     *    information about the encoded message EM.  Otherwise an opponent may
     *    be able to obtain useful information about the decryption of the
     *    ciphertext C, leading to a chosen-ciphertext attack such as the one
     *    observed by Manger [36].
     *
     * As for $l...  to quote from {@link http://tools.ietf.org/html/rfc3447#page-17 RFC3447#page-17}:
     *
     *    Both the encryption and the decryption operations of RSAES-OAEP take
     *    the value of a label L as input.  In this version of PKCS #1, L is
     *    the empty string; other uses of the label are outside the scope of
     *    this document.
     *
     * @access private
     * @param String $c
     * @param String $l
     * @return String
     */
    function _rsaes_oaep_decrypt($c, $l = '')
    {
        // Length checking
        // if $l is larger than two million terrabytes and you're using sha1, PKCS#1 suggests a "Label too long" error
        // be output.
        if (strlen($c) != $this->k || $this->k < 2 * $this->hLen + 2) {
            user_error('Decryption error');
            return false;
        }
        // RSA decryption
        $c = $this->_os2ip($c);
        $m = $this->_rsadp($c);
        if ($m === false) {
            user_error('Decryption error');
            return false;
        }
        $em = $this->_i2osp($m, $this->k);
        // EME-OAEP decoding
        $lHash = $this->hash->hash($l);
        $y = ord($em[0]);
        $maskedSeed = substr($em, 1, $this->hLen);
        $maskedDB = substr($em, $this->hLen + 1);
        $seedMask = $this->_mgf1($maskedDB, $this->hLen);
        $seed = $maskedSeed ^ $seedMask;
        $dbMask = $this->_mgf1($seed, $this->k - $this->hLen - 1);
        $db = $maskedDB ^ $dbMask;
        $lHash2 = substr($db, 0, $this->hLen);
        $m = substr($db, $this->hLen);
        if ($lHash != $lHash2) {
            user_error('Decryption error');
            return false;
        }
        $m = ltrim($m, chr(0));
        if (ord($m[0]) != 1) {
            user_error('Decryption error');
            return false;
        }
        // Output the message M
        return substr($m, 1);
    }
    /**
     * Raw Encryption / Decryption
     *
     * Doesn't use padding and is not recommended.
     *
     * @access private
     * @param String $m
     * @return String
     */
    function _raw_encrypt($m)
    {
        $temp = $this->_os2ip($m);
        $temp = $this->_rsaep($temp);
        return  $this->_i2osp($temp, $this->k);
    }
    /**
     * RSAES-PKCS1-V1_5-ENCRYPT
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-7.2.1 RFC3447#section-7.2.1}.
     *
     * @access private
     * @param String $m
     * @return String
     */
    function _rsaes_pkcs1_v1_5_encrypt($m)
    {
        $mLen = strlen($m);
        // Length checking
        if ($mLen > $this->k - 11) {
            user_error('Message too long');
            return false;
        }
        // EME-PKCS1-v1_5 encoding
        $psLen = $this->k - $mLen - 3;
        $ps = '';
        while (strlen($ps) != $psLen) {
            $temp = Random::string($psLen - strlen($ps));
            $temp = str_replace("\x00", '', $temp);
            $ps.= $temp;
        }
        $type = 2;
        // see the comments of _rsaes_pkcs1_v1_5_decrypt() to understand why this is being done
        if (defined('CRYPT_RSA_PKCS15_COMPAT') && (!isset($this->publicExponent) || $this->exponent !== $this->publicExponent)) {
            $type = 1;
            // "The padding string PS shall consist of k-3-||D|| octets. ... for block type 01, they shall have value FF"
            $ps = str_repeat("\xFF", $psLen);
        }
        $em = chr(0) . chr($type) . $ps . chr(0) . $m;
        // RSA encryption
        $m = $this->_os2ip($em);
        $c = $this->_rsaep($m);
        $c = $this->_i2osp($c, $this->k);
        // Output the ciphertext C
        return $c;
    }
    /**
     * RSAES-PKCS1-V1_5-DECRYPT
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-7.2.2 RFC3447#section-7.2.2}.
     *
     * For compatibility purposes, this function departs slightly from the description given in RFC3447.
     * The reason being that RFC2313#section-8.1 (PKCS#1 v1.5) states that ciphertext's encrypted by the
     * private key should have the second byte set to either 0 or 1 and that ciphertext's encrypted by the
     * public key should have the second byte set to 2.  In RFC3447 (PKCS#1 v2.1), the second byte is supposed
     * to be 2 regardless of which key is used.  For compatibility purposes, we'll just check to make sure the
     * second byte is 2 or less.  If it is, we'll accept the decrypted string as valid.
     *
     * As a consequence of this, a private key encrypted ciphertext produced with \phpseclib\Crypt\RSA may not decrypt
     * with a strictly PKCS#1 v1.5 compliant RSA implementation.  Public key encrypted ciphertext's should but
     * not private key encrypted ciphertext's.
     *
     * @access private
     * @param String $c
     * @return String
     */
    function _rsaes_pkcs1_v1_5_decrypt($c)
    {
        // Length checking
        if (strlen($c) != $this->k) { // or if k < 11
            user_error('Decryption error');
            return false;
        }
        // RSA decryption
        $c = $this->_os2ip($c);
        $m = $this->_rsadp($c);
        if ($m === false) {
            user_error('Decryption error');
            return false;
        }
        $em = $this->_i2osp($m, $this->k);
        // EME-PKCS1-v1_5 decoding
        if (ord($em[0]) != 0 || ord($em[1]) > 2) {
            user_error('Decryption error');
            return false;
        }
        $ps = substr($em, 2, strpos($em, chr(0), 2) - 2);
        $m = substr($em, strlen($ps) + 3);
        if (strlen($ps) < 8) {
            user_error('Decryption error');
            return false;
        }
        // Output M
        return $m;
    }
    /**
     * EMSA-PSS-ENCODE
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-9.1.1 RFC3447#section-9.1.1}.
     *
     * @access private
     * @param String $m
     * @param Integer $emBits
     */
    function _emsa_pss_encode($m, $emBits)
    {
        // if $m is larger than two million terrabytes and you're using sha1, PKCS#1 suggests a "Label too long" error
        // be output.
        $emLen = ($emBits + 1) >> 3; // ie. ceil($emBits / 8)
        $sLen = $this->sLen === false ? $this->hLen : $this->sLen;
        $mHash = $this->hash->hash($m);
        if ($emLen < $this->hLen + $sLen + 2) {
            user_error('Encoding error');
            return false;
        }
        $salt = Random::string($sLen);
        $m2 = "\0\0\0\0\0\0\0\0" . $mHash . $salt;
        $h = $this->hash->hash($m2);
        $ps = str_repeat(chr(0), $emLen - $sLen - $this->hLen - 2);
        $db = $ps . chr(1) . $salt;
        $dbMask = $this->_mgf1($h, $emLen - $this->hLen - 1);
        $maskedDB = $db ^ $dbMask;
        $maskedDB[0] = ~chr(0xFF << ($emBits & 7)) & $maskedDB[0];
        $em = $maskedDB . $h . chr(0xBC);
        return $em;
    }
    /**
     * EMSA-PSS-VERIFY
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-9.1.2 RFC3447#section-9.1.2}.
     *
     * @access private
     * @param String $m
     * @param String $em
     * @param Integer $emBits
     * @return String
     */
    function _emsa_pss_verify($m, $em, $emBits)
    {
        // if $m is larger than two million terrabytes and you're using sha1, PKCS#1 suggests a "Label too long" error
        // be output.
        $emLen = ($emBits + 1) >> 3; // ie. ceil($emBits / 8);
        $sLen = $this->sLen === false ? $this->hLen : $this->sLen;
        $mHash = $this->hash->hash($m);
        if ($emLen < $this->hLen + $sLen + 2) {
            return false;
        }
        if ($em[strlen($em) - 1] != chr(0xBC)) {
            return false;
        }
        $maskedDB = substr($em, 0, -$this->hLen - 1);
        $h = substr($em, -$this->hLen - 1, $this->hLen);
        $temp = chr(0xFF << ($emBits & 7));
        if ((~$maskedDB[0] & $temp) != $temp) {
            return false;
        }
        $dbMask = $this->_mgf1($h, $emLen - $this->hLen - 1);
        $db = $maskedDB ^ $dbMask;
        $db[0] = ~chr(0xFF << ($emBits & 7)) & $db[0];
        $temp = $emLen - $this->hLen - $sLen - 2;
        if (substr($db, 0, $temp) != str_repeat(chr(0), $temp) || ord($db[$temp]) != 1) {
            return false;
        }
        $salt = substr($db, $temp + 1); // should be $sLen long
        $m2 = "\0\0\0\0\0\0\0\0" . $mHash . $salt;
        $h2 = $this->hash->hash($m2);
        return $this->_equals($h, $h2);
    }
    /**
     * RSASSA-PSS-SIGN
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-8.1.1 RFC3447#section-8.1.1}.
     *
     * @access private
     * @param String $m
     * @return String
     */
    function _rsassa_pss_sign($m)
    {
        // EMSA-PSS encoding
        $em = $this->_emsa_pss_encode($m, 8 * $this->k - 1);
        // RSA signature
        $m = $this->_os2ip($em);
        $s = $this->_rsasp1($m);
        $s = $this->_i2osp($s, $this->k);
        // Output the signature S
        return $s;
    }
    /**
     * RSASSA-PSS-VERIFY
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-8.1.2 RFC3447#section-8.1.2}.
     *
     * @access private
     * @param String $m
     * @param String $s
     * @return String
     */
    function _rsassa_pss_verify($m, $s)
    {
        // Length checking
        if (strlen($s) != $this->k) {
            user_error('Invalid signature');
            return false;
        }
        // RSA verification
        $modBits = 8 * $this->k;
        $s2 = $this->_os2ip($s);
        $m2 = $this->_rsavp1($s2);
        if ($m2 === false) {
            user_error('Invalid signature');
            return false;
        }
        $em = $this->_i2osp($m2, $modBits >> 3);
        if ($em === false) {
            user_error('Invalid signature');
            return false;
        }
        // EMSA-PSS verification
        return $this->_emsa_pss_verify($m, $em, $modBits - 1);
    }
    /**
     * EMSA-PKCS1-V1_5-ENCODE
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-9.2 RFC3447#section-9.2}.
     *
     * @access private
     * @param String $m
     * @param Integer $emLen
     * @return String
     */
    function _emsa_pkcs1_v1_5_encode($m, $emLen)
    {
        $h = $this->hash->hash($m);
        if ($h === false) {
            return false;
        }
        // see http://tools.ietf.org/html/rfc3447#page-43
        switch ($this->hashName) {
            case 'md2':
                $t = pack('H*', '3020300c06082a864886f70d020205000410');
                break;
            case 'md5':
                $t = pack('H*', '3020300c06082a864886f70d020505000410');
                break;
            case 'sha1':
                $t = pack('H*', '3021300906052b0e03021a05000414');
                break;
            case 'sha256':
                $t = pack('H*', '3031300d060960864801650304020105000420');
                break;
            case 'sha384':
                $t = pack('H*', '3041300d060960864801650304020205000430');
                break;
            case 'sha512':
                $t = pack('H*', '3051300d060960864801650304020305000440');
        }
        $t.= $h;
        $tLen = strlen($t);
        if ($emLen < $tLen + 11) {
            user_error('Intended encoded message length too short');
            return false;
        }
        $ps = str_repeat(chr(0xFF), $emLen - $tLen - 3);
        $em = "\0\1$ps\0$t";
        return $em;
    }
    /**
     * RSASSA-PKCS1-V1_5-SIGN
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-8.2.1 RFC3447#section-8.2.1}.
     *
     * @access private
     * @param String $m
     * @return String
     */
    function _rsassa_pkcs1_v1_5_sign($m)
    {
        // EMSA-PKCS1-v1_5 encoding
        $em = $this->_emsa_pkcs1_v1_5_encode($m, $this->k);
        if ($em === false) {
            user_error('RSA modulus too short');
            return false;
        }
        // RSA signature
        $m = $this->_os2ip($em);
        $s = $this->_rsasp1($m);
        $s = $this->_i2osp($s, $this->k);
        // Output the signature S
        return $s;
    }
    /**
     * RSASSA-PKCS1-V1_5-VERIFY
     *
     * See {@link http://tools.ietf.org/html/rfc3447#section-8.2.2 RFC3447#section-8.2.2}.
     *
     * @access private
     * @param String $m
     * @return String
     */
    function _rsassa_pkcs1_v1_5_verify($m, $s)
    {
        // Length checking
        if (strlen($s) != $this->k) {
            user_error('Invalid signature');
            return false;
        }
        // RSA verification
        $s = $this->_os2ip($s);
        $m2 = $this->_rsavp1($s);
        if ($m2 === false) {
            user_error('Invalid signature');
            return false;
        }
        $em = $this->_i2osp($m2, $this->k);
        if ($em === false) {
            user_error('Invalid signature');
            return false;
        }
        // EMSA-PKCS1-v1_5 encoding
        $em2 = $this->_emsa_pkcs1_v1_5_encode($m, $this->k);
        if ($em2 === false) {
            user_error('RSA modulus too short');
            return false;
        }
        // Compare
        return $this->_equals($em, $em2);
    }
    /**
     * Set Encryption Mode
     *
     * Valid values include self::ENCRYPTION_OAEP and self::ENCRYPTION_PKCS1.
     *
     * @access public
     * @param Integer $mode
     */
    function setEncryptionMode($mode)
    {
        $this->encryptionMode = $mode;
    }
    /**
     * Set Signature Mode
     *
     * Valid values include self::SIGNATURE_PSS and self::SIGNATURE_PKCS1
     *
     * @access public
     * @param Integer $mode
     */
    function setSignatureMode($mode)
    {
        $this->signatureMode = $mode;
    }
    /**
     * Set public key comment.
     *
     * @access public
     * @param String $comment
     */
    function setComment($comment)
    {
        $this->comment = $comment;
    }
    /**
     * Get public key comment.
     *
     * @access public
     * @return String
     */
    function getComment()
    {
        return $this->comment;
    }
    /**
     * Encryption
     *
     * Both self::ENCRYPTION_OAEP and self::ENCRYPTION_PKCS1 both place limits on how long $plaintext can be.
     * If $plaintext exceeds those limits it will be broken up so that it does and the resultant ciphertext's will
     * be concatenated together.
     *
     * @see decrypt()
     * @access public
     * @param String $plaintext
     * @return String
     */
    function encrypt($plaintext)
    {
        switch ($this->encryptionMode) {
            case self::ENCRYPTION_NONE:
                $plaintext = str_split($plaintext, $this->k);
                $ciphertext = '';
                foreach ($plaintext as $m) {
                    $ciphertext.= $this->_raw_encrypt($m);
                }
                return $ciphertext;
            case self::ENCRYPTION_PKCS1:
                $length = $this->k - 11;
                if ($length <= 0) {
                    return false;
                }
                $plaintext = str_split($plaintext, $length);
                $ciphertext = '';
                foreach ($plaintext as $m) {
                    $ciphertext.= $this->_rsaes_pkcs1_v1_5_encrypt($m);
                }
                return $ciphertext;
            //case self::ENCRYPTION_OAEP:
            default:
                $length = $this->k - 2 * $this->hLen - 2;
                if ($length <= 0) {
                    return false;
                }
                $plaintext = str_split($plaintext, $length);
                $ciphertext = '';
                foreach ($plaintext as $m) {
                    $ciphertext.= $this->_rsaes_oaep_encrypt($m);
                }
                return $ciphertext;
        }
    }
    /**
     * Decryption
     *
     * @see encrypt()
     * @access public
     * @param String $plaintext
     * @return String
     */
    function decrypt($ciphertext)
    {
        if ($this->k <= 0) {
            return false;
        }
        $ciphertext = str_split($ciphertext, $this->k);
        $ciphertext[count($ciphertext) - 1] = str_pad($ciphertext[count($ciphertext) - 1], $this->k, chr(0), STR_PAD_LEFT);
        $plaintext = '';
        switch ($this->encryptionMode) {
            case self::ENCRYPTION_NONE:
                $decrypt = '_raw_encrypt';
                break;
            case self::ENCRYPTION_PKCS1:
                $decrypt = '_rsaes_pkcs1_v1_5_decrypt';
                break;
            //case self::ENCRYPTION_OAEP:
            default:
                $decrypt = '_rsaes_oaep_decrypt';
        }
        foreach ($ciphertext as $c) {
            $temp = $this->$decrypt($c);
            if ($temp === false) {
                return false;
            }
            $plaintext.= $temp;
        }
        return $plaintext;
    }
    /**
     * Create a signature
     *
     * @see verify()
     * @access public
     * @param String $message
     * @return String
     */
    function sign($message)
    {
        if (empty($this->modulus) || empty($this->exponent)) {
            return false;
        }
        switch ($this->signatureMode) {
            case self::SIGNATURE_PKCS1:
                return $this->_rsassa_pkcs1_v1_5_sign($message);
            //case self::SIGNATURE_PSS:
            default:
                return $this->_rsassa_pss_sign($message);
        }
    }
    /**
     * Verifies a signature
     *
     * @see sign()
     * @access public
     * @param String $message
     * @param String $signature
     * @return Boolean
     */
    function verify($message, $signature)
    {
        if (empty($this->modulus) || empty($this->exponent)) {
            return false;
        }
        switch ($this->signatureMode) {
            case self::SIGNATURE_PKCS1:
                return $this->_rsassa_pkcs1_v1_5_verify($message, $signature);
            //case self::SIGNATURE_PSS:
            default:
                return $this->_rsassa_pss_verify($message, $signature);
        }
    }
    /**
     * Extract raw BER from Base64 encoding
     *
     * @access private
     * @param String $str
     * @return String
     */
    function _extractBER($str)
    {
        /* X.509 certs are assumed to be base64 encoded but sometimes they'll have additional things in them
         * above and beyond the ceritificate.
         * ie. some may have the following preceding the -----BEGIN CERTIFICATE----- line:
         *
         * Bag Attributes
         *     localKeyID: 01 00 00 00
         * subject=/O=organization/OU=org unit/CN=common name
         * issuer=/O=organization/CN=common name
         */
        $temp = preg_replace('#.*?^-+[^-]+-+#ms', '', $str, 1);
        // remove the -----BEGIN CERTIFICATE----- and -----END CERTIFICATE----- stuff
        $temp = preg_replace('#-+[^-]+-+#', '', $temp);
        // remove new lines
        $temp = str_replace(array("\r", "\n", ' '), '', $temp);
        $temp = preg_match('#^[a-zA-Z\d/+]*={0,2}$#', $temp) ? base64_decode($temp) : false;
        return $temp != false ? $temp : $str;
    }
}




class BigInteger
{
    /**#@+
     * Reduction constants
     *
     * @access private
     * @see BigInteger::_reduce()
     */
    /**
     * @see BigInteger::_montgomery()
     * @see BigInteger::_prepMontgomery()
     */
    const MONTGOMERY = 0;
    /**
     * @see BigInteger::_barrett()
    */
    const BARRETT = 1;
    /**
     * @see BigInteger::_mod2()
    */
    const POWEROF2 = 2;
    /**
     * @see BigInteger::_remainder()
    */
    const CLASSIC = 3;
    /**
     * @see BigInteger::__clone()
    */
    const NONE = 4;
    /**#@-*/
    /**#@+
     * Array constants
     *
     * Rather than create a thousands and thousands of new BigInteger objects in repeated function calls to add() and
     * multiply() or whatever, we'll just work directly on arrays, taking them in as parameters and returning them.
     *
     * @access private
    */
    /**
     * $result[self::VALUE] contains the value.
    */
    const VALUE = 0;
    /**
     * $result[self::SIGN] contains the sign.
    */
    const SIGN = 1;
    /**#@-*/
    /**#@+
     * @access private
     * @see BigInteger::_montgomery()
     * @see BigInteger::_barrett()
    */
    /**
     * Cache constants
     *
     * $cache[self::VARIABLE] tells us whether or not the cached data is still valid.
    */
    const VARIABLE = 0;
    /**
     * $cache[self::DATA] contains the cached data.
    */
    const DATA = 1;
    /**#@-*/
    /**#@+
     * Mode constants.
     *
     * @access private
     * @see BigInteger::__construct()
    */
    /**
     * To use the pure-PHP implementation
    */
    const MODE_INTERNAL = 1;
    /**
     * To use the BCMath library
     *
     * (if enabled; otherwise, the internal implementation will be used)
    */
    const MODE_BCMATH = 2;
    /**
     * To use the GMP library
     *
     * (if present; otherwise, either the BCMath or the internal implementation will be used)
    */
    const MODE_GMP = 3;
    /**#@-*/
    /**
     * Karatsuba Cutoff
     *
     * At what point do we switch between Karatsuba multiplication and schoolbook long multiplication?
     *
     * @access private
    */
    const KARATSUBA_CUTOFF = 25;
    /**#@+
     * Static properties used by the pure-PHP implementation.
     *
     * @see __construct()
     */
    protected static $base;
    protected static $baseFull;
    protected static $maxDigit;
    protected static $msb;
    /**
     * $max10 in greatest $max10Len satisfying
     * $max10 = 10**$max10Len <= 2**$base.
    */
    protected static $max10;
    /**
     * $max10Len in greatest $max10Len satisfying
     * $max10 = 10**$max10Len <= 2**$base.
    */
    protected static $max10Len;
    protected static $maxDigit2;
    /**#@-*/
    /**
     * Holds the BigInteger's value.
     *
     * @var Array
     * @access private
     */
    var $value;
    /**
     * Holds the BigInteger's magnitude.
     *
     * @var Boolean
     * @access private
     */
    var $is_negative = false;
    /**
     * Random number generator function
     *
     * @access private
     */
    var $generator = 'mt_rand';
    /**
     * Precision
     *
     * @see setPrecision()
     * @access private
     */
    var $precision = -1;
    /**
     * Precision Bitmask
     *
     * @see setPrecision()
     * @access private
     */
    var $bitmask = false;
    /**
     * Mode independent value used for serialization.
     *
     * If the bcmath or gmp extensions are installed $this->value will be a non-serializable resource, hence the need for
     * a variable that'll be serializable regardless of whether or not extensions are being used.  Unlike $this->value,
     * however, $this->hex is only calculated when $this->__sleep() is called.
     *
     * @see __sleep()
     * @see __wakeup()
     * @var String
     * @access private
     */
    var $hex;
    /**
     * Converts base-2, base-10, base-16, and binary strings (base-256) to BigIntegers.
     *
     * If the second parameter - $base - is negative, then it will be assumed that the number's are encoded using
     * two's compliment.  The sole exception to this is -10, which is treated the same as 10 is.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger('0x32', 16); // 50 in base-16
     *
     *    echo $a->toString(); // outputs 50
     * ?>
     * </code>
     *
     * @param optional $x base-10 number or base-$base number if $base set.
     * @param optional integer $base
     * @return \phpseclib\Math\BigInteger
     * @access public
     */
    function __construct($x = 0, $base = 10)
    {
        if ( !defined('MATH_BIGINTEGER_MODE') ) {
            switch (true) {
                case extension_loaded('gmp'):
                    define('MATH_BIGINTEGER_MODE', self::MODE_GMP);
                    break;
                case extension_loaded('bcmath'):
                    define('MATH_BIGINTEGER_MODE', self::MODE_BCMATH);
                    break;
                default:
                    define('MATH_BIGINTEGER_MODE', self::MODE_INTERNAL);
            }
        }
        if (function_exists('openssl_public_encrypt') && !defined('MATH_BIGINTEGER_OPENSSL_DISABLE') && !defined('MATH_BIGINTEGER_OPENSSL_ENABLED')) {
            // some versions of XAMPP have mismatched versions of OpenSSL which causes it not to work
            ob_start();
            @phpinfo();
            $content = ob_get_contents();
            ob_end_clean();
            preg_match_all('#OpenSSL (Header|Library) Version(.*)#im', $content, $matches);
            $versions = array();
            if (!empty($matches[1])) {
                for ($i = 0; $i < count($matches[1]); $i++) {
                    $fullVersion = trim(str_replace('=>', '', strip_tags($matches[2][$i])));
                    // Remove letter part in OpenSSL version
                    if (!preg_match('/(\d+\.\d+\.\d+)/i', $fullVersion, $m)) {
                        $versions[$matches[1][$i]] = $fullVersion;
                    } else {
                        $versions[$matches[1][$i]] = $m[0];
                    }
                }
            }
            // it doesn't appear that OpenSSL versions were reported upon until PHP 5.3+
            switch (true) {
                case !isset($versions['Header']):
                case !isset($versions['Library']):
                case $versions['Header'] == $versions['Library']:
                    define('MATH_BIGINTEGER_OPENSSL_ENABLED', true);
                    break;
                default:
                    define('MATH_BIGINTEGER_OPENSSL_DISABLE', true);
            }
        }
        if (!defined('PHP_INT_SIZE')) {
            define('PHP_INT_SIZE', 4);
        }
        if (empty(self::$base) && MATH_BIGINTEGER_MODE == self::MODE_INTERNAL) {
            switch (PHP_INT_SIZE) {
                case 8: // use 64-bit integers if int size is 8 bytes
                    self::$base      = 31;
                    self::$baseFull  = 0x80000000;
                    self::$maxDigit  = 0x7FFFFFFF;
                    self::$msb       = 0x40000000;
                    self::$max10     = 1000000000;
                    self::$max10Len  = 9;
                    self::$maxDigit2 = pow(2, 62);
                    break;
                //case 4: // use 64-bit floats if int size is 4 bytes
                default:
                    self::$base      = 26;
                    self::$baseFull  = 0x4000000;
                    self::$maxDigit  = 0x3FFFFFF;
                    self::$msb       = 0x2000000;
                    self::$max10     = 10000000;
                    self::$max10Len  = 7;
                    self::$maxDigit2 = pow(2, 52); // pow() prevents truncation
                    break;
            }
        }
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                switch (true) {
                    case is_resource($x) && get_resource_type($x) == 'GMP integer':
                    // PHP 5.6 switched GMP from using resources to objects
                    case $x instanceof \GMP:
                        $this->value = $x;
                        return;
                }
                $this->value = gmp_init(0);
                break;
            case self::MODE_BCMATH:
                $this->value = '0';
                break;
            default:
                $this->value = array();
        }
        // '0' counts as empty() but when the base is 256 '0' is equal to ord('0') or 48
        // '0' is the only value like this per http://php.net/empty
        if (empty($x) && (abs($base) != 256 || $x !== '0')) {
            return;
        }
        switch ($base) {
            case -256:
                if (ord($x[0]) & 0x80) {
                    $x = ~$x;
                    $this->is_negative = true;
                }
            case  256:
                switch ( MATH_BIGINTEGER_MODE ) {
                    case self::MODE_GMP:
                        $sign = $this->is_negative ? '-' : '';
                        $this->value = gmp_init($sign . '0x' . bin2hex($x));
                        break;
                    case self::MODE_BCMATH:
                        // round $len to the nearest 4 (thanks, DavidMJ!)
                        $len = (strlen($x) + 3) & 0xFFFFFFFC;
                        $x = str_pad($x, $len, chr(0), STR_PAD_LEFT);
                        for ($i = 0; $i < $len; $i+= 4) {
                            $this->value = bcmul($this->value, '4294967296', 0); // 4294967296 == 2**32
                            $this->value = bcadd($this->value, 0x1000000 * ord($x[$i]) + ((ord($x[$i + 1]) << 16) | (ord($x[$i + 2]) << 8) | ord($x[$i + 3])), 0);
                        }
                        if ($this->is_negative) {
                            $this->value = '-' . $this->value;
                        }
                        break;
                    // converts a base-2**8 (big endian / msb) number to base-2**26 (little endian / lsb)
                    default:
                        while (strlen($x)) {
                            $this->value[] = $this->_bytes2int($this->_base256_rshift($x, self::$base));
                        }
                }
                if ($this->is_negative) {
                    if (MATH_BIGINTEGER_MODE != self::MODE_INTERNAL) {
                        $this->is_negative = false;
                    }
                    $temp = $this->add(new static('-1'));
                    $this->value = $temp->value;
                }
                break;
            case  16:
            case -16:
                if ($base > 0 && $x[0] == '-') {
                    $this->is_negative = true;
                    $x = substr($x, 1);
                }
                $x = preg_replace('#^(?:0x)?([A-Fa-f0-9]*).*#', '$1', $x);
                $is_negative = false;
                if ($base < 0 && hexdec($x[0]) >= 8) {
                    $this->is_negative = $is_negative = true;
                    $x = bin2hex(~pack('H*', $x));
                }
                switch ( MATH_BIGINTEGER_MODE ) {
                    case self::MODE_GMP:
                        $temp = $this->is_negative ? '-0x' . $x : '0x' . $x;
                        $this->value = gmp_init($temp);
                        $this->is_negative = false;
                        break;
                    case self::MODE_BCMATH:
                        $x = ( strlen($x) & 1 ) ? '0' . $x : $x;
                        $temp = new static(pack('H*', $x), 256);
                        $this->value = $this->is_negative ? '-' . $temp->value : $temp->value;
                        $this->is_negative = false;
                        break;
                    default:
                        $x = ( strlen($x) & 1 ) ? '0' . $x : $x;
                        $temp = new static(pack('H*', $x), 256);
                        $this->value = $temp->value;
                }
                if ($is_negative) {
                    $temp = $this->add(new static('-1'));
                    $this->value = $temp->value;
                }
                break;
            case  10:
            case -10:
                // (?<!^)(?:-).*: find any -'s that aren't at the beginning and then any characters that follow that
                // (?<=^|-)0*: find any 0's that are preceded by the start of the string or by a - (ie. octals)
                // [^-0-9].*: find any non-numeric characters and then any characters that follow that
                $x = preg_replace('#(?<!^)(?:-).*|(?<=^|-)0*|[^-0-9].*#', '', $x);
                switch ( MATH_BIGINTEGER_MODE ) {
                    case self::MODE_GMP:
                        $this->value = gmp_init($x);
                        break;
                    case self::MODE_BCMATH:
                        // explicitly casting $x to a string is necessary, here, since doing $x[0] on -1 yields different
                        // results then doing it on '-1' does (modInverse does $x[0])
                        $this->value = $x === '-' ? '0' : (string) $x;
                        break;
                    default:
                        $temp = new static();
                        $multiplier = new static();
                        $multiplier->value = array(self::$max10);
                        if ($x[0] == '-') {
                            $this->is_negative = true;
                            $x = substr($x, 1);
                        }
                        $x = str_pad($x, strlen($x) + ((self::$max10Len - 1) * strlen($x)) % self::$max10Len, 0, STR_PAD_LEFT);
                        while (strlen($x)) {
                            $temp = $temp->multiply($multiplier);
                            $temp = $temp->add(new static($this->_int2bytes(substr($x, 0, self::$max10Len)), 256));
                            $x = substr($x, self::$max10Len);
                        }
                        $this->value = $temp->value;
                }
                break;
            case  2: // base-2 support originally implemented by Lluis Pamies - thanks!
            case -2:
                if ($base > 0 && $x[0] == '-') {
                    $this->is_negative = true;
                    $x = substr($x, 1);
                }
                $x = preg_replace('#^([01]*).*#', '$1', $x);
                $x = str_pad($x, strlen($x) + (3 * strlen($x)) % 4, 0, STR_PAD_LEFT);
                $str = '0x';
                while (strlen($x)) {
                    $part = substr($x, 0, 4);
                    $str.= dechex(bindec($part));
                    $x = substr($x, 4);
                }
                if ($this->is_negative) {
                    $str = '-' . $str;
                }
                $temp = new static($str, 8 * $base); // ie. either -16 or +16
                $this->value = $temp->value;
                $this->is_negative = $temp->is_negative;
                break;
            default:
                // base not supported, so we'll let $this == 0
        }
    }
    /**
     * Converts a BigInteger to a byte string (eg. base-256).
     *
     * Negative numbers are saved as positive numbers, unless $twos_compliment is set to true, at which point, they're
     * saved as two's compliment.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger('65');
     *
     *    echo $a->toBytes(); // outputs chr(65)
     * ?>
     * </code>
     *
     * @param Boolean $twos_compliment
     * @return String
     * @access public
     * @internal Converts a base-2**26 number to base-2**8
     */
    function toBytes($twos_compliment = false)
    {
        if ($twos_compliment) {
            $comparison = $this->compare(new static());
            if ($comparison == 0) {
                return $this->precision > 0 ? str_repeat(chr(0), ($this->precision + 1) >> 3) : '';
            }
            $temp = $comparison < 0 ? $this->add(new static(1)) : $this->copy();
            $bytes = $temp->toBytes();
            if (empty($bytes)) { // eg. if the number we're trying to convert is -1
                $bytes = chr(0);
            }
            if (ord($bytes[0]) & 0x80) {
                $bytes = chr(0) . $bytes;
            }
            return $comparison < 0 ? ~$bytes : $bytes;
        }
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                if (gmp_cmp($this->value, gmp_init(0)) == 0) {
                    return $this->precision > 0 ? str_repeat(chr(0), ($this->precision + 1) >> 3) : '';
                }
                $temp = gmp_strval(gmp_abs($this->value), 16);
                $temp = ( strlen($temp) & 1 ) ? '0' . $temp : $temp;
                $temp = pack('H*', $temp);
                return $this->precision > 0 ?
                    substr(str_pad($temp, $this->precision >> 3, chr(0), STR_PAD_LEFT), -($this->precision >> 3)) :
                    ltrim($temp, chr(0));
            case self::MODE_BCMATH:
                if ($this->value === '0') {
                    return $this->precision > 0 ? str_repeat(chr(0), ($this->precision + 1) >> 3) : '';
                }
                $value = '';
                $current = $this->value;
                if ($current[0] == '-') {
                    $current = substr($current, 1);
                }
                while (bccomp($current, '0', 0) > 0) {
                    $temp = bcmod($current, '16777216');
                    $value = chr($temp >> 16) . chr($temp >> 8) . chr($temp) . $value;
                    $current = bcdiv($current, '16777216', 0);
                }
                return $this->precision > 0 ?
                    substr(str_pad($value, $this->precision >> 3, chr(0), STR_PAD_LEFT), -($this->precision >> 3)) :
                    ltrim($value, chr(0));
        }
        if (!count($this->value)) {
            return $this->precision > 0 ? str_repeat(chr(0), ($this->precision + 1) >> 3) : '';
        }
        $result = $this->_int2bytes($this->value[count($this->value) - 1]);
        $temp = $this->copy();
        for ($i = count($temp->value) - 2; $i >= 0; --$i) {
            $temp->_base256_lshift($result, self::$base);
            $result = $result | str_pad($temp->_int2bytes($temp->value[$i]), strlen($result), chr(0), STR_PAD_LEFT);
        }
        return $this->precision > 0 ?
            str_pad(substr($result, -(($this->precision + 7) >> 3)), ($this->precision + 7) >> 3, chr(0), STR_PAD_LEFT) :
            $result;
    }
    /**
     * Converts a BigInteger to a hex string (eg. base-16)).
     *
     * Negative numbers are saved as positive numbers, unless $twos_compliment is set to true, at which point, they're
     * saved as two's compliment.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger('65');
     *
     *    echo $a->toHex(); // outputs '41'
     * ?>
     * </code>
     *
     * @param Boolean $twos_compliment
     * @return String
     * @access public
     * @internal Converts a base-2**26 number to base-2**8
     */
    function toHex($twos_compliment = false)
    {
        return bin2hex($this->toBytes($twos_compliment));
    }
    /**
     * Converts a BigInteger to a bit string (eg. base-2).
     *
     * Negative numbers are saved as positive numbers, unless $twos_compliment is set to true, at which point, they're
     * saved as two's compliment.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger('65');
     *
     *    echo $a->toBits(); // outputs '1000001'
     * ?>
     * </code>
     *
     * @param Boolean $twos_compliment
     * @return String
     * @access public
     * @internal Converts a base-2**26 number to base-2**2
     */
    function toBits($twos_compliment = false)
    {
        $hex = $this->toHex($twos_compliment);
        $bits = '';
        for ($i = strlen($hex) - 8, $start = strlen($hex) & 7; $i >= $start; $i-=8) {
            $bits = str_pad(decbin(hexdec(substr($hex, $i, 8))), 32, '0', STR_PAD_LEFT) . $bits;
        }
        if ($start) { // hexdec('') == 0
            $bits = str_pad(decbin(hexdec(substr($hex, 0, $start))), 8, '0', STR_PAD_LEFT) . $bits;
        }
        $result = $this->precision > 0 ? substr($bits, -$this->precision) : ltrim($bits, '0');
        if ($twos_compliment && $this->compare(new static()) > 0 && $this->precision <= 0) {
            return '0' . $result;
        }
        return $result;
    }
    /**
     * Converts a BigInteger to a base-10 number.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger('50');
     *
     *    echo $a->toString(); // outputs 50
     * ?>
     * </code>
     *
     * @return String
     * @access public
     * @internal Converts a base-2**26 number to base-10**7 (which is pretty much base-10)
     */
    function toString()
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                return gmp_strval($this->value);
            case self::MODE_BCMATH:
                if ($this->value === '0') {
                    return '0';
                }
                return ltrim($this->value, '0');
        }
        if (!count($this->value)) {
            return '0';
        }
        $temp = $this->copy();
        $temp->is_negative = false;
        $divisor = new static();
        $divisor->value = array(self::$max10);
        $result = '';
        while (count($temp->value)) {
            list($temp, $mod) = $temp->divide($divisor);
            $result = str_pad(isset($mod->value[0]) ? $mod->value[0] : '', self::$max10Len, '0', STR_PAD_LEFT) . $result;
        }
        $result = ltrim($result, '0');
        if (empty($result)) {
            $result = '0';
        }
        if ($this->is_negative) {
            $result = '-' . $result;
        }
        return $result;
    }
    /**
     * Copy an object
     *
     * PHP5 passes objects by reference while PHP4 passes by value.  As such, we need a function to guarantee
     * that all objects are passed by value, when appropriate.  More information can be found here:
     *
     * {@link http://php.net/language.oop5.basic#51624}
     *
     * @access public
     * @see __clone()
     * @return \phpseclib\Math\BigInteger
     */
    function copy()
    {
        $temp = new static();
        $temp->value = $this->value;
        $temp->is_negative = $this->is_negative;
        $temp->generator = $this->generator;
        $temp->precision = $this->precision;
        $temp->bitmask = $this->bitmask;
        return $temp;
    }
    /**
     *  __toString() magic method
     *
     * Will be called, automatically, if you're supporting just PHP5.  If you're supporting PHP4, you'll need to call
     * toString().
     *
     * @access public
     * @internal Implemented per a suggestion by Techie-Michael - thanks!
     */
    function __toString()
    {
        return $this->toString();
    }
    /**
     * __clone() magic method
     *
     * Although you can call BigInteger::__toString() directly in PHP5, you cannot call BigInteger::__clone() directly
     * in PHP5.  You can in PHP4 since it's not a magic method, but in PHP5, you have to call it by using the PHP5
     * only syntax of $y = clone $x.  As such, if you're trying to write an application that works on both PHP4 and
     * PHP5, call BigInteger::copy(), instead.
     *
     * @access public
     * @see copy()
     * @return \phpseclib\Math\BigInteger
     */
    function __clone()
    {
        return $this->copy();
    }
    /**
     *  __sleep() magic method
     *
     * Will be called, automatically, when serialize() is called on a BigInteger object.
     *
     * @see __wakeup()
     * @access public
     */
    function __sleep()
    {
        $this->hex = $this->toHex(true);
        $vars = array('hex');
        if ($this->generator != 'mt_rand') {
            $vars[] = 'generator';
        }
        if ($this->precision > 0) {
            $vars[] = 'precision';
        }
        return $vars;
    }
    /**
     *  __wakeup() magic method
     *
     * Will be called, automatically, when unserialize() is called on a BigInteger object.
     *
     * @see __sleep()
     * @access public
     */
    function __wakeup()
    {
        $temp = new static($this->hex, -16);
        $this->value = $temp->value;
        $this->is_negative = $temp->is_negative;
        if ($this->precision > 0) {
            // recalculate $this->bitmask
            $this->setPrecision($this->precision);
        }
    }
    /**
     * Adds two BigIntegers.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger('10');
     *    $b = new \phpseclib\Math\BigInteger('20');
     *
     *    $c = $a->add($b);
     *
     *    echo $c->toString(); // outputs 30
     * ?>
     * </code>
     *
     * @param \phpseclib\Math\BigInteger $y
     * @return \phpseclib\Math\BigInteger
     * @access public
     * @internal Performs base-2**52 addition
     */
    function add($y)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                $temp = new static();
                $temp->value = gmp_add($this->value, $y->value);
                return $this->_normalize($temp);
            case self::MODE_BCMATH:
                $temp = new static();
                $temp->value = bcadd($this->value, $y->value, 0);
                return $this->_normalize($temp);
        }
        $temp = $this->_add($this->value, $this->is_negative, $y->value, $y->is_negative);
        $result = new static();
        $result->value = $temp[self::VALUE];
        $result->is_negative = $temp[self::SIGN];
        return $this->_normalize($result);
    }
    /**
     * Performs addition.
     *
     * @param Array $x_value
     * @param Boolean $x_negative
     * @param Array $y_value
     * @param Boolean $y_negative
     * @return Array
     * @access private
     */
    function _add($x_value, $x_negative, $y_value, $y_negative)
    {
        $x_size = count($x_value);
        $y_size = count($y_value);
        if ($x_size == 0) {
            return array(
                self::VALUE => $y_value,
                self::SIGN => $y_negative
            );
        } else if ($y_size == 0) {
            return array(
                self::VALUE => $x_value,
                self::SIGN => $x_negative
            );
        }
        // subtract, if appropriate
        if ( $x_negative != $y_negative ) {
            if ( $x_value == $y_value ) {
                return array(
                    self::VALUE => array(),
                    self::SIGN => false
                );
            }
            $temp = $this->_subtract($x_value, false, $y_value, false);
            $temp[self::SIGN] = $this->_compare($x_value, false, $y_value, false) > 0 ?
                                          $x_negative : $y_negative;
            return $temp;
        }
        if ($x_size < $y_size) {
            $size = $x_size;
            $value = $y_value;
        } else {
            $size = $y_size;
            $value = $x_value;
        }
        $value[count($value)] = 0; // just in case the carry adds an extra digit
        $carry = 0;
        for ($i = 0, $j = 1; $j < $size; $i+=2, $j+=2) {
            $sum = $x_value[$j] * self::$baseFull + $x_value[$i] + $y_value[$j] * self::$baseFull + $y_value[$i] + $carry;
            $carry = $sum >= self::$maxDigit2; // eg. floor($sum / 2**52); only possible values (in any base) are 0 and 1
            $sum = $carry ? $sum - self::$maxDigit2 : $sum;
            $temp = self::$base === 26 ? intval($sum / 0x4000000) : ($sum >> 31);
            $value[$i] = (int) ($sum - self::$baseFull * $temp); // eg. a faster alternative to fmod($sum, 0x4000000)
            $value[$j] = $temp;
        }
        if ($j == $size) { // ie. if $y_size is odd
            $sum = $x_value[$i] + $y_value[$i] + $carry;
            $carry = $sum >= self::$baseFull;
            $value[$i] = $carry ? $sum - self::$baseFull : $sum;
            ++$i; // ie. let $i = $j since we've just done $value[$i]
        }
        if ($carry) {
            for (; $value[$i] == self::$maxDigit; ++$i) {
                $value[$i] = 0;
            }
            ++$value[$i];
        }
        return array(
            self::VALUE => $this->_trim($value),
            self::SIGN => $x_negative
        );
    }
    /**
     * Subtracts two BigIntegers.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger('10');
     *    $b = new \phpseclib\Math\BigInteger('20');
     *
     *    $c = $a->subtract($b);
     *
     *    echo $c->toString(); // outputs -10
     * ?>
     * </code>
     *
     * @param \phpseclib\Math\BigInteger $y
     * @return \phpseclib\Math\BigInteger
     * @access public
     * @internal Performs base-2**52 subtraction
     */
    function subtract($y)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                $temp = new static();
                $temp->value = gmp_sub($this->value, $y->value);
                return $this->_normalize($temp);
            case self::MODE_BCMATH:
                $temp = new static();
                $temp->value = bcsub($this->value, $y->value, 0);
                return $this->_normalize($temp);
        }
        $temp = $this->_subtract($this->value, $this->is_negative, $y->value, $y->is_negative);
        $result = new static();
        $result->value = $temp[self::VALUE];
        $result->is_negative = $temp[self::SIGN];
        return $this->_normalize($result);
    }
    /**
     * Performs subtraction.
     *
     * @param Array $x_value
     * @param Boolean $x_negative
     * @param Array $y_value
     * @param Boolean $y_negative
     * @return Array
     * @access private
     */
    function _subtract($x_value, $x_negative, $y_value, $y_negative)
    {
        $x_size = count($x_value);
        $y_size = count($y_value);
        if ($x_size == 0) {
            return array(
                self::VALUE => $y_value,
                self::SIGN => !$y_negative
            );
        } else if ($y_size == 0) {
            return array(
                self::VALUE => $x_value,
                self::SIGN => $x_negative
            );
        }
        // add, if appropriate (ie. -$x - +$y or +$x - -$y)
        if ( $x_negative != $y_negative ) {
            $temp = $this->_add($x_value, false, $y_value, false);
            $temp[self::SIGN] = $x_negative;
            return $temp;
        }
        $diff = $this->_compare($x_value, $x_negative, $y_value, $y_negative);
        if ( !$diff ) {
            return array(
                self::VALUE => array(),
                self::SIGN => false
            );
        }
        // switch $x and $y around, if appropriate.
        if ( (!$x_negative && $diff < 0) || ($x_negative && $diff > 0) ) {
            $temp = $x_value;
            $x_value = $y_value;
            $y_value = $temp;
            $x_negative = !$x_negative;
            $x_size = count($x_value);
            $y_size = count($y_value);
        }
        // at this point, $x_value should be at least as big as - if not bigger than - $y_value
        $carry = 0;
        for ($i = 0, $j = 1; $j < $y_size; $i+=2, $j+=2) {
            $sum = $x_value[$j] * self::$baseFull + $x_value[$i] - $y_value[$j] * self::$baseFull - $y_value[$i] - $carry;
            $carry = $sum < 0; // eg. floor($sum / 2**52); only possible values (in any base) are 0 and 1
            $sum = $carry ? $sum + self::$maxDigit2 : $sum;
            $temp = self::$base === 26 ? intval($sum / 0x4000000) : ($sum >> 31);
            $x_value[$i] = (int) ($sum - self::$baseFull * $temp);
            $x_value[$j] = $temp;
        }
        if ($j == $y_size) { // ie. if $y_size is odd
            $sum = $x_value[$i] - $y_value[$i] - $carry;
            $carry = $sum < 0;
            $x_value[$i] = $carry ? $sum + self::$baseFull : $sum;
            ++$i;
        }
        if ($carry) {
            for (; !$x_value[$i]; ++$i) {
                $x_value[$i] = self::$maxDigit;
            }
            --$x_value[$i];
        }
        return array(
            self::VALUE => $this->_trim($x_value),
            self::SIGN => $x_negative
        );
    }
    /**
     * Multiplies two BigIntegers
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger('10');
     *    $b = new \phpseclib\Math\BigInteger('20');
     *
     *    $c = $a->multiply($b);
     *
     *    echo $c->toString(); // outputs 200
     * ?>
     * </code>
     *
     * @param \phpseclib\Math\BigInteger $x
     * @return \phpseclib\Math\BigInteger
     * @access public
     */
    function multiply($x)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                $temp = new static();
                $temp->value = gmp_mul($this->value, $x->value);
                return $this->_normalize($temp);
            case self::MODE_BCMATH:
                $temp = new static();
                $temp->value = bcmul($this->value, $x->value, 0);
                return $this->_normalize($temp);
        }
        $temp = $this->_multiply($this->value, $this->is_negative, $x->value, $x->is_negative);
        $product = new static();
        $product->value = $temp[self::VALUE];
        $product->is_negative = $temp[self::SIGN];
        return $this->_normalize($product);
    }
    /**
     * Performs multiplication.
     *
     * @param Array $x_value
     * @param Boolean $x_negative
     * @param Array $y_value
     * @param Boolean $y_negative
     * @return Array
     * @access private
     */
    function _multiply($x_value, $x_negative, $y_value, $y_negative)
    {
        //if ( $x_value == $y_value ) {
        //    return array(
        //        self::VALUE => $this->_square($x_value),
        //        self::SIGN => $x_sign != $y_value
        //    );
        //}
        $x_length = count($x_value);
        $y_length = count($y_value);
        if ( !$x_length || !$y_length ) { // a 0 is being multiplied
            return array(
                self::VALUE => array(),
                self::SIGN => false
            );
        }
        return array(
            self::VALUE => min($x_length, $y_length) < 2 * self::KARATSUBA_CUTOFF ?
                $this->_trim($this->_regularMultiply($x_value, $y_value)) :
                $this->_trim($this->_karatsuba($x_value, $y_value)),
            self::SIGN => $x_negative != $y_negative
        );
    }
    /**
     * Performs long multiplication on two BigIntegers
     *
     * Modeled after 'multiply' in MutableBigInteger.java.
     *
     * @param Array $x_value
     * @param Array $y_value
     * @return Array
     * @access private
     */
    function _regularMultiply($x_value, $y_value)
    {
        $x_length = count($x_value);
        $y_length = count($y_value);
        if ( !$x_length || !$y_length ) { // a 0 is being multiplied
            return array();
        }
        if ( $x_length < $y_length ) {
            $temp = $x_value;
            $x_value = $y_value;
            $y_value = $temp;
            $x_length = count($x_value);
            $y_length = count($y_value);
        }
        $product_value = $this->_array_repeat(0, $x_length + $y_length);
        // the following for loop could be removed if the for loop following it
        // (the one with nested for loops) initially set $i to 0, but
        // doing so would also make the result in one set of unnecessary adds,
        // since on the outermost loops first pass, $product->value[$k] is going
        // to always be 0
        $carry = 0;
        for ($j = 0; $j < $x_length; ++$j) { // ie. $i = 0
            $temp = $x_value[$j] * $y_value[0] + $carry; // $product_value[$k] == 0
            $carry = self::$base === 26 ? intval($temp / 0x4000000) : ($temp >> 31);
            $product_value[$j] = (int) ($temp - self::$baseFull * $carry);
        }
        $product_value[$j] = $carry;
        // the above for loop is what the previous comment was talking about.  the
        // following for loop is the "one with nested for loops"
        for ($i = 1; $i < $y_length; ++$i) {
            $carry = 0;
            for ($j = 0, $k = $i; $j < $x_length; ++$j, ++$k) {
                $temp = $product_value[$k] + $x_value[$j] * $y_value[$i] + $carry;
                $carry = self::$base === 26 ? intval($temp / 0x4000000) : ($temp >> 31);
                $product_value[$k] = (int) ($temp - self::$baseFull * $carry);
            }
            $product_value[$k] = $carry;
        }
        return $product_value;
    }
    /**
     * Performs Karatsuba multiplication on two BigIntegers
     *
     * See {@link http://en.wikipedia.org/wiki/Karatsuba_algorithm Karatsuba algorithm} and
     * {@link http://math.libtomcrypt.com/files/tommath.pdf#page=120 MPM 5.2.3}.
     *
     * @param Array $x_value
     * @param Array $y_value
     * @return Array
     * @access private
     */
    function _karatsuba($x_value, $y_value)
    {
        $m = min(count($x_value) >> 1, count($y_value) >> 1);
        if ($m < self::KARATSUBA_CUTOFF) {
            return $this->_regularMultiply($x_value, $y_value);
        }
        $x1 = array_slice($x_value, $m);
        $x0 = array_slice($x_value, 0, $m);
        $y1 = array_slice($y_value, $m);
        $y0 = array_slice($y_value, 0, $m);
        $z2 = $this->_karatsuba($x1, $y1);
        $z0 = $this->_karatsuba($x0, $y0);
        $z1 = $this->_add($x1, false, $x0, false);
        $temp = $this->_add($y1, false, $y0, false);
        $z1 = $this->_karatsuba($z1[self::VALUE], $temp[self::VALUE]);
        $temp = $this->_add($z2, false, $z0, false);
        $z1 = $this->_subtract($z1, false, $temp[self::VALUE], false);
        $z2 = array_merge(array_fill(0, 2 * $m, 0), $z2);
        $z1[self::VALUE] = array_merge(array_fill(0, $m, 0), $z1[self::VALUE]);
        $xy = $this->_add($z2, false, $z1[self::VALUE], $z1[self::SIGN]);
        $xy = $this->_add($xy[self::VALUE], $xy[self::SIGN], $z0, false);
        return $xy[self::VALUE];
    }
    /**
     * Performs squaring
     *
     * @param Array $x
     * @return Array
     * @access private
     */
    function _square($x = false)
    {
        return count($x) < 2 * self::KARATSUBA_CUTOFF ?
            $this->_trim($this->_baseSquare($x)) :
            $this->_trim($this->_karatsubaSquare($x));
    }
    /**
     * Performs traditional squaring on two BigIntegers
     *
     * Squaring can be done faster than multiplying a number by itself can be.  See
     * {@link http://www.cacr.math.uwaterloo.ca/hac/about/chap14.pdf#page=7 HAC 14.2.4} /
     * {@link http://math.libtomcrypt.com/files/tommath.pdf#page=141 MPM 5.3} for more information.
     *
     * @param Array $value
     * @return Array
     * @access private
     */
    function _baseSquare($value)
    {
        if ( empty($value) ) {
            return array();
        }
        $square_value = $this->_array_repeat(0, 2 * count($value));
        for ($i = 0, $max_index = count($value) - 1; $i <= $max_index; ++$i) {
            $i2 = $i << 1;
            $temp = $square_value[$i2] + $value[$i] * $value[$i];
            $carry = self::$base === 26 ? intval($temp / 0x4000000) : ($temp >> 31);
            $square_value[$i2] = (int) ($temp - self::$baseFull * $carry);
            // note how we start from $i+1 instead of 0 as we do in multiplication.
            for ($j = $i + 1, $k = $i2 + 1; $j <= $max_index; ++$j, ++$k) {
                $temp = $square_value[$k] + 2 * $value[$j] * $value[$i] + $carry;
                $carry = self::$base === 26 ? intval($temp / 0x4000000) : ($temp >> 31);
                $square_value[$k] = (int) ($temp - self::$baseFull * $carry);
            }
            // the following line can yield values larger 2**15.  at this point, PHP should switch
            // over to floats.
            $square_value[$i + $max_index + 1] = $carry;
        }
        return $square_value;
    }
    /**
     * Performs Karatsuba "squaring" on two BigIntegers
     *
     * See {@link http://en.wikipedia.org/wiki/Karatsuba_algorithm Karatsuba algorithm} and
     * {@link http://math.libtomcrypt.com/files/tommath.pdf#page=151 MPM 5.3.4}.
     *
     * @param Array $value
     * @return Array
     * @access private
     */
    function _karatsubaSquare($value)
    {
        $m = count($value) >> 1;
        if ($m < self::KARATSUBA_CUTOFF) {
            return $this->_baseSquare($value);
        }
        $x1 = array_slice($value, $m);
        $x0 = array_slice($value, 0, $m);
        $z2 = $this->_karatsubaSquare($x1);
        $z0 = $this->_karatsubaSquare($x0);
        $z1 = $this->_add($x1, false, $x0, false);
        $z1 = $this->_karatsubaSquare($z1[self::VALUE]);
        $temp = $this->_add($z2, false, $z0, false);
        $z1 = $this->_subtract($z1, false, $temp[self::VALUE], false);
        $z2 = array_merge(array_fill(0, 2 * $m, 0), $z2);
        $z1[self::VALUE] = array_merge(array_fill(0, $m, 0), $z1[self::VALUE]);
        $xx = $this->_add($z2, false, $z1[self::VALUE], $z1[self::SIGN]);
        $xx = $this->_add($xx[self::VALUE], $xx[self::SIGN], $z0, false);
        return $xx[self::VALUE];
    }
    /**
     * Divides two BigIntegers.
     *
     * Returns an array whose first element contains the quotient and whose second element contains the
     * "common residue".  If the remainder would be positive, the "common residue" and the remainder are the
     * same.  If the remainder would be negative, the "common residue" is equal to the sum of the remainder
     * and the divisor (basically, the "common residue" is the first positive modulo).
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger('10');
     *    $b = new \phpseclib\Math\BigInteger('20');
     *
     *    list($quotient, $remainder) = $a->divide($b);
     *
     *    echo $quotient->toString(); // outputs 0
     *    echo "\r\n";
     *    echo $remainder->toString(); // outputs 10
     * ?>
     * </code>
     *
     * @param \phpseclib\Math\BigInteger $y
     * @return Array
     * @access public
     * @internal This function is based off of {@link http://www.cacr.math.uwaterloo.ca/hac/about/chap14.pdf#page=9 HAC 14.20}.
     */
    function divide($y)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                $quotient = new static();
                $remainder = new static();
                list($quotient->value, $remainder->value) = gmp_div_qr($this->value, $y->value);
                if (gmp_sign($remainder->value) < 0) {
                    $remainder->value = gmp_add($remainder->value, gmp_abs($y->value));
                }
                return array($this->_normalize($quotient), $this->_normalize($remainder));
            case self::MODE_BCMATH:
                $quotient = new static();
                $remainder = new static();
                $quotient->value = bcdiv($this->value, $y->value, 0);
                $remainder->value = bcmod($this->value, $y->value);
                if ($remainder->value[0] == '-') {
                    $remainder->value = bcadd($remainder->value, $y->value[0] == '-' ? substr($y->value, 1) : $y->value, 0);
                }
                return array($this->_normalize($quotient), $this->_normalize($remainder));
        }
        if (count($y->value) == 1) {
            list($q, $r) = $this->_divide_digit($this->value, $y->value[0]);
            $quotient = new static();
            $remainder = new static();
            $quotient->value = $q;
            $remainder->value = array($r);
            $quotient->is_negative = $this->is_negative != $y->is_negative;
            return array($this->_normalize($quotient), $this->_normalize($remainder));
        }
        static $zero;
        if ( !isset($zero) ) {
            $zero = new static();
        }
        $x = $this->copy();
        $y = $y->copy();
        $x_sign = $x->is_negative;
        $y_sign = $y->is_negative;
        $x->is_negative = $y->is_negative = false;
        $diff = $x->compare($y);
        if ( !$diff ) {
            $temp = new static();
            $temp->value = array(1);
            $temp->is_negative = $x_sign != $y_sign;
            return array($this->_normalize($temp), $this->_normalize(new static()));
        }
        if ( $diff < 0 ) {
            // if $x is negative, "add" $y.
            if ( $x_sign ) {
                $x = $y->subtract($x);
            }
            return array($this->_normalize(new static()), $this->_normalize($x));
        }
        // normalize $x and $y as described in HAC 14.23 / 14.24
        $msb = $y->value[count($y->value) - 1];
        for ($shift = 0; !($msb & self::$msb); ++$shift) {
            $msb <<= 1;
        }
        $x->_lshift($shift);
        $y->_lshift($shift);
        $y_value = &$y->value;
        $x_max = count($x->value) - 1;
        $y_max = count($y->value) - 1;
        $quotient = new static();
        $quotient_value = &$quotient->value;
        $quotient_value = $this->_array_repeat(0, $x_max - $y_max + 1);
        static $temp, $lhs, $rhs;
        if (!isset($temp)) {
            $temp = new static();
            $lhs =  new static();
            $rhs =  new static();
        }
        $temp_value = &$temp->value;
        $rhs_value =  &$rhs->value;
        // $temp = $y << ($x_max - $y_max-1) in base 2**26
        $temp_value = array_merge($this->_array_repeat(0, $x_max - $y_max), $y_value);
        while ( $x->compare($temp) >= 0 ) {
            // calculate the "common residue"
            ++$quotient_value[$x_max - $y_max];
            $x = $x->subtract($temp);
            $x_max = count($x->value) - 1;
        }
        for ($i = $x_max; $i >= $y_max + 1; --$i) {
            $x_value = &$x->value;
            $x_window = array(
                isset($x_value[$i]) ? $x_value[$i] : 0,
                isset($x_value[$i - 1]) ? $x_value[$i - 1] : 0,
                isset($x_value[$i - 2]) ? $x_value[$i - 2] : 0
            );
            $y_window = array(
                $y_value[$y_max],
                ( $y_max > 0 ) ? $y_value[$y_max - 1] : 0
            );
            $q_index = $i - $y_max - 1;
            if ($x_window[0] == $y_window[0]) {
                $quotient_value[$q_index] = self::$maxDigit;
            } else {
                $quotient_value[$q_index] = $this->_safe_divide(
                    $x_window[0] * self::$baseFull + $x_window[1],
                    $y_window[0]
                );
            }
            $temp_value = array($y_window[1], $y_window[0]);
            $lhs->value = array($quotient_value[$q_index]);
            $lhs = $lhs->multiply($temp);
            $rhs_value = array($x_window[2], $x_window[1], $x_window[0]);
            while ( $lhs->compare($rhs) > 0 ) {
                --$quotient_value[$q_index];
                $lhs->value = array($quotient_value[$q_index]);
                $lhs = $lhs->multiply($temp);
            }
            $adjust = $this->_array_repeat(0, $q_index);
            $temp_value = array($quotient_value[$q_index]);
            $temp = $temp->multiply($y);
            $temp_value = &$temp->value;
            $temp_value = array_merge($adjust, $temp_value);
            $x = $x->subtract($temp);
            if ($x->compare($zero) < 0) {
                $temp_value = array_merge($adjust, $y_value);
                $x = $x->add($temp);
                --$quotient_value[$q_index];
            }
            $x_max = count($x_value) - 1;
        }
        // unnormalize the remainder
        $x->_rshift($shift);
        $quotient->is_negative = $x_sign != $y_sign;
        // calculate the "common residue", if appropriate
        if ( $x_sign ) {
            $y->_rshift($shift);
            $x = $y->subtract($x);
        }
        return array($this->_normalize($quotient), $this->_normalize($x));
    }
    /**
     * Divides a BigInteger by a regular integer
     *
     * abc / x = a00 / x + b0 / x + c / x
     *
     * @param Array $dividend
     * @param Array $divisor
     * @return Array
     * @access private
     */
    function _divide_digit($dividend, $divisor)
    {
        $carry = 0;
        $result = array();
        for ($i = count($dividend) - 1; $i >= 0; --$i) {
            $temp = self::$baseFull * $carry + $dividend[$i];
            $result[$i] = $this->_safe_divide($temp, $divisor);
            $carry = (int) ($temp - $divisor * $result[$i]);
        }
        return array($result, $carry);
    }
    /**
     * Performs modular exponentiation.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger('10');
     *    $b = new \phpseclib\Math\BigInteger('20');
     *    $c = new \phpseclib\Math\BigInteger('30');
     *
     *    $c = $a->modPow($b, $c);
     *
     *    echo $c->toString(); // outputs 10
     * ?>
     * </code>
     *
     * @param \phpseclib\Math\BigInteger $e
     * @param \phpseclib\Math\BigInteger $n
     * @return \phpseclib\Math\BigInteger
     * @access public
     * @internal The most naive approach to modular exponentiation has very unreasonable requirements, and
     *    and although the approach involving repeated squaring does vastly better, it, too, is impractical
     *    for our purposes.  The reason being that division - by far the most complicated and time-consuming
     *    of the basic operations (eg. +,-,*,/) - occurs multiple times within it.
     *
     *    Modular reductions resolve this issue.  Although an individual modular reduction takes more time
     *    then an individual division, when performed in succession (with the same modulo), they're a lot faster.
     *
     *    The two most commonly used modular reductions are Barrett and Montgomery reduction.  Montgomery reduction,
     *    although faster, only works when the gcd of the modulo and of the base being used is 1.  In RSA, when the
     *    base is a power of two, the modulo - a product of two primes - is always going to have a gcd of 1 (because
     *    the product of two odd numbers is odd), but what about when RSA isn't used?
     *
     *    In contrast, Barrett reduction has no such constraint.  As such, some bigint implementations perform a
     *    Barrett reduction after every operation in the modpow function.  Others perform Barrett reductions when the
     *    modulo is even and Montgomery reductions when the modulo is odd.  BigInteger.java's modPow method, however,
     *    uses a trick involving the Chinese Remainder Theorem to factor the even modulo into two numbers - one odd and
     *    the other, a power of two - and recombine them, later.  This is the method that this modPow function uses.
     *    {@link http://islab.oregonstate.edu/papers/j34monex.pdf Montgomery Reduction with Even Modulus} elaborates.
     */
    function modPow($e, $n)
    {
    
        $n = $this->bitmask !== false && $this->bitmask->compare($n) < 0 ? $this->bitmask : $n->abs();
        if ($e->compare(new static()) < 0) {
            $e = $e->abs();
            $temp = $this->modInverse($n);
            if ($temp === false) {
                return false;
            }
            return $this->_normalize($temp->modPow($e, $n));
        }
        if ( MATH_BIGINTEGER_MODE == self::MODE_GMP ) {
            $temp = new static();
            $temp->value = gmp_powm($this->value, $e->value, $n->value);
            return $this->_normalize($temp);
        }
        if ($this->compare(new static()) < 0 || $this->compare($n) > 0) {
            list(, $temp) = $this->divide($n);
            return $temp->modPow($e, $n);
        }
        if (defined('MATH_BIGINTEGER_OPENSSL_ENABLED')) {
            $components = array(
                'modulus' => $n->toBytes(true),
                'publicExponent' => $e->toBytes(true)
            );
            $components = array(
                'modulus' => pack('Ca*a*', 2, $this->_encodeASN1Length(strlen($components['modulus'])), $components['modulus']),
                'publicExponent' => pack('Ca*a*', 2, $this->_encodeASN1Length(strlen($components['publicExponent'])), $components['publicExponent'])
            );
            $RSAPublicKey = pack('Ca*a*a*',
                48, $this->_encodeASN1Length(strlen($components['modulus']) + strlen($components['publicExponent'])),
                $components['modulus'], $components['publicExponent']
            );
            $rsaOID = pack('H*', '300d06092a864886f70d0101010500'); // hex version of MA0GCSqGSIb3DQEBAQUA
            $RSAPublicKey = chr(0) . $RSAPublicKey;
            $RSAPublicKey = chr(3) . $this->_encodeASN1Length(strlen($RSAPublicKey)) . $RSAPublicKey;
            $encapsulated = pack('Ca*a*',
                48, $this->_encodeASN1Length(strlen($rsaOID . $RSAPublicKey)), $rsaOID . $RSAPublicKey
            );
            $RSAPublicKey = "-----BEGIN PUBLIC KEY-----\r\n" .
                             chunk_split(base64_encode($encapsulated)) .
                             '-----END PUBLIC KEY-----';
            $plaintext = str_pad($this->toBytes(), strlen($n->toBytes(true)) - 1, "\0", STR_PAD_LEFT);
	            if (openssl_public_encrypt($plaintext, $result, $RSAPublicKey, OPENSSL_NO_PADDING)) {
                return new static($result, 256);
            }
        }
        if ( MATH_BIGINTEGER_MODE == self::MODE_BCMATH ) {
                $temp = new static();
                $temp->value = bcpowmod($this->value, $e->value, $n->value, 0);
                return $this->_normalize($temp);
        }
        if ( empty($e->value) ) {
            $temp = new static();
            $temp->value = array(1);
            return $this->_normalize($temp);
        }
        if ( $e->value == array(1) ) {
            list(, $temp) = $this->divide($n);
            return $this->_normalize($temp);
        }
        if ( $e->value == array(2) ) {
            $temp = new static();
            $temp->value = $this->_square($this->value);
            list(, $temp) = $temp->divide($n);
            return $this->_normalize($temp);
        }
        return $this->_normalize($this->_slidingWindow($e, $n, self::BARRETT));
        // the following code, although not callable, can be run independently of the above code
        // although the above code performed better in my benchmarks the following could might
        // perform better under different circumstances. in lieu of deleting it it's just been
        // made uncallable
        // is the modulo odd?
        if ( $n->value[0] & 1 ) {
            return $this->_normalize($this->_slidingWindow($e, $n, self::MONTGOMERY));
        }
        // if it's not, it's even
        // find the lowest set bit (eg. the max pow of 2 that divides $n)
        for ($i = 0; $i < count($n->value); ++$i) {
            if ( $n->value[$i] ) {
                $temp = decbin($n->value[$i]);
                $j = strlen($temp) - strrpos($temp, '1') - 1;
                $j+= 26 * $i;
                break;
            }
        }
        // at this point, 2^$j * $n/(2^$j) == $n
        $mod1 = $n->copy();
        $mod1->_rshift($j);
        $mod2 = new static();
        $mod2->value = array(1);
        $mod2->_lshift($j);
        $part1 = ( $mod1->value != array(1) ) ? $this->_slidingWindow($e, $mod1, self::MONTGOMERY) : new static();
        $part2 = $this->_slidingWindow($e, $mod2, self::POWEROF2);
        $y1 = $mod2->modInverse($mod1);
        $y2 = $mod1->modInverse($mod2);
        $result = $part1->multiply($mod2);
        $result = $result->multiply($y1);
        $temp = $part2->multiply($mod1);
        $temp = $temp->multiply($y2);
        $result = $result->add($temp);
        list(, $result) = $result->divide($n);
        return $this->_normalize($result);
    }
    /**
     * Performs modular exponentiation.
     *
     * Alias for modPow().
     *
     * @param \phpseclib\Math\BigInteger $e
     * @param \phpseclib\Math\BigInteger $n
     * @return \phpseclib\Math\BigInteger
     * @access public
     */
    function powMod($e, $n)
    {
        return $this->modPow($e, $n);
    }
    /**
     * Sliding Window k-ary Modular Exponentiation
     *
     * Based on {@link http://www.cacr.math.uwaterloo.ca/hac/about/chap14.pdf#page=27 HAC 14.85} /
     * {@link http://math.libtomcrypt.com/files/tommath.pdf#page=210 MPM 7.7}.  In a departure from those algorithims,
     * however, this function performs a modular reduction after every multiplication and squaring operation.
     * As such, this function has the same preconditions that the reductions being used do.
     *
     * @param \phpseclib\Math\BigInteger $e
     * @param \phpseclib\Math\BigInteger $n
     * @param Integer $mode
     * @return \phpseclib\Math\BigInteger
     * @access private
     */
    function _slidingWindow($e, $n, $mode)
    {
        static $window_ranges = array(7, 25, 81, 241, 673, 1793); // from BigInteger.java's oddModPow function
        //static $window_ranges = array(0, 7, 36, 140, 450, 1303, 3529); // from MPM 7.3.1
        $e_value = $e->value;
        $e_length = count($e_value) - 1;
        $e_bits = decbin($e_value[$e_length]);
        for ($i = $e_length - 1; $i >= 0; --$i) {
            $e_bits.= str_pad(decbin($e_value[$i]), self::$base, '0', STR_PAD_LEFT);
        }
        $e_length = strlen($e_bits);
        // calculate the appropriate window size.
        // $window_size == 3 if $window_ranges is between 25 and 81, for example.
        for ($i = 0, $window_size = 1; $e_length > $window_ranges[$i] && $i < count($window_ranges); ++$window_size, ++$i);
        $n_value = $n->value;
        // precompute $this^0 through $this^$window_size
        $powers = array();
        $powers[1] = $this->_prepareReduce($this->value, $n_value, $mode);
        $powers[2] = $this->_squareReduce($powers[1], $n_value, $mode);
        // we do every other number since substr($e_bits, $i, $j+1) (see below) is supposed to end
        // in a 1.  ie. it's supposed to be odd.
        $temp = 1 << ($window_size - 1);
        for ($i = 1; $i < $temp; ++$i) {
            $i2 = $i << 1;
            $powers[$i2 + 1] = $this->_multiplyReduce($powers[$i2 - 1], $powers[2], $n_value, $mode);
        }
        $result = array(1);
        $result = $this->_prepareReduce($result, $n_value, $mode);
        for ($i = 0; $i < $e_length; ) {
            if ( !$e_bits[$i] ) {
                $result = $this->_squareReduce($result, $n_value, $mode);
                ++$i;
            } else {
                for ($j = $window_size - 1; $j > 0; --$j) {
                    if ( !empty($e_bits[$i + $j]) ) {
                        break;
                    }
                }
                for ($k = 0; $k <= $j; ++$k) {// eg. the length of substr($e_bits, $i, $j+1)
                    $result = $this->_squareReduce($result, $n_value, $mode);
                }
                $result = $this->_multiplyReduce($result, $powers[bindec(substr($e_bits, $i, $j + 1))], $n_value, $mode);
                $i+=$j + 1;
            }
        }
        $temp = new static();
        $temp->value = $this->_reduce($result, $n_value, $mode);
        return $temp;
    }
    /**
     * Modular reduction
     *
     * For most $modes this will return the remainder.
     *
     * @see _slidingWindow()
     * @access private
     * @param Array $x
     * @param Array $n
     * @param Integer $mode
     * @return Array
     */
    function _reduce($x, $n, $mode)
    {
        switch ($mode) {
            case self::MONTGOMERY:
                return $this->_montgomery($x, $n);
            case self::BARRETT:
                return $this->_barrett($x, $n);
            case self::POWEROF2:
                $lhs = new static();
                $lhs->value = $x;
                $rhs = new static();
                $rhs->value = $n;
                return $x->_mod2($n);
            case self::CLASSIC:
                $lhs = new static();
                $lhs->value = $x;
                $rhs = new static();
                $rhs->value = $n;
                list(, $temp) = $lhs->divide($rhs);
                return $temp->value;
            case self::NONE:
                return $x;
            default:
                // an invalid $mode was provided
        }
    }
    /**
     * Modular reduction preperation
     *
     * @see _slidingWindow()
     * @access private
     * @param Array $x
     * @param Array $n
     * @param Integer $mode
     * @return Array
     */
    function _prepareReduce($x, $n, $mode)
    {
        if ($mode == self::MONTGOMERY) {
            return $this->_prepMontgomery($x, $n);
        }
        return $this->_reduce($x, $n, $mode);
    }
    /**
     * Modular multiply
     *
     * @see _slidingWindow()
     * @access private
     * @param Array $x
     * @param Array $y
     * @param Array $n
     * @param Integer $mode
     * @return Array
     */
    function _multiplyReduce($x, $y, $n, $mode)
    {
        if ($mode == self::MONTGOMERY) {
            return $this->_montgomeryMultiply($x, $y, $n);
        }
        $temp = $this->_multiply($x, false, $y, false);
        return $this->_reduce($temp[self::VALUE], $n, $mode);
    }
    /**
     * Modular square
     *
     * @see _slidingWindow()
     * @access private
     * @param Array $x
     * @param Array $n
     * @param Integer $mode
     * @return Array
     */
    function _squareReduce($x, $n, $mode)
    {
        if ($mode == self::MONTGOMERY) {
            return $this->_montgomeryMultiply($x, $x, $n);
        }
        return $this->_reduce($this->_square($x), $n, $mode);
    }
    /**
     * Modulos for Powers of Two
     *
     * Calculates $x%$n, where $n = 2**$e, for some $e.  Since this is basically the same as doing $x & ($n-1),
     * we'll just use this function as a wrapper for doing that.
     *
     * @see _slidingWindow()
     * @access private
     * @param \phpseclib\Math\BigInteger
     * @return \phpseclib\Math\BigInteger
     */
    function _mod2($n)
    {
        $temp = new static();
        $temp->value = array(1);
        return $this->bitwise_and($n->subtract($temp));
    }
    /**
     * Barrett Modular Reduction
     *
     * See {@link http://www.cacr.math.uwaterloo.ca/hac/about/chap14.pdf#page=14 HAC 14.3.3} /
     * {@link http://math.libtomcrypt.com/files/tommath.pdf#page=165 MPM 6.2.5} for more information.  Modified slightly,
     * so as not to require negative numbers (initially, this script didn't support negative numbers).
     *
     * Employs "folding", as described at
     * {@link http://www.cosic.esat.kuleuven.be/publications/thesis-149.pdf#page=66 thesis-149.pdf#page=66}.  To quote from
     * it, "the idea [behind folding] is to find a value x' such that x (mod m) = x' (mod m), with x' being smaller than x."
     *
     * Unfortunately, the "Barrett Reduction with Folding" algorithm described in thesis-149.pdf is not, as written, all that
     * usable on account of (1) its not using reasonable radix points as discussed in
     * {@link http://math.libtomcrypt.com/files/tommath.pdf#page=162 MPM 6.2.2} and (2) the fact that, even with reasonable
     * radix points, it only works when there are an even number of digits in the denominator.  The reason for (2) is that
     * (x >> 1) + (x >> 1) != x / 2 + x / 2.  If x is even, they're the same, but if x is odd, they're not.  See the in-line
     * comments for details.
     *
     * @see _slidingWindow()
     * @access private
     * @param Array $n
     * @param Array $m
     * @return Array
     */
    function _barrett($n, $m)
    {
        static $cache = array(
            self::VARIABLE => array(),
            self::DATA => array()
        );
        $m_length = count($m);
        // if ($this->_compare($n, $this->_square($m)) >= 0) {
        if (count($n) > 2 * $m_length) {
            $lhs = new static();
            $rhs = new static();
            $lhs->value = $n;
            $rhs->value = $m;
            list(, $temp) = $lhs->divide($rhs);
            return $temp->value;
        }
        // if (m.length >> 1) + 2 <= m.length then m is too small and n can't be reduced
        if ($m_length < 5) {
            return $this->_regularBarrett($n, $m);
        }
        // n = 2 * m.length
        if ( ($key = array_search($m, $cache[self::VARIABLE])) === false ) {
            $key = count($cache[self::VARIABLE]);
            $cache[self::VARIABLE][] = $m;
            $lhs = new static();
            $lhs_value = &$lhs->value;
            $lhs_value = $this->_array_repeat(0, $m_length + ($m_length >> 1));
            $lhs_value[] = 1;
            $rhs = new static();
            $rhs->value = $m;
            list($u, $m1) = $lhs->divide($rhs);
            $u = $u->value;
            $m1 = $m1->value;
            $cache[self::DATA][] = array(
                'u' => $u, // m.length >> 1 (technically (m.length >> 1) + 1)
                'm1'=> $m1 // m.length
            );
        } else {
            extract($cache[self::DATA][$key]);
        }
        $cutoff = $m_length + ($m_length >> 1);
        $lsd = array_slice($n, 0, $cutoff); // m.length + (m.length >> 1)
        $msd = array_slice($n, $cutoff);    // m.length >> 1
        $lsd = $this->_trim($lsd);
        $temp = $this->_multiply($msd, false, $m1, false);
        $n = $this->_add($lsd, false, $temp[self::VALUE], false); // m.length + (m.length >> 1) + 1
        if ($m_length & 1) {
            return $this->_regularBarrett($n[self::VALUE], $m);
        }
        // (m.length + (m.length >> 1) + 1) - (m.length - 1) == (m.length >> 1) + 2
        $temp = array_slice($n[self::VALUE], $m_length - 1);
        // if even: ((m.length >> 1) + 2) + (m.length >> 1) == m.length + 2
        // if odd:  ((m.length >> 1) + 2) + (m.length >> 1) == (m.length - 1) + 2 == m.length + 1
        $temp = $this->_multiply($temp, false, $u, false);
        // if even: (m.length + 2) - ((m.length >> 1) + 1) = m.length - (m.length >> 1) + 1
        // if odd:  (m.length + 1) - ((m.length >> 1) + 1) = m.length - (m.length >> 1)
        $temp = array_slice($temp[self::VALUE], ($m_length >> 1) + 1);
        // if even: (m.length - (m.length >> 1) + 1) + m.length = 2 * m.length - (m.length >> 1) + 1
        // if odd:  (m.length - (m.length >> 1)) + m.length     = 2 * m.length - (m.length >> 1)
        $temp = $this->_multiply($temp, false, $m, false);
        // at this point, if m had an odd number of digits, we'd be subtracting a 2 * m.length - (m.length >> 1) digit
        // number from a m.length + (m.length >> 1) + 1 digit number.  ie. there'd be an extra digit and the while loop
        // following this comment would loop a lot (hence our calling _regularBarrett() in that situation).
        $result = $this->_subtract($n[self::VALUE], false, $temp[self::VALUE], false);
        while ($this->_compare($result[self::VALUE], $result[self::SIGN], $m, false) >= 0) {
            $result = $this->_subtract($result[self::VALUE], $result[self::SIGN], $m, false);
        }
        return $result[self::VALUE];
    }
    /**
     * (Regular) Barrett Modular Reduction
     *
     * For numbers with more than four digits BigInteger::_barrett() is faster.  The difference between that and this
     * is that this function does not fold the denominator into a smaller form.
     *
     * @see _slidingWindow()
     * @access private
     * @param Array $x
     * @param Array $n
     * @return Array
     */
    function _regularBarrett($x, $n)
    {
        static $cache = array(
            self::VARIABLE => array(),
            self::DATA => array()
        );
        $n_length = count($n);
        if (count($x) > 2 * $n_length) {
            $lhs = new static();
            $rhs = new static();
            $lhs->value = $x;
            $rhs->value = $n;
            list(, $temp) = $lhs->divide($rhs);
            return $temp->value;
        }
        if ( ($key = array_search($n, $cache[self::VARIABLE])) === false ) {
            $key = count($cache[self::VARIABLE]);
            $cache[self::VARIABLE][] = $n;
            $lhs = new static();
            $lhs_value = &$lhs->value;
            $lhs_value = $this->_array_repeat(0, 2 * $n_length);
            $lhs_value[] = 1;
            $rhs = new static();
            $rhs->value = $n;
            list($temp, ) = $lhs->divide($rhs); // m.length
            $cache[self::DATA][] = $temp->value;
        }
        // 2 * m.length - (m.length - 1) = m.length + 1
        $temp = array_slice($x, $n_length - 1);
        // (m.length + 1) + m.length = 2 * m.length + 1
        $temp = $this->_multiply($temp, false, $cache[self::DATA][$key], false);
        // (2 * m.length + 1) - (m.length - 1) = m.length + 2
        $temp = array_slice($temp[self::VALUE], $n_length + 1);
        // m.length + 1
        $result = array_slice($x, 0, $n_length + 1);
        // m.length + 1
        $temp = $this->_multiplyLower($temp, false, $n, false, $n_length + 1);
        // $temp == array_slice($temp->_multiply($temp, false, $n, false)->value, 0, $n_length + 1)
        if ($this->_compare($result, false, $temp[self::VALUE], $temp[self::SIGN]) < 0) {
            $corrector_value = $this->_array_repeat(0, $n_length + 1);
            $corrector_value[count($corrector_value)] = 1;
            $result = $this->_add($result, false, $corrector_value, false);
            $result = $result[self::VALUE];
        }
        // at this point, we're subtracting a number with m.length + 1 digits from another number with m.length + 1 digits
        $result = $this->_subtract($result, false, $temp[self::VALUE], $temp[self::SIGN]);
        while ($this->_compare($result[self::VALUE], $result[self::SIGN], $n, false) > 0) {
            $result = $this->_subtract($result[self::VALUE], $result[self::SIGN], $n, false);
        }
        return $result[self::VALUE];
    }
    /**
     * Performs long multiplication up to $stop digits
     *
     * If you're going to be doing array_slice($product->value, 0, $stop), some cycles can be saved.
     *
     * @see _regularBarrett()
     * @param Array $x_value
     * @param Boolean $x_negative
     * @param Array $y_value
     * @param Boolean $y_negative
     * @param Integer $stop
     * @return Array
     * @access private
     */
    function _multiplyLower($x_value, $x_negative, $y_value, $y_negative, $stop)
    {
        $x_length = count($x_value);
        $y_length = count($y_value);
        if ( !$x_length || !$y_length ) { // a 0 is being multiplied
            return array(
                self::VALUE => array(),
                self::SIGN => false
            );
        }
        if ( $x_length < $y_length ) {
            $temp = $x_value;
            $x_value = $y_value;
            $y_value = $temp;
            $x_length = count($x_value);
            $y_length = count($y_value);
        }
        $product_value = $this->_array_repeat(0, $x_length + $y_length);
        // the following for loop could be removed if the for loop following it
        // (the one with nested for loops) initially set $i to 0, but
        // doing so would also make the result in one set of unnecessary adds,
        // since on the outermost loops first pass, $product->value[$k] is going
        // to always be 0
        $carry = 0;
        for ($j = 0; $j < $x_length; ++$j) { // ie. $i = 0, $k = $i
            $temp = $x_value[$j] * $y_value[0] + $carry; // $product_value[$k] == 0
            $carry = self::$base === 26 ? intval($temp / 0x4000000) : ($temp >> 31);
            $product_value[$j] = (int) ($temp - self::$baseFull * $carry);
        }
        if ($j < $stop) {
            $product_value[$j] = $carry;
        }
        // the above for loop is what the previous comment was talking about.  the
        // following for loop is the "one with nested for loops"
        for ($i = 1; $i < $y_length; ++$i) {
            $carry = 0;
            for ($j = 0, $k = $i; $j < $x_length && $k < $stop; ++$j, ++$k) {
                $temp = $product_value[$k] + $x_value[$j] * $y_value[$i] + $carry;
                $carry = self::$base === 26 ? intval($temp / 0x4000000) : ($temp >> 31);
                $product_value[$k] = (int) ($temp - self::$baseFull * $carry);
            }
            if ($k < $stop) {
                $product_value[$k] = $carry;
            }
        }
        return array(
            self::VALUE => $this->_trim($product_value),
            self::SIGN => $x_negative != $y_negative
        );
    }
    /**
     * Montgomery Modular Reduction
     *
     * ($x->_prepMontgomery($n))->_montgomery($n) yields $x % $n.
     * {@link http://math.libtomcrypt.com/files/tommath.pdf#page=170 MPM 6.3} provides insights on how this can be
     * improved upon (basically, by using the comba method).  gcd($n, 2) must be equal to one for this function
     * to work correctly.
     *
     * @see _prepMontgomery()
     * @see _slidingWindow()
     * @access private
     * @param Array $x
     * @param Array $n
     * @return Array
     */
    function _montgomery($x, $n)
    {
        static $cache = array(
            self::VARIABLE => array(),
            self::DATA => array()
        );
        if ( ($key = array_search($n, $cache[self::VARIABLE])) === false ) {
            $key = count($cache[self::VARIABLE]);
            $cache[self::VARIABLE][] = $x;
            $cache[self::DATA][] = $this->_modInverse67108864($n);
        }
        $k = count($n);
        $result = array(self::VALUE => $x);
        for ($i = 0; $i < $k; ++$i) {
            $temp = $result[self::VALUE][$i] * $cache[self::DATA][$key];
            $temp = $temp - self::$baseFull * (self::$base === 26 ? intval($temp / 0x4000000) : ($temp >> 31));
            $temp = $this->_regularMultiply(array($temp), $n);
            $temp = array_merge($this->_array_repeat(0, $i), $temp);
            $result = $this->_add($result[self::VALUE], false, $temp, false);
        }
        $result[self::VALUE] = array_slice($result[self::VALUE], $k);
        if ($this->_compare($result, false, $n, false) >= 0) {
            $result = $this->_subtract($result[self::VALUE], false, $n, false);
        }
        return $result[self::VALUE];
    }
    /**
     * Montgomery Multiply
     *
     * Interleaves the montgomery reduction and long multiplication algorithms together as described in
     * {@link http://www.cacr.math.uwaterloo.ca/hac/about/chap14.pdf#page=13 HAC 14.36}
     *
     * @see _prepMontgomery()
     * @see _montgomery()
     * @access private
     * @param Array $x
     * @param Array $y
     * @param Array $m
     * @return Array
     */
    function _montgomeryMultiply($x, $y, $m)
    {
        $temp = $this->_multiply($x, false, $y, false);
        return $this->_montgomery($temp[self::VALUE], $m);
        // the following code, although not callable, can be run independently of the above code
        // although the above code performed better in my benchmarks the following could might
        // perform better under different circumstances. in lieu of deleting it it's just been
        // made uncallable
        static $cache = array(
            self::VARIABLE => array(),
            self::DATA => array()
        );
        if ( ($key = array_search($m, $cache[self::VARIABLE])) === false ) {
            $key = count($cache[self::VARIABLE]);
            $cache[self::VARIABLE][] = $m;
            $cache[self::DATA][] = $this->_modInverse67108864($m);
        }
        $n = max(count($x), count($y), count($m));
        $x = array_pad($x, $n, 0);
        $y = array_pad($y, $n, 0);
        $m = array_pad($m, $n, 0);
        $a = array(self::VALUE => $this->_array_repeat(0, $n + 1));
        for ($i = 0; $i < $n; ++$i) {
            $temp = $a[self::VALUE][0] + $x[$i] * $y[0];
            $temp = $temp - self::$baseFull * (self::$base === 26 ? intval($temp / 0x4000000) : ($temp >> 31));
            $temp = $temp * $cache[self::DATA][$key];
            $temp = $temp - self::$baseFull * (self::$base === 26 ? intval($temp / 0x4000000) : ($temp >> 31));
            $temp = $this->_add($this->_regularMultiply(array($x[$i]), $y), false, $this->_regularMultiply(array($temp), $m), false);
            $a = $this->_add($a[self::VALUE], false, $temp[self::VALUE], false);
            $a[self::VALUE] = array_slice($a[self::VALUE], 1);
        }
        if ($this->_compare($a[self::VALUE], false, $m, false) >= 0) {
            $a = $this->_subtract($a[self::VALUE], false, $m, false);
        }
        return $a[self::VALUE];
    }
    /**
     * Prepare a number for use in Montgomery Modular Reductions
     *
     * @see _montgomery()
     * @see _slidingWindow()
     * @access private
     * @param Array $x
     * @param Array $n
     * @return Array
     */
    function _prepMontgomery($x, $n)
    {
        $lhs = new static();
        $lhs->value = array_merge($this->_array_repeat(0, count($n)), $x);
        $rhs = new static();
        $rhs->value = $n;
        list(, $temp) = $lhs->divide($rhs);
        return $temp->value;
    }
    /**
     * Modular Inverse of a number mod 2**26 (eg. 67108864)
     *
     * Based off of the bnpInvDigit function implemented and justified in the following URL:
     *
     * {@link http://www-cs-students.stanford.edu/~tjw/jsbn/jsbn.js}
     *
     * The following URL provides more info:
     *
     * {@link http://groups.google.com/group/sci.crypt/msg/7a137205c1be7d85}
     *
     * As for why we do all the bitmasking...  strange things can happen when converting from floats to ints. For
     * instance, on some computers, var_dump((int) -4294967297) yields int(-1) and on others, it yields
     * int(-2147483648).  To avoid problems stemming from this, we use bitmasks to guarantee that ints aren't
     * auto-converted to floats.  The outermost bitmask is present because without it, there's no guarantee that
     * the "residue" returned would be the so-called "common residue".  We use fmod, in the last step, because the
     * maximum possible $x is 26 bits and the maximum $result is 16 bits.  Thus, we have to be able to handle up to
     * 40 bits, which only 64-bit floating points will support.
     *
     * Thanks to Pedro Gimeno Fortea for input!
     *
     * @see _montgomery()
     * @access private
     * @param Array $x
     * @return Integer
     */
    function _modInverse67108864($x) // 2**26 == 67,108,864
    {
        $x = -$x[0];
        $result = $x & 0x3; // x**-1 mod 2**2
        $result = ($result * (2 - $x * $result)) & 0xF; // x**-1 mod 2**4
        $result = ($result * (2 - ($x & 0xFF) * $result))  & 0xFF; // x**-1 mod 2**8
        $result = ($result * ((2 - ($x & 0xFFFF) * $result) & 0xFFFF)) & 0xFFFF; // x**-1 mod 2**16
        $result = fmod($result * (2 - fmod($x * $result, self::$baseFull)), self::$baseFull); // x**-1 mod 2**26
        return $result & self::$maxDigit;
    }
    /**
     * Calculates modular inverses.
     *
     * Say you have (30 mod 17 * x mod 17) mod 17 == 1.  x can be found using modular inverses.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger(30);
     *    $b = new \phpseclib\Math\BigInteger(17);
     *
     *    $c = $a->modInverse($b);
     *    echo $c->toString(); // outputs 4
     *
     *    echo "\r\n";
     *
     *    $d = $a->multiply($c);
     *    list(, $d) = $d->divide($b);
     *    echo $d; // outputs 1 (as per the definition of modular inverse)
     * ?>
     * </code>
     *
     * @param \phpseclib\Math\BigInteger $n
     * @return mixed false, if no modular inverse exists, \phpseclib\Math\BigInteger, otherwise.
     * @access public
     * @internal See {@link http://www.cacr.math.uwaterloo.ca/hac/about/chap14.pdf#page=21 HAC 14.64} for more information.
     */
    function modInverse($n)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                $temp = new static();
                $temp->value = gmp_invert($this->value, $n->value);
                return ( $temp->value === false ) ? false : $this->_normalize($temp);
        }
        static $zero, $one;
        if (!isset($zero)) {
            $zero = new static();
            $one = new static(1);
        }
        // $x mod -$n == $x mod $n.
        $n = $n->abs();
        if ($this->compare($zero) < 0) {
            $temp = $this->abs();
            $temp = $temp->modInverse($n);
            return $this->_normalize($n->subtract($temp));
        }
        extract($this->extendedGCD($n));
        if (!$gcd->equals($one)) {
            return false;
        }
        $x = $x->compare($zero) < 0 ? $x->add($n) : $x;
        return $this->compare($zero) < 0 ? $this->_normalize($n->subtract($x)) : $this->_normalize($x);
    }
    /**
     * Calculates the greatest common divisor and Bezout's identity.
     *
     * Say you have 693 and 609.  The GCD is 21.  Bezout's identity states that there exist integers x and y such that
     * 693*x + 609*y == 21.  In point of fact, there are actually an infinite number of x and y combinations and which
     * combination is returned is dependant upon which mode is in use.  See
     * {@link http://en.wikipedia.org/wiki/B%C3%A9zout%27s_identity Bezout's identity - Wikipedia} for more information.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger(693);
     *    $b = new \phpseclib\Math\BigInteger(609);
     *
     *    extract($a->extendedGCD($b));
     *
     *    echo $gcd->toString() . "\r\n"; // outputs 21
     *    echo $a->toString() * $x->toString() + $b->toString() * $y->toString(); // outputs 21
     * ?>
     * </code>
     *
     * @param \phpseclib\Math\BigInteger $n
     * @return \phpseclib\Math\BigInteger
     * @access public
     * @internal Calculates the GCD using the binary xGCD algorithim described in
     *    {@link http://www.cacr.math.uwaterloo.ca/hac/about/chap14.pdf#page=19 HAC 14.61}.  As the text above 14.61 notes,
     *    the more traditional algorithim requires "relatively costly multiple-precision divisions".
     */
    function extendedGCD($n)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                extract(gmp_gcdext($this->value, $n->value));
                return array(
                    'gcd' => $this->_normalize(new static($g)),
                    'x'   => $this->_normalize(new static($s)),
                    'y'   => $this->_normalize(new static($t))
                );
            case self::MODE_BCMATH:
                // it might be faster to use the binary xGCD algorithim here, as well, but (1) that algorithim works
                // best when the base is a power of 2 and (2) i don't think it'd make much difference, anyway.  as is,
                // the basic extended euclidean algorithim is what we're using.
                $u = $this->value;
                $v = $n->value;
                $a = '1';
                $b = '0';
                $c = '0';
                $d = '1';
                while (bccomp($v, '0', 0) != 0) {
                    $q = bcdiv($u, $v, 0);
                    $temp = $u;
                    $u = $v;
                    $v = bcsub($temp, bcmul($v, $q, 0), 0);
                    $temp = $a;
                    $a = $c;
                    $c = bcsub($temp, bcmul($a, $q, 0), 0);
                    $temp = $b;
                    $b = $d;
                    $d = bcsub($temp, bcmul($b, $q, 0), 0);
                }
                return array(
                    'gcd' => $this->_normalize(new static($u)),
                    'x'   => $this->_normalize(new static($a)),
                    'y'   => $this->_normalize(new static($b))
                );
        }
        $y = $n->copy();
        $x = $this->copy();
        $g = new static();
        $g->value = array(1);
        while ( !(($x->value[0] & 1)|| ($y->value[0] & 1)) ) {
            $x->_rshift(1);
            $y->_rshift(1);
            $g->_lshift(1);
        }
        $u = $x->copy();
        $v = $y->copy();
        $a = new static();
        $b = new static();
        $c = new static();
        $d = new static();
        $a->value = $d->value = $g->value = array(1);
        $b->value = $c->value = array();
        while ( !empty($u->value) ) {
            while ( !($u->value[0] & 1) ) {
                $u->_rshift(1);
                if ( (!empty($a->value) && ($a->value[0] & 1)) || (!empty($b->value) && ($b->value[0] & 1)) ) {
                    $a = $a->add($y);
                    $b = $b->subtract($x);
                }
                $a->_rshift(1);
                $b->_rshift(1);
            }
            while ( !($v->value[0] & 1) ) {
                $v->_rshift(1);
                if ( (!empty($d->value) && ($d->value[0] & 1)) || (!empty($c->value) && ($c->value[0] & 1)) ) {
                    $c = $c->add($y);
                    $d = $d->subtract($x);
                }
                $c->_rshift(1);
                $d->_rshift(1);
            }
            if ($u->compare($v) >= 0) {
                $u = $u->subtract($v);
                $a = $a->subtract($c);
                $b = $b->subtract($d);
            } else {
                $v = $v->subtract($u);
                $c = $c->subtract($a);
                $d = $d->subtract($b);
            }
        }
        return array(
            'gcd' => $this->_normalize($g->multiply($v)),
            'x'   => $this->_normalize($c),
            'y'   => $this->_normalize($d)
        );
    }
    /**
     * Calculates the greatest common divisor
     *
     * Say you have 693 and 609.  The GCD is 21.
     *
     * Here's an example:
     * <code>
     * <?php
     *    $a = new \phpseclib\Math\BigInteger(693);
     *    $b = new \phpseclib\Math\BigInteger(609);
     *
     *    $gcd = a->extendedGCD($b);
     *
     *    echo $gcd->toString() . "\r\n"; // outputs 21
     * ?>
     * </code>
     *
     * @param \phpseclib\Math\BigInteger $n
     * @return \phpseclib\Math\BigInteger
     * @access public
     */
    function gcd($n)
    {
        extract($this->extendedGCD($n));
        return $gcd;
    }
    /**
     * Absolute value.
     *
     * @return \phpseclib\Math\BigInteger
     * @access public
     */
    function abs()
    {
        $temp = new static();
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                $temp->value = gmp_abs($this->value);
                break;
            case self::MODE_BCMATH:
                $temp->value = (bccomp($this->value, '0', 0) < 0) ? substr($this->value, 1) : $this->value;
                break;
            default:
                $temp->value = $this->value;
        }
        return $temp;
    }
    /**
     * Compares two numbers.
     *
     * Although one might think !$x->compare($y) means $x != $y, it, in fact, means the opposite.  The reason for this is
     * demonstrated thusly:
     *
     * $x  > $y: $x->compare($y)  > 0
     * $x  < $y: $x->compare($y)  < 0
     * $x == $y: $x->compare($y) == 0
     *
     * Note how the same comparison operator is used.  If you want to test for equality, use $x->equals($y).
     *
     * @param \phpseclib\Math\BigInteger $y
     * @return Integer < 0 if $this is less than $y; > 0 if $this is greater than $y, and 0 if they are equal.
     * @access public
     * @see equals()
     * @internal Could return $this->subtract($x), but that's not as fast as what we do do.
     */
    function compare($y)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                return gmp_cmp($this->value, $y->value);
            case self::MODE_BCMATH:
                return bccomp($this->value, $y->value, 0);
        }
        return $this->_compare($this->value, $this->is_negative, $y->value, $y->is_negative);
    }
    /**
     * Compares two numbers.
     *
     * @param Array $x_value
     * @param Boolean $x_negative
     * @param Array $y_value
     * @param Boolean $y_negative
     * @return Integer
     * @see compare()
     * @access private
     */
    function _compare($x_value, $x_negative, $y_value, $y_negative)
    {
        if ( $x_negative != $y_negative ) {
            return ( !$x_negative && $y_negative ) ? 1 : -1;
        }
        $result = $x_negative ? -1 : 1;
        if ( count($x_value) != count($y_value) ) {
            return ( count($x_value) > count($y_value) ) ? $result : -$result;
        }
        $size = max(count($x_value), count($y_value));
        $x_value = array_pad($x_value, $size, 0);
        $y_value = array_pad($y_value, $size, 0);
        for ($i = count($x_value) - 1; $i >= 0; --$i) {
            if ($x_value[$i] != $y_value[$i]) {
                return ( $x_value[$i] > $y_value[$i] ) ? $result : -$result;
            }
        }
        return 0;
    }
    /**
     * Tests the equality of two numbers.
     *
     * If you need to see if one number is greater than or less than another number, use BigInteger::compare()
     *
     * @param \phpseclib\Math\BigInteger $x
     * @return Boolean
     * @access public
     * @see compare()
     */
    function equals($x)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                return gmp_cmp($this->value, $x->value) == 0;
            default:
                return $this->value === $x->value && $this->is_negative == $x->is_negative;
        }
    }
    /**
     * Set Precision
     *
     * Some bitwise operations give different results depending on the precision being used.  Examples include left
     * shift, not, and rotates.
     *
     * @param Integer $bits
     * @access public
     */
    function setPrecision($bits)
    {
        $this->precision = $bits;
        if ( MATH_BIGINTEGER_MODE != self::MODE_BCMATH ) {
            $this->bitmask = new static(chr((1 << ($bits & 0x7)) - 1) . str_repeat(chr(0xFF), $bits >> 3), 256);
        } else {
            $this->bitmask = new static(bcpow('2', $bits, 0));
        }
        $temp = $this->_normalize($this);
        $this->value = $temp->value;
    }
    /**
     * Logical And
     *
     * @param \phpseclib\Math\BigInteger $x
     * @access public
     * @internal Implemented per a request by Lluis Pamies i Juarez <lluis _a_ pamies.cat>
     * @return \phpseclib\Math\BigInteger
     */
    function bitwise_and($x)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                $temp = new static();
                $temp->value = gmp_and($this->value, $x->value);
                return $this->_normalize($temp);
            case self::MODE_BCMATH:
                $left = $this->toBytes();
                $right = $x->toBytes();
                $length = max(strlen($left), strlen($right));
                $left = str_pad($left, $length, chr(0), STR_PAD_LEFT);
                $right = str_pad($right, $length, chr(0), STR_PAD_LEFT);
                return $this->_normalize(new static($left & $right, 256));
        }
        $result = $this->copy();
        $length = min(count($x->value), count($this->value));
        $result->value = array_slice($result->value, 0, $length);
        for ($i = 0; $i < $length; ++$i) {
            $result->value[$i]&= $x->value[$i];
        }
        return $this->_normalize($result);
    }
    /**
     * Logical Or
     *
     * @param \phpseclib\Math\BigInteger $x
     * @access public
     * @internal Implemented per a request by Lluis Pamies i Juarez <lluis _a_ pamies.cat>
     * @return \phpseclib\Math\BigInteger
     */
    function bitwise_or($x)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                $temp = new static();
                $temp->value = gmp_or($this->value, $x->value);
                return $this->_normalize($temp);
            case self::MODE_BCMATH:
                $left = $this->toBytes();
                $right = $x->toBytes();
                $length = max(strlen($left), strlen($right));
                $left = str_pad($left, $length, chr(0), STR_PAD_LEFT);
                $right = str_pad($right, $length, chr(0), STR_PAD_LEFT);
                return $this->_normalize(new static($left | $right, 256));
        }
        $length = max(count($this->value), count($x->value));
        $result = $this->copy();
        $result->value = array_pad($result->value, $length, 0);
        $x->value = array_pad($x->value, $length, 0);
        for ($i = 0; $i < $length; ++$i) {
            $result->value[$i]|= $x->value[$i];
        }
        return $this->_normalize($result);
    }
    /**
     * Logical Exclusive-Or
     *
     * @param \phpseclib\Math\BigInteger $x
     * @access public
     * @internal Implemented per a request by Lluis Pamies i Juarez <lluis _a_ pamies.cat>
     * @return \phpseclib\Math\BigInteger
     */
    function bitwise_xor($x)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                $temp = new static();
                $temp->value = gmp_xor($this->value, $x->value);
                return $this->_normalize($temp);
            case self::MODE_BCMATH:
                $left = $this->toBytes();
                $right = $x->toBytes();
                $length = max(strlen($left), strlen($right));
                $left = str_pad($left, $length, chr(0), STR_PAD_LEFT);
                $right = str_pad($right, $length, chr(0), STR_PAD_LEFT);
                return $this->_normalize(new static($left ^ $right, 256));
        }
        $length = max(count($this->value), count($x->value));
        $result = $this->copy();
        $result->value = array_pad($result->value, $length, 0);
        $x->value = array_pad($x->value, $length, 0);
        for ($i = 0; $i < $length; ++$i) {
            $result->value[$i]^= $x->value[$i];
        }
        return $this->_normalize($result);
    }
    /**
     * Logical Not
     *
     * @access public
     * @internal Implemented per a request by Lluis Pamies i Juarez <lluis _a_ pamies.cat>
     * @return \phpseclib\Math\BigInteger
     */
    function bitwise_not()
    {
        // calculuate "not" without regard to $this->precision
        // (will always result in a smaller number.  ie. ~1 isn't 1111 1110 - it's 0)
        $temp = $this->toBytes();
        $pre_msb = decbin(ord($temp[0]));
        $temp = ~$temp;
        $msb = decbin(ord($temp[0]));
        if (strlen($msb) == 8) {
            $msb = substr($msb, strpos($msb, '0'));
        }
        $temp[0] = chr(bindec($msb));
        // see if we need to add extra leading 1's
        $current_bits = strlen($pre_msb) + 8 * strlen($temp) - 8;
        $new_bits = $this->precision - $current_bits;
        if ($new_bits <= 0) {
            return $this->_normalize(new static($temp, 256));
        }
        // generate as many leading 1's as we need to.
        $leading_ones = chr((1 << ($new_bits & 0x7)) - 1) . str_repeat(chr(0xFF), $new_bits >> 3);
        $this->_base256_lshift($leading_ones, $current_bits);
        $temp = str_pad($temp, strlen($leading_ones), chr(0), STR_PAD_LEFT);
        return $this->_normalize(new static($leading_ones | $temp, 256));
    }
    /**
     * Logical Right Shift
     *
     * Shifts BigInteger's by $shift bits, effectively dividing by 2**$shift.
     *
     * @param Integer $shift
     * @return \phpseclib\Math\BigInteger
     * @access public
     * @internal The only version that yields any speed increases is the internal version.
     */
    function bitwise_rightShift($shift)
    {
        $temp = new static();
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                static $two;
                if (!isset($two)) {
                    $two = gmp_init('2');
                }
                $temp->value = gmp_div_q($this->value, gmp_pow($two, $shift));
                break;
            case self::MODE_BCMATH:
                $temp->value = bcdiv($this->value, bcpow('2', $shift, 0), 0);
                break;
            default: // could just replace _lshift with this, but then all _lshift() calls would need to be rewritten
                     // and I don't want to do that...
                $temp->value = $this->value;
                $temp->_rshift($shift);
        }
        return $this->_normalize($temp);
    }
    /**
     * Logical Left Shift
     *
     * Shifts BigInteger's by $shift bits, effectively multiplying by 2**$shift.
     *
     * @param Integer $shift
     * @return \phpseclib\Math\BigInteger
     * @access public
     * @internal The only version that yields any speed increases is the internal version.
     */
    function bitwise_leftShift($shift)
    {
        $temp = new static();
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                static $two;
                if (!isset($two)) {
                    $two = gmp_init('2');
                }
                $temp->value = gmp_mul($this->value, gmp_pow($two, $shift));
                break;
            case self::MODE_BCMATH:
                $temp->value = bcmul($this->value, bcpow('2', $shift, 0), 0);
                break;
            default: // could just replace _rshift with this, but then all _lshift() calls would need to be rewritten
                     // and I don't want to do that...
                $temp->value = $this->value;
                $temp->_lshift($shift);
        }
        return $this->_normalize($temp);
    }
    /**
     * Logical Left Rotate
     *
     * Instead of the top x bits being dropped they're appended to the shifted bit string.
     *
     * @param Integer $shift
     * @return \phpseclib\Math\BigInteger
     * @access public
     */
    function bitwise_leftRotate($shift)
    {
        $bits = $this->toBytes();
        if ($this->precision > 0) {
            $precision = $this->precision;
            if ( MATH_BIGINTEGER_MODE == self::MODE_BCMATH ) {
                $mask = $this->bitmask->subtract(new static(1));
                $mask = $mask->toBytes();
            } else {
                $mask = $this->bitmask->toBytes();
            }
        } else {
            $temp = ord($bits[0]);
            for ($i = 0; $temp >> $i; ++$i);
            $precision = 8 * strlen($bits) - 8 + $i;
            $mask = chr((1 << ($precision & 0x7)) - 1) . str_repeat(chr(0xFF), $precision >> 3);
        }
        if ($shift < 0) {
            $shift+= $precision;
        }
        $shift%= $precision;
        if (!$shift) {
            return $this->copy();
        }
        $left = $this->bitwise_leftShift($shift);
        $left = $left->bitwise_and(new static($mask, 256));
        $right = $this->bitwise_rightShift($precision - $shift);
        $result = MATH_BIGINTEGER_MODE != self::MODE_BCMATH ? $left->bitwise_or($right) : $left->add($right);
        return $this->_normalize($result);
    }
    /**
     * Logical Right Rotate
     *
     * Instead of the bottom x bits being dropped they're prepended to the shifted bit string.
     *
     * @param Integer $shift
     * @return \phpseclib\Math\BigInteger
     * @access public
     */
    function bitwise_rightRotate($shift)
    {
        return $this->bitwise_leftRotate(-$shift);
    }
    /**
     * Generates a random BigInteger
     *
     * Byte length is equal to $length. Uses \phpseclib\Crypt\Random if it's loaded and mt_rand if it's not.
     *
     * @param Integer $length
     * @return \phpseclib\Math\BigInteger
     * @access private
     */
    function _random_number_helper($size)
    {
        if (class_exists('\phpseclib\Crypt\Random')) {
            $random = Random::string($size);
        } else {
            $random = '';
            if ($size & 1) {
                $random.= chr(mt_rand(0, 255));
            }
            $blocks = $size >> 1;
            for ($i = 0; $i < $blocks; ++$i) {
                // mt_rand(-2147483648, 0x7FFFFFFF) always produces -2147483648 on some systems
                $random.= pack('n', mt_rand(0, 0xFFFF));
            }
        }
        return new static($random, 256);
    }
    /**
     * Generate a random number
     *
     * Returns a random number between $min and $max where $min and $max
     * can be defined using one of the two methods:
     *
     * $min->random($max)
     * $max->random($min)
     *
     * @param \phpseclib\Math\BigInteger $arg1
     * @param optional \phpseclib\Math\BigInteger $arg2
     * @return \phpseclib\Math\BigInteger
     * @access public
     * @internal The API for creating random numbers used to be $a->random($min, $max), where $a was a BigInteger object.
     *           That method is still supported for BC purposes.
     */
    function random($arg1, $arg2 = false)
    {
        if ($arg1 === false) {
            return false;
        }
        if ($arg2 === false) {
            $max = $arg1;
            $min = $this;
        } else {
            $min = $arg1;
            $max = $arg2;
        }
        $compare = $max->compare($min);
        if (!$compare) {
            return $this->_normalize($min);
        } else if ($compare < 0) {
            // if $min is bigger then $max, swap $min and $max
            $temp = $max;
            $max = $min;
            $min = $temp;
        }
        static $one;
        if (!isset($one)) {
            $one = new static(1);
        }
        $max = $max->subtract($min->subtract($one));
        $size = strlen(ltrim($max->toBytes(), chr(0)));
        /*
            doing $random % $max doesn't work because some numbers will be more likely to occur than others.
            eg. if $max is 140 and $random's max is 255 then that'd mean both $random = 5 and $random = 145
            would produce 5 whereas the only value of random that could produce 139 would be 139. ie.
            not all numbers would be equally likely. some would be more likely than others.
            creating a whole new random number until you find one that is within the range doesn't work
            because, for sufficiently small ranges, the likelihood that you'd get a number within that range
            would be pretty small. eg. with $random's max being 255 and if your $max being 1 the probability
            would be pretty high that $random would be greater than $max.
            phpseclib works around this using the technique described here:
            http://crypto.stackexchange.com/questions/5708/creating-a-small-number-from-a-cryptographically-secure-random-string
        */
        $random_max = new static(chr(1) . str_repeat("\0", $size), 256);
        $random = $this->_random_number_helper($size);
        list($max_multiple) = $random_max->divide($max);
        $max_multiple = $max_multiple->multiply($max);
        while ($random->compare($max_multiple) >= 0) {
            $random = $random->subtract($max_multiple);
            $random_max = $random_max->subtract($max_multiple);
            $random = $random->bitwise_leftShift(8);
            $random = $random->add($this->_random_number_helper(1));
            $random_max = $random_max->bitwise_leftShift(8);
            list($max_multiple) = $random_max->divide($max);
            $max_multiple = $max_multiple->multiply($max);
        }
        list(, $random) = $random->divide($max);
        return $this->_normalize($random->add($min));
    }
    /**
     * Generate a random prime number.
     *
     * If there's not a prime within the given range, false will be returned.  If more than $timeout seconds have elapsed,
     * give up and return false.
     *
     * @param \phpseclib\Math\BigInteger $arg1
     * @param optional \phpseclib\Math\BigInteger $arg2
     * @param optional Integer $timeout
     * @return Mixed
     * @access public
     * @internal See {@link http://www.cacr.math.uwaterloo.ca/hac/about/chap4.pdf#page=15 HAC 4.44}.
     */
    function randomPrime($arg1, $arg2 = false, $timeout = false)
    {
        if ($arg1 === false) {
            return false;
        }
        if ($arg2 === false) {
            $max = $arg1;
            $min = $this;
        } else {
            $min = $arg1;
            $max = $arg2;
        }
        $compare = $max->compare($min);
        if (!$compare) {
            return $min->isPrime() ? $min : false;
        } else if ($compare < 0) {
            // if $min is bigger then $max, swap $min and $max
            $temp = $max;
            $max = $min;
            $min = $temp;
        }
        static $one, $two;
        if (!isset($one)) {
            $one = new static(1);
            $two = new static(2);
        }
        $start = time();
        $x = $this->random($min, $max);
        // gmp_nextprime() requires PHP 5 >= 5.2.0 per <http://php.net/gmp-nextprime>.
        if ( MATH_BIGINTEGER_MODE == self::MODE_GMP && function_exists('gmp_nextprime') ) {
            $p = new static();
            $p->value = gmp_nextprime($x->value);
            if ($p->compare($max) <= 0) {
                return $p;
            }
            if (!$min->equals($x)) {
                $x = $x->subtract($one);
            }
            return $x->randomPrime($min, $x);
        }
        if ($x->equals($two)) {
            return $x;
        }
        $x->_make_odd();
        if ($x->compare($max) > 0) {
            // if $x > $max then $max is even and if $min == $max then no prime number exists between the specified range
            if ($min->equals($max)) {
                return false;
            }
            $x = $min->copy();
            $x->_make_odd();
        }
        $initial_x = $x->copy();
        while (true) {
            if ($timeout !== false && time() - $start > $timeout) {
                return false;
            }
            if ($x->isPrime()) {
                return $x;
            }
            $x = $x->add($two);
            if ($x->compare($max) > 0) {
                $x = $min->copy();
                if ($x->equals($two)) {
                    return $x;
                }
                $x->_make_odd();
            }
            if ($x->equals($initial_x)) {
                return false;
            }
        }
    }
    /**
     * Make the current number odd
     *
     * If the current number is odd it'll be unchanged.  If it's even, one will be added to it.
     *
     * @see randomPrime()
     * @access private
     */
    function _make_odd()
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                gmp_setbit($this->value, 0);
                break;
            case self::MODE_BCMATH:
                if ($this->value[strlen($this->value) - 1] % 2 == 0) {
                    $this->value = bcadd($this->value, '1');
                }
                break;
            default:
                $this->value[0] |= 1;
        }
    }
    /**
     * Checks a numer to see if it's prime
     *
     * Assuming the $t parameter is not set, this function has an error rate of 2**-80.  The main motivation for the
     * $t parameter is distributability.  BigInteger::randomPrime() can be distributed across multiple pageloads
     * on a website instead of just one.
     *
     * @param optional \phpseclib\Math\BigInteger $t
     * @return Boolean
     * @access public
     * @internal Uses the
     *     {@link http://en.wikipedia.org/wiki/Miller%E2%80%93Rabin_primality_test Miller-Rabin primality test}.  See
     *     {@link http://www.cacr.math.uwaterloo.ca/hac/about/chap4.pdf#page=8 HAC 4.24}.
     */
    function isPrime($t = false)
    {
        $length = strlen($this->toBytes());
        if (!$t) {
            // see HAC 4.49 "Note (controlling the error probability)"
            // @codingStandardsIgnoreStart
                 if ($length >= 163) { $t =  2; } // floor(1300 / 8)
            else if ($length >= 106) { $t =  3; } // floor( 850 / 8)
            else if ($length >= 81 ) { $t =  4; } // floor( 650 / 8)
            else if ($length >= 68 ) { $t =  5; } // floor( 550 / 8)
            else if ($length >= 56 ) { $t =  6; } // floor( 450 / 8)
            else if ($length >= 50 ) { $t =  7; } // floor( 400 / 8)
            else if ($length >= 43 ) { $t =  8; } // floor( 350 / 8)
            else if ($length >= 37 ) { $t =  9; } // floor( 300 / 8)
            else if ($length >= 31 ) { $t = 12; } // floor( 250 / 8)
            else if ($length >= 25 ) { $t = 15; } // floor( 200 / 8)
            else if ($length >= 18 ) { $t = 18; } // floor( 150 / 8)
            else                     { $t = 27; }
            // @codingStandardsIgnoreEnd
        }
        // ie. gmp_testbit($this, 0)
        // ie. isEven() or !isOdd()
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                return gmp_prob_prime($this->value, $t) != 0;
            case self::MODE_BCMATH:
                if ($this->value === '2') {
                    return true;
                }
                if ($this->value[strlen($this->value) - 1] % 2 == 0) {
                    return false;
                }
                break;
            default:
                if ($this->value == array(2)) {
                    return true;
                }
                if (~$this->value[0] & 1) {
                    return false;
                }
        }
        static $primes, $zero, $one, $two;
        if (!isset($primes)) {
            $primes = array(
                3,    5,    7,    11,   13,   17,   19,   23,   29,   31,   37,   41,   43,   47,   53,   59,
                61,   67,   71,   73,   79,   83,   89,   97,   101,  103,  107,  109,  113,  127,  131,  137,
                139,  149,  151,  157,  163,  167,  173,  179,  181,  191,  193,  197,  199,  211,  223,  227,
                229,  233,  239,  241,  251,  257,  263,  269,  271,  277,  281,  283,  293,  307,  311,  313,
                317,  331,  337,  347,  349,  353,  359,  367,  373,  379,  383,  389,  397,  401,  409,  419,
                421,  431,  433,  439,  443,  449,  457,  461,  463,  467,  479,  487,  491,  499,  503,  509,
                521,  523,  541,  547,  557,  563,  569,  571,  577,  587,  593,  599,  601,  607,  613,  617,
                619,  631,  641,  643,  647,  653,  659,  661,  673,  677,  683,  691,  701,  709,  719,  727,
                733,  739,  743,  751,  757,  761,  769,  773,  787,  797,  809,  811,  821,  823,  827,  829,
                839,  853,  857,  859,  863,  877,  881,  883,  887,  907,  911,  919,  929,  937,  941,  947,
                953,  967,  971,  977,  983,  991,  997
            );
            if ( MATH_BIGINTEGER_MODE != self::MODE_INTERNAL ) {
                for ($i = 0; $i < count($primes); ++$i) {
                    $primes[$i] = new static($primes[$i]);
                }
            }
            $zero = new static();
            $one = new static(1);
            $two = new static(2);
        }
        if ($this->equals($one)) {
            return false;
        }
        // see HAC 4.4.1 "Random search for probable primes"
        if ( MATH_BIGINTEGER_MODE != self::MODE_INTERNAL ) {
            foreach ($primes as $prime) {
                list(, $r) = $this->divide($prime);
                if ($r->equals($zero)) {
                    return $this->equals($prime);
                }
            }
        } else {
            $value = $this->value;
            foreach ($primes as $prime) {
                list(, $r) = $this->_divide_digit($value, $prime);
                if (!$r) {
                    return count($value) == 1 && $value[0] == $prime;
                }
            }
        }
        $n   = $this->copy();
        $n_1 = $n->subtract($one);
        $n_2 = $n->subtract($two);
        $r = $n_1->copy();
        $r_value = $r->value;
        // ie. $s = gmp_scan1($n, 0) and $r = gmp_div_q($n, gmp_pow(gmp_init('2'), $s));
        if ( MATH_BIGINTEGER_MODE == self::MODE_BCMATH ) {
            $s = 0;
            // if $n was 1, $r would be 0 and this would be an infinite loop, hence our $this->equals($one) check earlier
            while ($r->value[strlen($r->value) - 1] % 2 == 0) {
                $r->value = bcdiv($r->value, '2', 0);
                ++$s;
            }
        } else {
            for ($i = 0, $r_length = count($r_value); $i < $r_length; ++$i) {
                $temp = ~$r_value[$i] & 0xFFFFFF;
                for ($j = 1; ($temp >> $j) & 1; ++$j);
                if ($j != 25) {
                    break;
                }
            }
            $s = 26 * $i + $j - 1;
            $r->_rshift($s);
        }
        for ($i = 0; $i < $t; ++$i) {
            $a = $this->random($two, $n_2);
            $y = $a->modPow($r, $n);
            if (!$y->equals($one) && !$y->equals($n_1)) {
                for ($j = 1; $j < $s && !$y->equals($n_1); ++$j) {
                    $y = $y->modPow($two, $n);
                    if ($y->equals($one)) {
                        return false;
                    }
                }
                if (!$y->equals($n_1)) {
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * Logical Left Shift
     *
     * Shifts BigInteger's by $shift bits.
     *
     * @param Integer $shift
     * @access private
     */
    function _lshift($shift)
    {
        if ( $shift == 0 ) {
            return;
        }
        $num_digits = (int) ($shift / self::$base);
        $shift %= self::$base;
        $shift = 1 << $shift;
        $carry = 0;
        for ($i = 0; $i < count($this->value); ++$i) {
            $temp = $this->value[$i] * $shift + $carry;
            $carry = self::$base === 26 ? intval($temp / 0x4000000) : ($temp >> 31);
            $this->value[$i] = (int) ($temp - $carry * self::$baseFull);
        }
        if ( $carry ) {
            $this->value[count($this->value)] = $carry;
        }
        while ($num_digits--) {
            array_unshift($this->value, 0);
        }
    }
    /**
     * Logical Right Shift
     *
     * Shifts BigInteger's by $shift bits.
     *
     * @param Integer $shift
     * @access private
     */
    function _rshift($shift)
    {
        if ($shift == 0) {
            return;
        }
        $num_digits = (int) ($shift / self::$base);
        $shift %= self::$base;
        $carry_shift = self::$base - $shift;
        $carry_mask = (1 << $shift) - 1;
        if ( $num_digits ) {
            $this->value = array_slice($this->value, $num_digits);
        }
        $carry = 0;
        for ($i = count($this->value) - 1; $i >= 0; --$i) {
            $temp = $this->value[$i] >> $shift | $carry;
            $carry = ($this->value[$i] & $carry_mask) << $carry_shift;
            $this->value[$i] = $temp;
        }
        $this->value = $this->_trim($this->value);
    }
    /**
     * Normalize
     *
     * Removes leading zeros and truncates (if necessary) to maintain the appropriate precision
     *
     * @param \phpseclib\Math\BigInteger
     * @return \phpseclib\Math\BigInteger
     * @see _trim()
     * @access private
     */
    function _normalize($result)
    {
        $result->precision = $this->precision;
        $result->bitmask = $this->bitmask;
        switch ( MATH_BIGINTEGER_MODE ) {
            case self::MODE_GMP:
                if (!empty($result->bitmask->value)) {
                    $result->value = gmp_and($result->value, $result->bitmask->value);
                }
                return $result;
            case self::MODE_BCMATH:
                if (!empty($result->bitmask->value)) {
                    $result->value = bcmod($result->value, $result->bitmask->value);
                }
                return $result;
        }
        $value = &$result->value;
        if ( !count($value) ) {
            return $result;
        }
        $value = $this->_trim($value);
        if (!empty($result->bitmask->value)) {
            $length = min(count($value), count($this->bitmask->value));
            $value = array_slice($value, 0, $length);
            for ($i = 0; $i < $length; ++$i) {
                $value[$i] = $value[$i] & $this->bitmask->value[$i];
            }
        }
        return $result;
    }
    /**
     * Trim
     *
     * Removes leading zeros
     *
     * @param Array $value
     * @return \phpseclib\Math\BigInteger
     * @access private
     */
    function _trim($value)
    {
        for ($i = count($value) - 1; $i >= 0; --$i) {
            if ( $value[$i] ) {
                break;
            }
            unset($value[$i]);
        }
        return $value;
    }
    /**
     * Array Repeat
     *
     * @param $input Array
     * @param $multiplier mixed
     * @return Array
     * @access private
     */
    function _array_repeat($input, $multiplier)
    {
        return ($multiplier) ? array_fill(0, $multiplier, $input) : array();
    }
    /**
     * Logical Left Shift
     *
     * Shifts binary strings $shift bits, essentially multiplying by 2**$shift.
     *
     * @param $x String
     * @param $shift Integer
     * @return String
     * @access private
     */
    function _base256_lshift(&$x, $shift)
    {
        if ($shift == 0) {
            return;
        }
        $num_bytes = $shift >> 3; // eg. floor($shift/8)
        $shift &= 7; // eg. $shift % 8
        $carry = 0;
        for ($i = strlen($x) - 1; $i >= 0; --$i) {
            $temp = ord($x[$i]) << $shift | $carry;
            $x[$i] = chr($temp);
            $carry = $temp >> 8;
        }
        $carry = ($carry != 0) ? chr($carry) : '';
        $x = $carry . $x . str_repeat(chr(0), $num_bytes);
    }
    /**
     * Logical Right Shift
     *
     * Shifts binary strings $shift bits, essentially dividing by 2**$shift and returning the remainder.
     *
     * @param $x String
     * @param $shift Integer
     * @return String
     * @access private
     */
    function _base256_rshift(&$x, $shift)
    {
        if ($shift == 0) {
            $x = ltrim($x, chr(0));
            return '';
        }
        $num_bytes = $shift >> 3; // eg. floor($shift/8)
        $shift &= 7; // eg. $shift % 8
        $remainder = '';
        if ($num_bytes) {
            $start = $num_bytes > strlen($x) ? -strlen($x) : -$num_bytes;
            $remainder = substr($x, $start);
            $x = substr($x, 0, -$num_bytes);
        }
        $carry = 0;
        $carry_shift = 8 - $shift;
        for ($i = 0; $i < strlen($x); ++$i) {
            $temp = (ord($x[$i]) >> $shift) | $carry;
            $carry = (ord($x[$i]) << $carry_shift) & 0xFF;
            $x[$i] = chr($temp);
        }
        $x = ltrim($x, chr(0));
        $remainder = chr($carry >> $carry_shift) . $remainder;
        return ltrim($remainder, chr(0));
    }
    // one quirk about how the following functions are implemented is that PHP defines N to be an unsigned long
    // at 32-bits, while java's longs are 64-bits.
    /**
     * Converts 32-bit integers to bytes.
     *
     * @param Integer $x
     * @return String
     * @access private
     */
    function _int2bytes($x)
    {
        return ltrim(pack('N', $x), chr(0));
    }
    /**
     * Converts bytes to 32-bit integers
     *
     * @param String $x
     * @return Integer
     * @access private
     */
    function _bytes2int($x)
    {
        $temp = unpack('Nint', str_pad($x, 4, chr(0), STR_PAD_LEFT));
        return $temp['int'];
    }
    /**
     * DER-encode an integer
     *
     * The ability to DER-encode integers is needed to create RSA public keys for use with OpenSSL
     *
     * @see modPow()
     * @access private
     * @param Integer $length
     * @return String
     */
    function _encodeASN1Length($length)
    {
        if ($length <= 0x7F) {
            return chr($length);
        }
        $temp = ltrim(pack('N', $length), chr(0));
        return pack('Ca*', 0x80 | strlen($temp), $temp);
    }
    /**
     * Single digit division
     *
     * Even if int64 is being used the division operator will return a float64 value
     * if the dividend is not evenly divisible by the divisor. Since a float64 doesn't
     * have the precision of int64 this is a problem so, when int64 is being used,
     * we'll guarantee that the dividend is divisible by first subtracting the remainder.
     *
     * @access private
     * @param Integer $x
     * @param Integer $y
     * @return Integer
     */
    function _safe_divide($x, $y)
    {
        if (self::$base === 26) {
            return (int) ($x / $y);
        }
        // self::$base === 31
        return ($x - ($x % $y)) / $y;
    }
}



  
class Hash
{
    /**#@+
     * @access private
     * @see \phpseclib\Crypt\Hash::__construct()
     */
    /**
     * Toggles the internal implementation
     */
    const MODE_INTERNAL = 1;
    /**
     * Toggles the mhash() implementation, which has been deprecated on PHP 5.3.0+.
    */
    const MODE_MHASH = 2;
    /**
     * Toggles the hash() implementation, which works on PHP 5.1.2+.
    */
    const MODE_HASH = 3;
    /**#@-*/
    /**
     * Hash Parameter
     *
     * @see \phpseclib\Crypt\Hash::setHash()
     * @var Integer
     * @access private
     */
    var $hashParam;
    /**
     * Byte-length of compression blocks / key (Internal HMAC)
     *
     * @see \phpseclib\Crypt\Hash::setAlgorithm()
     * @var Integer
     * @access private
     */
    var $b;
    /**
     * Byte-length of hash output (Internal HMAC)
     *
     * @see \phpseclib\Crypt\Hash::setHash()
     * @var Integer
     * @access private
     */
    var $l = false;
    /**
     * Hash Algorithm
     *
     * @see \phpseclib\Crypt\Hash::setHash()
     * @var String
     * @access private
     */
    var $hash;
    /**
     * Key
     *
     * @see \phpseclib\Crypt\Hash::setKey()
     * @var String
     * @access private
     */
    var $key = false;
    /**
     * Outer XOR (Internal HMAC)
     *
     * @see \phpseclib\Crypt\Hash::setKey()
     * @var String
     * @access private
     */
    var $opad;
    /**
     * Inner XOR (Internal HMAC)
     *
     * @see \phpseclib\Crypt\Hash::setKey()
     * @var String
     * @access private
     */
    var $ipad;
    /**
     * Default Constructor.
     *
     * @param optional String $hash
     * @return \phpseclib\Crypt\Hash
     * @access public
     */
    function __construct($hash = 'sha1')
    {
        if ( !defined('CRYPT_HASH_MODE') ) {
            switch (true) {
                case extension_loaded('hash'):
                    define('CRYPT_HASH_MODE', self::MODE_HASH);
                    break;
                case extension_loaded('mhash'):
                    define('CRYPT_HASH_MODE', self::MODE_MHASH);
                    break;
                default:
                    define('CRYPT_HASH_MODE', self::MODE_INTERNAL);
            }
        }
        $this->setHash($hash);
    }
    /**
     * Sets the key for HMACs
     *
     * Keys can be of any length.
     *
     * @access public
     * @param optional String $key
     */
    function setKey($key = false)
    {
        $this->key = $key;
    }
    /**
     * Gets the hash function.
     *
     * As set by the constructor or by the setHash() method.
     *
     * @access public
     * @return String
     */
    function getHash()
    {
        return $this->hashParam;
    }
    /**
     * Sets the hash function.
     *
     * @access public
     * @param String $hash
     */
    function setHash($hash)
    {
        $this->hashParam = $hash = strtolower($hash);
        switch ($hash) {
            case 'md5-96':
            case 'sha1-96':
            case 'sha256-96':
            case 'sha512-96':
                $hash = substr($hash, 0, -3);
                $this->l = 12; // 96 / 8 = 12
                break;
            case 'md2':
            case 'md5':
                $this->l = 16;
                break;
            case 'sha1':
                $this->l = 20;
                break;
            case 'sha256':
                $this->l = 32;
                break;
            case 'sha384':
                $this->l = 48;
                break;
            case 'sha512':
                $this->l = 64;
        }
        switch ($hash) {
            case 'md2':
                $mode = CRYPT_HASH_MODE == self::MODE_HASH && in_array('md2', hash_algos()) ?
                    self::MODE_HASH : self::MODE_INTERNAL;
                break;
            case 'sha384':
            case 'sha512':
                $mode = CRYPT_HASH_MODE == self::MODE_MHASH ? self::MODE_INTERNAL : CRYPT_HASH_MODE;
                break;
            default:
                $mode = CRYPT_HASH_MODE;
        }
        switch ( $mode ) {
            case self::MODE_MHASH:
                switch ($hash) {
                    case 'md5':
                        $this->hash = MHASH_MD5;
                        break;
                    case 'sha256':
                        $this->hash = MHASH_SHA256;
                        break;
                    case 'sha1':
                    default:
                        $this->hash = MHASH_SHA1;
                }
                return;
            case self::MODE_HASH:
                switch ($hash) {
                    case 'md5':
                        $this->hash = 'md5';
                        return;
                    case 'md2':
                    case 'sha256':
                    case 'sha384':
                    case 'sha512':
                        $this->hash = $hash;
                        return;
                    case 'sha1':
                    default:
                        $this->hash = 'sha1';
                }
                return;
        }
        switch ($hash) {
            case 'md2':
                 $this->b = 16;
                 $this->hash = array($this, '_md2');
                 break;
            case 'md5':
                 $this->b = 64;
                 $this->hash = array($this, '_md5');
                 break;
            case 'sha256':
                 $this->b = 64;
                 $this->hash = array($this, '_sha256');
                 break;
            case 'sha384':
            case 'sha512':
                 $this->b = 128;
                 $this->hash = array($this, '_sha512');
                 break;
            case 'sha1':
            default:
                 $this->b = 64;
                 $this->hash = array($this, '_sha1');
        }
        $this->ipad = str_repeat(chr(0x36), $this->b);
        $this->opad = str_repeat(chr(0x5C), $this->b);
    }
    /**
     * Compute the HMAC.
     *
     * @access public
     * @param String $text
     * @return String
     */
    function hash($text)
    {
        $mode = is_array($this->hash) ? self::MODE_INTERNAL : CRYPT_HASH_MODE;
        if (!empty($this->key) || is_string($this->key)) {
            switch ( $mode ) {
                case self::MODE_MHASH:
                    $output = mhash($this->hash, $text, $this->key);
                    break;
                case self::MODE_HASH:
                    $output = hash_hmac($this->hash, $text, $this->key, true);
                    break;
                case self::MODE_INTERNAL:
                    /* "Applications that use keys longer than B bytes will first hash the key using H and then use the
                        resultant L byte string as the actual key to HMAC."
                        -- http://tools.ietf.org/html/rfc2104#section-2 */
                    $key = strlen($this->key) > $this->b ? call_user_func($this->hash, $this->key) : $this->key;
                    $key    = str_pad($key, $this->b, chr(0));      // step 1
                    $temp   = $this->ipad ^ $key;                   // step 2
                    $temp  .= $text;                                // step 3
                    $temp   = call_user_func($this->hash, $temp);   // step 4
                    $output = $this->opad ^ $key;                   // step 5
                    $output.= $temp;                                // step 6
                    $output = call_user_func($this->hash, $output); // step 7
            }
        } else {
            switch ( $mode ) {
                case self::MODE_MHASH:
                    $output = mhash($this->hash, $text);
                    break;
                case self::MODE_HASH:
                    $output = hash($this->hash, $text, true);
                    break;
                case self::MODE_INTERNAL:
                    $output = call_user_func($this->hash, $text);
            }
        }
        return substr($output, 0, $this->l);
    }
    /**
     * Returns the hash length (in bytes)
     *
     * @access public
     * @return Integer
     */
    function getLength()
    {
        return $this->l;
    }
    /**
     * Wrapper for MD5
     *
     * @access private
     * @param String $m
     */
    function _md5($m)
    {
        return pack('H*', md5($m));
    }
    /**
     * Wrapper for SHA1
     *
     * @access private
     * @param String $m
     */
    function _sha1($m)
    {
        return pack('H*', sha1($m));
    }
    /**
     * Pure-PHP implementation of MD2
     *
     * See {@link http://tools.ietf.org/html/rfc1319 RFC1319}.
     *
     * @access private
     * @param String $m
     */
    function _md2($m)
    {
        static $s = array(
             41,  46,  67, 201, 162, 216, 124,   1,  61,  54,  84, 161, 236, 240, 6,
             19,  98, 167,   5, 243, 192, 199, 115, 140, 152, 147,  43, 217, 188,
             76, 130, 202,  30, 155,  87,  60, 253, 212, 224,  22, 103,  66, 111, 24,
            138,  23, 229,  18, 190,  78, 196, 214, 218, 158, 222,  73, 160, 251,
            245, 142, 187,  47, 238, 122, 169, 104, 121, 145,  21, 178,   7,  63,
            148, 194,  16, 137,  11,  34,  95,  33, 128, 127,  93, 154,  90, 144, 50,
             39,  53,  62, 204, 231, 191, 247, 151,   3, 255,  25,  48, 179,  72, 165,
            181, 209, 215,  94, 146,  42, 172,  86, 170, 198,  79, 184,  56, 210,
            150, 164, 125, 182, 118, 252, 107, 226, 156, 116,   4, 241,  69, 157,
            112,  89, 100, 113, 135,  32, 134,  91, 207, 101, 230,  45, 168,   2, 27,
             96,  37, 173, 174, 176, 185, 246,  28,  70,  97, 105,  52,  64, 126, 15,
             85,  71, 163,  35, 221,  81, 175,  58, 195,  92, 249, 206, 186, 197,
            234,  38,  44,  83,  13, 110, 133,  40, 132,   9, 211, 223, 205, 244, 65,
            129,  77,  82, 106, 220,  55, 200, 108, 193, 171, 250,  36, 225, 123,
              8,  12, 189, 177,  74, 120, 136, 149, 139, 227,  99, 232, 109, 233,
            203, 213, 254,  59,   0,  29,  57, 242, 239, 183,  14, 102,  88, 208, 228,
            166, 119, 114, 248, 235, 117,  75,  10,  49,  68,  80, 180, 143, 237,
             31,  26, 219, 153, 141,  51, 159,  17, 131, 20
        );
        // Step 1. Append Padding Bytes
        $pad = 16 - (strlen($m) & 0xF);
        $m.= str_repeat(chr($pad), $pad);
        $length = strlen($m);
        // Step 2. Append Checksum
        $c = str_repeat(chr(0), 16);
        $l = chr(0);
        for ($i = 0; $i < $length; $i+= 16) {
            for ($j = 0; $j < 16; $j++) {
                // RFC1319 incorrectly states that C[j] should be set to S[c xor L]
                //$c[$j] = chr($s[ord($m[$i + $j] ^ $l)]);
                // per <http://www.rfc-editor.org/errata_search.php?rfc=1319>, however, C[j] should be set to S[c xor L] xor C[j]
                $c[$j] = chr($s[ord($m[$i + $j] ^ $l)] ^ ord($c[$j]));
                $l = $c[$j];
            }
        }
        $m.= $c;
        $length+= 16;
        // Step 3. Initialize MD Buffer
        $x = str_repeat(chr(0), 48);
        // Step 4. Process Message in 16-Byte Blocks
        for ($i = 0; $i < $length; $i+= 16) {
            for ($j = 0; $j < 16; $j++) {
                $x[$j + 16] = $m[$i + $j];
                $x[$j + 32] = $x[$j + 16] ^ $x[$j];
            }
            $t = chr(0);
            for ($j = 0; $j < 18; $j++) {
                for ($k = 0; $k < 48; $k++) {
                    $x[$k] = $t = $x[$k] ^ chr($s[ord($t)]);
                    //$t = $x[$k] = $x[$k] ^ chr($s[ord($t)]);
                }
                $t = chr(ord($t) + $j);
            }
        }
        // Step 5. Output
        return substr($x, 0, 16);
    }
    /**
     * Pure-PHP implementation of SHA256
     *
     * See {@link http://en.wikipedia.org/wiki/SHA_hash_functions#SHA-256_.28a_SHA-2_variant.29_pseudocode SHA-256 (a SHA-2 variant) pseudocode - Wikipedia}.
     *
     * @access private
     * @param String $m
     */
    function _sha256($m)
    {
        if (extension_loaded('suhosin')) {
            return pack('H*', sha256($m));
        }
        // Initialize variables
        $hash = array(
            0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19
        );
        // Initialize table of round constants
        // (first 32 bits of the fractional parts of the cube roots of the first 64 primes 2..311)
        static $k = array(
            0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
            0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
            0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
            0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
            0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
            0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
            0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
            0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2
        );
        // Pre-processing
        $length = strlen($m);
        // to round to nearest 56 mod 64, we'll add 64 - (length + (64 - 56)) % 64
        $m.= str_repeat(chr(0), 64 - (($length + 8) & 0x3F));
        $m[$length] = chr(0x80);
        // we don't support hashing strings 512MB long
        $m.= pack('N2', 0, $length << 3);
        // Process the message in successive 512-bit chunks
        $chunks = str_split($m, 64);
        foreach ($chunks as $chunk) {
            $w = array();
            for ($i = 0; $i < 16; $i++) {
                extract(unpack('Ntemp', $this->_string_shift($chunk, 4)));
                $w[] = $temp;
            }
            // Extend the sixteen 32-bit words into sixty-four 32-bit words
            for ($i = 16; $i < 64; $i++) {
                $s0 = $this->_rightRotate($w[$i - 15],  7) ^
                      $this->_rightRotate($w[$i - 15], 18) ^
                      $this->_rightShift( $w[$i - 15],  3);
                $s1 = $this->_rightRotate($w[$i - 2], 17) ^
                      $this->_rightRotate($w[$i - 2], 19) ^
                      $this->_rightShift( $w[$i - 2], 10);
                $w[$i] = $this->_add($w[$i - 16], $s0, $w[$i - 7], $s1);
            }
            // Initialize hash value for this chunk
            list($a, $b, $c, $d, $e, $f, $g, $h) = $hash;
            // Main loop
            for ($i = 0; $i < 64; $i++) {
                $s0 = $this->_rightRotate($a,  2) ^
                      $this->_rightRotate($a, 13) ^
                      $this->_rightRotate($a, 22);
                $maj = ($a & $b) ^
                       ($a & $c) ^
                       ($b & $c);
                $t2 = $this->_add($s0, $maj);
                $s1 = $this->_rightRotate($e,  6) ^
                      $this->_rightRotate($e, 11) ^
                      $this->_rightRotate($e, 25);
                $ch = ($e & $f) ^
                      ($this->_not($e) & $g);
                $t1 = $this->_add($h, $s1, $ch, $k[$i], $w[$i]);
                $h = $g;
                $g = $f;
                $f = $e;
                $e = $this->_add($d, $t1);
                $d = $c;
                $c = $b;
                $b = $a;
                $a = $this->_add($t1, $t2);
            }
            // Add this chunk's hash to result so far
            $hash = array(
                $this->_add($hash[0], $a),
                $this->_add($hash[1], $b),
                $this->_add($hash[2], $c),
                $this->_add($hash[3], $d),
                $this->_add($hash[4], $e),
                $this->_add($hash[5], $f),
                $this->_add($hash[6], $g),
                $this->_add($hash[7], $h)
            );
        }
        // Produce the final hash value (big-endian)
        return pack('N8', $hash[0], $hash[1], $hash[2], $hash[3], $hash[4], $hash[5], $hash[6], $hash[7]);
    }
    /**
     * Pure-PHP implementation of SHA384 and SHA512
     *
     * @access private
     * @param String $m
     */
    function _sha512($m)
    {
        static $init384, $init512, $k;
        if (!isset($k)) {
            // Initialize variables
            $init384 = array( // initial values for SHA384
                'cbbb9d5dc1059ed8', '629a292a367cd507', '9159015a3070dd17', '152fecd8f70e5939',
                '67332667ffc00b31', '8eb44a8768581511', 'db0c2e0d64f98fa7', '47b5481dbefa4fa4'
            );
            $init512 = array( // initial values for SHA512
                '6a09e667f3bcc908', 'bb67ae8584caa73b', '3c6ef372fe94f82b', 'a54ff53a5f1d36f1',
                '510e527fade682d1', '9b05688c2b3e6c1f', '1f83d9abfb41bd6b', '5be0cd19137e2179'
            );
            for ($i = 0; $i < 8; $i++) {
                $init384[$i] = new BigInteger($init384[$i], 16);
                $init384[$i]->setPrecision(64);
                $init512[$i] = new BigInteger($init512[$i], 16);
                $init512[$i]->setPrecision(64);
            }
            // Initialize table of round constants
            // (first 64 bits of the fractional parts of the cube roots of the first 80 primes 2..409)
            $k = array(
                '428a2f98d728ae22', '7137449123ef65cd', 'b5c0fbcfec4d3b2f', 'e9b5dba58189dbbc',
                '3956c25bf348b538', '59f111f1b605d019', '923f82a4af194f9b', 'ab1c5ed5da6d8118',
                'd807aa98a3030242', '12835b0145706fbe', '243185be4ee4b28c', '550c7dc3d5ffb4e2',
                '72be5d74f27b896f', '80deb1fe3b1696b1', '9bdc06a725c71235', 'c19bf174cf692694',
                'e49b69c19ef14ad2', 'efbe4786384f25e3', '0fc19dc68b8cd5b5', '240ca1cc77ac9c65',
                '2de92c6f592b0275', '4a7484aa6ea6e483', '5cb0a9dcbd41fbd4', '76f988da831153b5',
                '983e5152ee66dfab', 'a831c66d2db43210', 'b00327c898fb213f', 'bf597fc7beef0ee4',
                'c6e00bf33da88fc2', 'd5a79147930aa725', '06ca6351e003826f', '142929670a0e6e70',
                '27b70a8546d22ffc', '2e1b21385c26c926', '4d2c6dfc5ac42aed', '53380d139d95b3df',
                '650a73548baf63de', '766a0abb3c77b2a8', '81c2c92e47edaee6', '92722c851482353b',
                'a2bfe8a14cf10364', 'a81a664bbc423001', 'c24b8b70d0f89791', 'c76c51a30654be30',
                'd192e819d6ef5218', 'd69906245565a910', 'f40e35855771202a', '106aa07032bbd1b8',
                '19a4c116b8d2d0c8', '1e376c085141ab53', '2748774cdf8eeb99', '34b0bcb5e19b48a8',
                '391c0cb3c5c95a63', '4ed8aa4ae3418acb', '5b9cca4f7763e373', '682e6ff3d6b2b8a3',
                '748f82ee5defb2fc', '78a5636f43172f60', '84c87814a1f0ab72', '8cc702081a6439ec',
                '90befffa23631e28', 'a4506cebde82bde9', 'bef9a3f7b2c67915', 'c67178f2e372532b',
                'ca273eceea26619c', 'd186b8c721c0c207', 'eada7dd6cde0eb1e', 'f57d4f7fee6ed178',
                '06f067aa72176fba', '0a637dc5a2c898a6', '113f9804bef90dae', '1b710b35131c471b',
                '28db77f523047d84', '32caab7b40c72493', '3c9ebe0a15c9bebc', '431d67c49c100d4c',
                '4cc5d4becb3e42b6', '597f299cfc657e2a', '5fcb6fab3ad6faec', '6c44198c4a475817'
            );
            for ($i = 0; $i < 80; $i++) {
                $k[$i] = new BigInteger($k[$i], 16);
            }
        }
        $hash = $this->l == 48 ? $init384 : $init512;
        // Pre-processing
        $length = strlen($m);
        // to round to nearest 112 mod 128, we'll add 128 - (length + (128 - 112)) % 128
        $m.= str_repeat(chr(0), 128 - (($length + 16) & 0x7F));
        $m[$length] = chr(0x80);
        // we don't support hashing strings 512MB long
        $m.= pack('N4', 0, 0, 0, $length << 3);
        // Process the message in successive 1024-bit chunks
        $chunks = str_split($m, 128);
        foreach ($chunks as $chunk) {
            $w = array();
            for ($i = 0; $i < 16; $i++) {
                $temp = new BigInteger($this->_string_shift($chunk, 8), 256);
                $temp->setPrecision(64);
                $w[] = $temp;
            }
            // Extend the sixteen 32-bit words into eighty 32-bit words
            for ($i = 16; $i < 80; $i++) {
                $temp = array(
                          $w[$i - 15]->bitwise_rightRotate(1),
                          $w[$i - 15]->bitwise_rightRotate(8),
                          $w[$i - 15]->bitwise_rightShift(7)
                );
                $s0 = $temp[0]->bitwise_xor($temp[1]);
                $s0 = $s0->bitwise_xor($temp[2]);
                $temp = array(
                          $w[$i - 2]->bitwise_rightRotate(19),
                          $w[$i - 2]->bitwise_rightRotate(61),
                          $w[$i - 2]->bitwise_rightShift(6)
                );
                $s1 = $temp[0]->bitwise_xor($temp[1]);
                $s1 = $s1->bitwise_xor($temp[2]);
                $w[$i] = $w[$i - 16]->copy();
                $w[$i] = $w[$i]->add($s0);
                $w[$i] = $w[$i]->add($w[$i - 7]);
                $w[$i] = $w[$i]->add($s1);
            }
            // Initialize hash value for this chunk
            $a = $hash[0]->copy();
            $b = $hash[1]->copy();
            $c = $hash[2]->copy();
            $d = $hash[3]->copy();
            $e = $hash[4]->copy();
            $f = $hash[5]->copy();
            $g = $hash[6]->copy();
            $h = $hash[7]->copy();
            // Main loop
            for ($i = 0; $i < 80; $i++) {
                $temp = array(
                    $a->bitwise_rightRotate(28),
                    $a->bitwise_rightRotate(34),
                    $a->bitwise_rightRotate(39)
                );
                $s0 = $temp[0]->bitwise_xor($temp[1]);
                $s0 = $s0->bitwise_xor($temp[2]);
                $temp = array(
                    $a->bitwise_and($b),
                    $a->bitwise_and($c),
                    $b->bitwise_and($c)
                );
                $maj = $temp[0]->bitwise_xor($temp[1]);
                $maj = $maj->bitwise_xor($temp[2]);
                $t2 = $s0->add($maj);
                $temp = array(
                    $e->bitwise_rightRotate(14),
                    $e->bitwise_rightRotate(18),
                    $e->bitwise_rightRotate(41)
                );
                $s1 = $temp[0]->bitwise_xor($temp[1]);
                $s1 = $s1->bitwise_xor($temp[2]);
                $temp = array(
                    $e->bitwise_and($f),
                    $g->bitwise_and($e->bitwise_not())
                );
                $ch = $temp[0]->bitwise_xor($temp[1]);
                $t1 = $h->add($s1);
                $t1 = $t1->add($ch);
                $t1 = $t1->add($k[$i]);
                $t1 = $t1->add($w[$i]);
                $h = $g->copy();
                $g = $f->copy();
                $f = $e->copy();
                $e = $d->add($t1);
                $d = $c->copy();
                $c = $b->copy();
                $b = $a->copy();
                $a = $t1->add($t2);
            }
            // Add this chunk's hash to result so far
            $hash = array(
                $hash[0]->add($a),
                $hash[1]->add($b),
                $hash[2]->add($c),
                $hash[3]->add($d),
                $hash[4]->add($e),
                $hash[5]->add($f),
                $hash[6]->add($g),
                $hash[7]->add($h)
            );
        }
        // Produce the final hash value (big-endian)
        // (\phpseclib\Crypt\Hash::hash() trims the output for hashes but not for HMACs.  as such, we trim the output here)
        $temp = $hash[0]->toBytes() . $hash[1]->toBytes() . $hash[2]->toBytes() . $hash[3]->toBytes() .
                $hash[4]->toBytes() . $hash[5]->toBytes();
        if ($this->l != 48) {
            $temp.= $hash[6]->toBytes() . $hash[7]->toBytes();
        }
        return $temp;
    }
    /**
     * Right Rotate
     *
     * @access private
     * @param Integer $int
     * @param Integer $amt
     * @see _sha256()
     * @return Integer
     */
    function _rightRotate($int, $amt)
    {
        $invamt = 32 - $amt;
        $mask = (1 << $invamt) - 1;
        return (($int << $invamt) & 0xFFFFFFFF) | (($int >> $amt) & $mask);
    }
    /**
     * Right Shift
     *
     * @access private
     * @param Integer $int
     * @param Integer $amt
     * @see _sha256()
     * @return Integer
     */
    function _rightShift($int, $amt)
    {
        $mask = (1 << (32 - $amt)) - 1;
        return ($int >> $amt) & $mask;
    }
    /**
     * Not
     *
     * @access private
     * @param Integer $int
     * @see _sha256()
     * @return Integer
     */
    function _not($int)
    {
        return ~$int & 0xFFFFFFFF;
    }
    /**
     * Add
     *
     * _sha256() adds multiple unsigned 32-bit integers.  Since PHP doesn't support unsigned integers and since the
     * possibility of overflow exists, care has to be taken.  BigInteger could be used but this should be faster.
     *
     * @param Integer $...
     * @return Integer
     * @see _sha256()
     * @access private
     */
    function _add()
    {
        static $mod;
        if (!isset($mod)) {
            $mod = pow(2, 32);
        }
        $result = 0;
        $arguments = func_get_args();
        foreach ($arguments as $argument) {
            $result+= $argument < 0 ? ($argument & 0x7FFFFFFF) + 0x80000000 : $argument;
        }
        return fmod($result, $mod);
    }
    /**
     * String Shift
     *
     * Inspired by array_shift
     *
     * @param String $string
     * @param optional Integer $index
     * @return String
     * @access private
     */
    function _string_shift(&$string, $index = 1)
    {
        $substr = substr($string, 0, $index);
        $string = substr($string, $index);
        return $substr;
    }
}