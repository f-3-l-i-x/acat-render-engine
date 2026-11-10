<?php

namespace ACAT\Document\Word;

use DOMException;
use ACAT\Exception\DocumentException;
use ACAT\Parser\Placeholder\WordDocumentProtectionPlaceholder;

/**
 *
 */
class SettingsContentPart extends ContentPart
{
    /**
     * @param DocumentProtection $documentProtection
     * @return void
     * @throws DocumentException
     * @throws DOMException
     */
    public function protect(DocumentProtection $documentProtection): void
    {
        $wordDocumentProtectionPlaceHolder = new WordDocumentProtectionPlaceholder($documentProtection);
        $nodes = $this->getXPath()->query('//w:settings');

        if (!$nodes || $nodes->length <> 1) {
            throw new DocumentException($this->path . ' has no root node');
        }

        // An already protected document must not end up with two protection nodes. A node created by an earlier
        // call has no namespace until the document is parsed again, so it is also selected by its name.
        $documentProtectionNodes = $this->getXPath()->query(
            '//w:settings/w:documentProtection|//w:settings/*[name() = "w:documentProtection"]'
        );

        foreach ($documentProtectionNodes as $documentProtectionNode) {
            $documentProtectionNode->parentNode->removeChild($documentProtectionNode);
        }

        $nodes->item(0)->appendChild($wordDocumentProtectionPlaceHolder->getDOMNode($this->getDomDocument()));
    }
}
