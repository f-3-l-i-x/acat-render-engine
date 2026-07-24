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
     *
     */
    public const string EDIT_NONE = 'none';
    /**
     *
     */
    public const string EDIT_READ_ONLY = 'readOnly';
    /**
     *
     */
    public const string EDIT_COMMENTS = 'comments';
    /**
     *
     */
    public const string EDIT_FORMS = 'forms';
    /**
     *
     */
    public const string EDIT_TRACKED_CHANGES = 'trackedChanges';

    /**
     * @var string[]
     */
    private const array EDIT_MODES = [
        self::EDIT_NONE,
        self::EDIT_READ_ONLY,
        self::EDIT_COMMENTS,
        self::EDIT_FORMS,
        self::EDIT_TRACKED_CHANGES,
    ];

    /**
     * @var string|null
     */
    private ?string $editing = self::EDIT_READ_ONLY;

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
     * @param string $editing
     * @return void
     * @throws DocumentException
     */
    public function setEditing(string $editing) : void
    {
        if (!in_array($editing, self::EDIT_MODES, true)) {
            throw new DocumentException(
                "unsupported edit mode '$editing', expected one of: " . implode(', ', self::EDIT_MODES)
            );
        }

        $this->editing = $editing;
    }

    /**
     * @return string
     * @throws DocumentException
     */
    public function getPassword() : string
    {
        return PasswordEncoder::hashPassword($this->password, $this->algorithm, $this->getSalt(), $this->spinCount);
    }

    /**
     * @return string
     */
    public function getSalt() : string
    {
        if (empty($this->salt)) {
            $this->salt = openssl_random_pseudo_bytes(16);
        }
        return $this->salt;
    }

    /**
     * @param string|null $salt
     * @return void
     * @throws DocumentException
     */
    public function setSalt(?string $salt) : void
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
     * @return void
     * @throws DocumentException
     */
    public function setAlgorithm(string $algorithm) : void
    {
        if (!in_array($algorithm, PasswordEncoder::getAlgorithmNames(), true)) {
            throw new DocumentException(
                "unsupported algorithm '$algorithm', expected one of: "
                . implode(', ', PasswordEncoder::getAlgorithmNames())
            );
        }

        $this->algorithm = $algorithm;
    }

    /**
     * @return string
     * @throws DocumentException
     */
    public function getAlgorithmId() : string
    {
        return (string) PasswordEncoder::getAlgorithmId($this->algorithm);
    }

}