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
     * @var DocumentProtection
     */
    private DocumentProtection $documentProtection;

    /**
     * WordDocumentProtectionPlaceholder constructor.
     * @param DocumentProtection $documentProtection
     */
    public function __construct(DocumentProtection $documentProtection)
    {
        parent::__construct();
        $this->documentProtection = $documentProtection;
    }

    /**
     * @param DOMDocument $domDocument
     * @return DOMNode
     * @throws DOMException
     */
    public function getDOMNode(DOMDocument $domDocument) : DOMNode
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
     * @return int
     */
    public function length() : int
    {
        return strlen($this->getXMLTagAsString());
    }

    /**
     * @return string
     */
    public function getXMLTagAsString() : string
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
     * @return string
     */
    public function getNodeAsString() : string
    {
        return "";
    }
}