<?php

namespace ACAT\Parser\Placeholder;

use DOMNode;
use DOMDocument;
use DOMException;
use ACAT\Document\Word\DocumentProtection;

/**
 *
 */
class WordDocumentProtectionPlaceholder extends ACatPlaceholder
{
    /**
     * Creates a placeholder for the given document protection settings.
     *
     * @param DocumentProtection $documentProtection
     */
    public function __construct(private DocumentProtection $documentProtection)
    {
        parent::__construct();
    }

    /**
     * Builds the "w:documentProtection" DOM element that enforces Word's
     * document-protection restrictions.
     *
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    #[\Override]
    public function getDOMNode(DOMDocument $domDocument): DOMNode
    {
        $edit = $this->documentProtection->getEditing();
        $hash = $this->documentProtection->getPassword();
        $spinCount = $this->documentProtection->getSpinCount();
        $salt = base64_encode($this->documentProtection->getSalt());
        $algorithmSid = $this->documentProtection->getAlgorithmId();

        $elementNode = $domDocument->createElement('w:documentProtection');

        $saltAttribute = $domDocument->createAttribute('w:salt');
        $saltAttribute->value = $salt;

        $hashAttribute = $domDocument->createAttribute('w:hash');
        $hashAttribute->value = $hash;

        $spinCountAttribute = $domDocument->createAttribute('w:cryptSpinCount');
        $spinCountAttribute->value = $spinCount;

        $cryptAlgorithmSidAttribute = $domDocument->createAttribute('w:cryptAlgorithmSid');
        $cryptAlgorithmSidAttribute->value = $algorithmSid;

        $algorithmTypeAttribute = $domDocument->createAttribute('w:cryptAlgorithmType');
        $algorithmTypeAttribute->value = "typeAny";

        $cryptAlgorithmClassAttribute = $domDocument->createAttribute('w:cryptAlgorithmClass');
        $cryptAlgorithmClassAttribute->value = "hash";

        $cryptProviderTypeAttribute = $domDocument->createAttribute('w:cryptProviderType');
        $cryptProviderTypeAttribute->value = "rsaFull";

        $enforcementAttribute = $domDocument->createAttribute('w:enforcement');
        $enforcementAttribute->value = "1";

        $editAttribute = $domDocument->createAttribute('w:edit');
        $editAttribute->value = $edit;

        $elementNode->appendChild($saltAttribute);
        $elementNode->appendChild($hashAttribute);
        $elementNode->appendChild($spinCountAttribute);
        $elementNode->appendChild($cryptAlgorithmSidAttribute);
        $elementNode->appendChild($algorithmTypeAttribute);
        $elementNode->appendChild($cryptAlgorithmClassAttribute);
        $elementNode->appendChild($cryptProviderTypeAttribute);
        $elementNode->appendChild($enforcementAttribute);
        $elementNode->appendChild($editAttribute);

        return $elementNode;
    }

    /**
     * Returns the length of the placeholder's XML tag representation.
     *
     * @return int
     */
    #[\Override]
    public function length(): int
    {
        return strlen($this->getXMLTagAsString());
    }

    /**
     * Returns the placeholder's "w:documentProtection" tag as a string, used
     * for logging.
     *
     * @return string
     */
    #[\Override]
    public function getXMLTagAsString(): string
    {
        $edit = $this->documentProtection->getEditing();
        $hash = $this->documentProtection->getPassword();
        $spinCount = $this->documentProtection->getSpinCount();
        $salt = base64_encode($this->documentProtection->getSalt());
        $algorithmSid = $this->documentProtection->getAlgorithmId();

        return '<w:documentProtection w:salt="' . $salt . '" 
							  		  w:hash="' . $hash . '" 
							  		  w:cryptSpinCount="' . $spinCount . '" 
							  		  w:cryptAlgorithmSid="' . $algorithmSid . '" 
							  		  w:cryptAlgorithmType="typeAny" 
							  		  w:cryptAlgorithmClass="hash" 
							  		  w:cryptProviderType="rsaFull" 
							  		  w:enforcement="1" 
							  		  w:edit="' . $edit . '"/>';
    }

    /**
     * Returns an empty string, since this placeholder has no corresponding
     * "${...}" marker text.
     *
     * @return string
     */
    #[\Override]
    public function getNodeAsString(): string
    {
        return "";
    }
}
