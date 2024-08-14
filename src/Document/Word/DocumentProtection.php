<?php

namespace ACAT\Document\Word;

use Exception;
use ACAT\Exception\DocumentException;

/**
 *
 */
class DocumentProtection
{

    /**
     * @var string|null
     */
    private ?string $editing = "readOnly";

    /**
     * @var string|null
     */
    private ?string $password;

    /**
     * @var int
     */
    private int $spinCount = 100000;

    /**
     * @var string
     */
    private string $algorithm = PasswordEncoder::ALGORITHM_SHA_1;

    /**
     * @var string|null
     */
    private ?string $salt;

    /**
     * @param string|null $password
     * @throws Exception
     */
    public function __construct(?string $password = null)
    {
        if (!$password) {
            $randomBytes = random_bytes(32);
            $password = bin2hex($randomBytes);
        }

        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getEditing() : ?string
    {
        return $this->editing;
    }

    /**
     * @return string
     */
    public function getPassword() : string
    {
        return PasswordEncoder::hashPassword($this->password, $this->algorithm, $this->getSalt(), $this->spinCount);
    }

    /**
     * @return mixed
     */
    public function getSalt() : string
    {
        if (empty($this->salt)) {
            $this->salt = openssl_random_pseudo_bytes(16);
        }
        return $this->salt;
    }

    /**
     * @param $salt
     * @return void
     * @throws DocumentException
     */
    public function setSalt($salt) : void
    {
        if ($salt !== null && strlen($salt) !== 16) {
            throw new DocumentException('salt has to be of exactly 16 bytes length');
        }

        $this->salt = $salt;
    }

    /**
     * @return int
     */
    public function getSpinCount() : int
    {
        return $this->spinCount;
    }

    /**
     * @param int $spinCount
     */
    public function setSpinCount(int $spinCount) : void
    {
        $this->spinCount = $spinCount;
    }

    /**
     * @return string
     */
    public function getAlgorithm() : string
    {
        return $this->algorithm;
    }

    /**
     * @param string $algorithm
     */
    public function setAlgorithm(string $algorithm) : void
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @return string
     */
    public function getAlgorithmId() : string
    {
        return PasswordEncoder::getAlgorithmId($this->algorithm);
    }

}