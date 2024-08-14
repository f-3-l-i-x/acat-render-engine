<?php

namespace Tests\Render;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use ACAT\Document\Word\WordDocument;
use ACAT\Exception\ElementException;
use Monolog\Formatter\LineFormatter;
use ACAT\Exception\DocumentException;
use ACAT\Document\Word\ContentPart;
use ACAT\Parser\Element\ElementGenerator;

/**
 *
 */
class AbstractRenderTest extends TestCase
{

    /**
     * @return ElementGenerator
     * @throws ElementException
     */
    public function getWordElementGenerator() : ElementGenerator
    {
        $wordContentPart = $this->getWordContentPart();
        return new ElementGenerator($wordContentPart);
    }

    /**
     * @test
     *
     * @return ContentPart
     */
    public function getWordContentPart() : ContentPart
    {
        $testXMLFile = __DIR__ . '/../Resources/Render/Element/document.xml';

        $xmlContent = file_get_contents($testXMLFile);
        $this->assertIsString($xmlContent);

        return new ContentPart($xmlContent, $testXMLFile);
    }

    /**
     * @return ElementGenerator
     * @throws ElementException
     */
    public function getWordParagraphElementGenerator() : ElementGenerator
    {
        return new ElementGenerator($this->getParagraphBlockContentPart());
    }

    /**
     * @return ContentPart
     */
    private function getParagraphBlockContentPart() : ContentPart
    {
        $testXMLFile = __DIR__ . '/../Resources/Render/Block/ParagraphBlock.xml';

        $xmlContent = file_get_contents($testXMLFile);
        $this->assertIsString($xmlContent);

        $contentPart = new ContentPart($xmlContent, $testXMLFile);
        $this->assertInstanceOf(ContentPart::class, $contentPart);

        return $contentPart;
    }

    /**
     * @return ElementGenerator
     * @throws ElementException
     */
    public function getWordTableRowElementGenerator() : ElementGenerator
    {
        return new ElementGenerator($this->getTableRowBlockContentPart());
    }

    /**
     * @return ContentPart
     */
    private function getTableRowBlockContentPart() : ContentPart
    {
        $testXMLFile = __DIR__ . '/../Resources/Render/Block/TableRowBlock.xml';

        $xmlContent = file_get_contents($testXMLFile);
        $this->assertIsString($xmlContent);

        $contentPart = new ContentPart($xmlContent, $testXMLFile);
        $this->assertInstanceOf(ContentPart::class, $contentPart);

        return $contentPart;
    }

    /**
     * @return ElementGenerator
     * @throws ElementException
     */
    public function getConditionElementGenerator() : ElementGenerator
    {
        return new ElementGenerator($this->getConditionContentPart());
    }

    /**
     * @return ContentPart
     */
    protected function getConditionContentPart() : ContentPart
    {

        $testXMLFile = __DIR__ . '/../Resources/Render/Condition/Conditions.xml';

        $xmlContent = file_get_contents($testXMLFile);
        $this->assertIsString($xmlContent);

        $contentPart = new ContentPart($xmlContent, $testXMLFile);
        $this->assertInstanceOf(ContentPart::class, $contentPart);

        return $contentPart;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger() : LoggerInterface
    {
        $name = 'acat-render-engine';
        $path = __DIR__ . '/../logs/acat-render-engine.log';
        $level = Logger::DEBUG;

        $logger = new Logger($name);

        $dateFormat = "d.m.Y - H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message% \n";

        $formatter = new LineFormatter($output, $dateFormat, true);
        $formatter->includeStacktraces();

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($path, $level);
        $handler->setFormatter($formatter);

        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * @return array
     */
    protected function getValues() : array
    {
        $values['156'] = 156;
        $values['1121'] = 1121;
        $values['1744'] = 1744;
        $values['1745'] = 1745;
        $values['1747'] = 1747;
        $values['1748'] = 1748;

        return $values;
    }

    /**
     * @return WordDocument
     * @throws DocumentException
     */
    protected function getWordDocument() : WordDocument
    {
        $currentDocument = __DIR__ . "/resources/Rechnung_not_empty_lck.docx";
        copy(__DIR__ . '/resources/Rechnung.docx', $currentDocument);

        $wordDocument = new WordDocument($currentDocument);
        $this->assertInstanceOf(WordDocument::class, $wordDocument);

        return $wordDocument;
    }

    /**
     * @return WordDocument
     * @throws DocumentException
     */
    protected function getZulassung() : WordDocument
    {
        $currentDocument = __DIR__ . "/Resources/Zulassung_not_empty_lck.docx";
        copy(__DIR__ . '/Resources/Zulassung.docx', $currentDocument);

        $wordDocument = new WordDocument($currentDocument);
        $this->assertInstanceOf(WordDocument::class, $wordDocument);

        return $wordDocument;
    }

    /**
     * @return array
     */
    protected function getWinther() : array
    {
        return json_decode(
            '{"bcc":"ina.langisch@vw.uni-bremen.de","preview":false,"approval_id":"8144","cohort_id":"7830","contact_id":"8143","process_id":"4b52eec4-7c08-43dd-bafa-65646d215810","render_values":{"1b43973b-8a76-4ab1-8675-837d84795201":{"word\/document.xml":[],"word\/styles.xml":[],"word\/settings.xml":[],"word\/webSettings.xml":[],"word\/fontTable.xml":[],"word\/theme\/theme1.xml":[],"docProps\/core.xml":[],"docProps\/app.xml":[]},"448a3808-c3de-4d5e-9e24-45383e3dd3ef":{"word\/document.xml":[],"customXml\/itemProps1.xml":[],"word\/numbering.xml":[],"word\/styles.xml":[],"word\/settings.xml":[],"word\/webSettings.xml":[],"word\/footnotes.xml":[],"word\/endnotes.xml":[],"word\/fontTable.xml":[],"word\/theme\/theme1.xml":[],"docProps\/core.xml":[],"docProps\/app.xml":[]},"50ab45af-8288-4b5e-bae6-f7c9b098b99b":{"word\/document.xml":{"fields":{"bewerbung_id":"8144","478":"Ina","479":"Langisch","488":"Unicom, Haus Turin","490":"3.0120","501":"Mary-Somerville-Stra\u00dfe 3","504":"28359","502":"Bremen","489":"0421 218-61626","495":"0421 218-61626","481":"ina.langisch@vw.uni-bremen.de","499":"2-2","1981":"emma@uni-bremen.de","1982":"www.uni-bremen.de\/emma","1736":"18.000,00","67":"Stefanie","70":"Winther","1698":"Senator-B\u00f6lken-Stra\u00dfe 10","1699":"28359","1700":"Bremen","66":"Frau"},"views":{"V_TITLE":null,"V_DATE":"10.07.2024","V_P_BEZEICHNUNG":"Weiterbildenden Masterstudiengang \"Entscheidungsmanagement (Professional Public Decision Making)\" 2024 - 2027","V_SALUTATION":"Sehr geehrte Frau","V_EMMA_GROUP_1":true,"V_EMMA_GROUP_2":false,"V_EMMA_GROUP_3":false}},"customXml\/itemProps1.xml":[],"word\/numbering.xml":[],"word\/styles.xml":[],"word\/settings.xml":[],"word\/webSettings.xml":[],"word\/footnotes.xml":[],"word\/endnotes.xml":[],"word\/header1.xml":[],"word\/footer1.xml":[],"word\/header2.xml":[],"word\/footer2.xml":[],"word\/header3.xml":[],"word\/fontTable.xml":[],"word\/webextensions\/taskpanes.xml":[],"word\/webextensions\/webextension1.xml":[],"word\/theme\/theme1.xml":[],"docProps\/core.xml":[],"docProps\/app.xml":[]},"72a619f7-e1b3-48ed-bc9d-056bbe6a5238":{"word\/document.xml":{"fields":{"bewerbung_id":"8144","67":"Stefanie","70":"Winther","1703":"16.11.1982","80":"stefanie.c.winther@gmail.com","71":"0173 21 59 993"}},"customXml\/itemProps1.xml":[],"word\/numbering.xml":[],"word\/styles.xml":[],"word\/settings.xml":[],"word\/webSettings.xml":[],"word\/footnotes.xml":[],"word\/endnotes.xml":[],"word\/header1.xml":[],"word\/fontTable.xml":[],"word\/webextensions\/taskpanes.xml":[],"word\/webextensions\/webextension1.xml":[],"word\/theme\/theme1.xml":[],"docProps\/core.xml":[],"docProps\/app.xml":[]},"75659055-cbda-48d1-acbb-6eb4a98a1ef6":{"word\/document.xml":{"fields":{"bewerbung_id":"8144","70":"Winther","478":"Ina","479":"Langisch","501":"Mary-Somerville-Stra\u00dfe 3","504":"28359","502":"Bremen","489":"0421 218-61626","495":"0421 218-61626","481":"ina.langisch@vw.uni-bremen.de","1981":"emma@uni-bremen.de","1982":"www.uni-bremen.de\/emma"},"views":{"V_SALUTATION":"Sehr geehrte Frau","V_TITLE":null,"V_P_BEZEICHNUNG":"Weiterbildenden Masterstudiengang \"Entscheidungsmanagement (Professional Public Decision Making)\" 2024 - 2027"}},"word\/numbering.xml":[],"word\/styles.xml":[],"word\/settings.xml":[],"word\/webSettings.xml":[],"word\/fontTable.xml":[],"word\/webextensions\/taskpanes.xml":[],"word\/webextensions\/webextension1.xml":[],"word\/theme\/theme1.xml":[],"docProps\/core.xml":[],"docProps\/app.xml":[]}},"recipient":"stefanie.c.winther@gmail.com","action":"create","send_from":"emma@uni-bremen.de","send_from_name":"Akademie f\u00fcr Weiterbildung der Universit\u00e4t Bremen","attachments":[]}',
            JSON_OBJECT_AS_ARRAY
        );
    }

    /**
     * @return array
     */
    protected function getData() : array
    {
        return json_decode(
            '{"word/document.xml":{"fields":{"68":"30860","1676":"PL - 10150","1741":"4200026","1742":"20.07.2022","1744":"Herr","1745":"Jörg","1747":"Knop","1748":"Hofstraße 39D","1749":"33607","1750":"Bielefeld","1752":null,"1757":"","1760":null,"1761":null,"1762":null,"1765":"Weiterbildendes Studium mit Zertifikatsabschluss „Arbeitsbezogene Beratung“ 2022/2023","1768":"85511013","1769":"01.09.2022","1770":"31.08.2023","1771":"5.600,00","1772":"5.600,00","1780":true,"1783":"3","1858":"","1860":null,"1862":null,"1863":null,"1868":"","1869":"","1918":0,"1921":null,"1922":null,"1923":0,"1964":null,"1965":null,"rechnung_id":5467},"blocks":{"3":{"fields":[{"1772":"5.600,00","1786":"01.09.2022","1787":"1.400,00","rechnung_id":5467},{"1772":"5.600,00","1786":"01.12.2022","1787":"1.400,00","rechnung_id":5467},{"1772":"5.600,00","1786":"01.03.2023","1787":"1.400,00","rechnung_id":5467},{"1772":"5.600,00","1786":"01.06.2023","1787":"1.400,00","rechnung_id":5467}]}}},"customXml/itemProps1.xml":{"fields":{"rechnung_id":5467}},"word/numbering.xml":{"fields":{"rechnung_id":5467}},"word/styles.xml":{"fields":{"rechnung_id":5467}},"word/settings.xml":{"fields":{"rechnung_id":5467}},"word/webSettings.xml":{"fields":{"rechnung_id":5467}},"word/footnotes.xml":{"fields":{"rechnung_id":5467}},"word/endnotes.xml":{"fields":{"rechnung_id":5467}},"word/header1.xml":{"fields":{"rechnung_id":5467}},"word/header2.xml":{"fields":{"rechnung_id":5467}},"word/footer1.xml":{"fields":{"rechnung_id":5467}},"word/footer2.xml":{"fields":{"rechnung_id":5467}},"word/header3.xml":{"fields":{"478":"Marlis","479":"Glomba","481":"mglomba@uni-bremen.de","489":"+49 421 218 616 22","490":"ZB B 1360","495":"+49 421 218 98 616 22","501":"Bibliothekstraße 2a","rechnung_id":5467}},"word/footer3.xml":{"fields":{"rechnung_id":5467}},"word/fontTable.xml":{"fields":{"rechnung_id":5467}},"word/webextensions/taskpanes.xml":{"fields":{"rechnung_id":5467}},"word/webextensions/webextension1.xml":{"fields":{"rechnung_id":5467}},"word/theme/theme1.xml":{"fields":{"rechnung_id":5467}},"docProps/core.xml":{"fields":{"rechnung_id":5467}},"docProps/app.xml":{"fields":{"rechnung_id":5467}}}',
            JSON_OBJECT_AS_ARRAY
        );
    }

    /**
     * @return array
     */
    protected function getZData() : array
    {
        return json_decode(
            '{"word\/document.xml":{"fields":{"bewerbung_id":5284,"478":"Marlis","479":"Glomba","501":"Bibliothekstra\u00dfe 2a - Zentralbereich","489":"+49 421 218 616 22","495":"+49 421 218 98 616 22","481":"mglomba@uni-bremen.de","499":"2-2","1981":"mabo@uni-bremen.de","1982":"https:\/\/www.uni-bremen.de\/mabo\/studium\/arbeitsbezogene-beratung","1674":"Weiterbildungskurs Arbeitsbezogene Beratung","67":"Natalie","70":"Kaluzny","1698":"Lange Stra\u00dfe 60","1699":"27211","1700":"Bassum","66":"Frau","1707":null,"1801":null,"1961":"27.06.2022"},"views":{"V_TITLE":null,"V_P_BEZEICHNUNG":"zum","V_SALUTATION":"Sehr geehrte Frau","V_TERMINATE_DATE_1":null,"V_TERMINATE_DATE_2":null}},"customXml\/itemProps1.xml":{"fields":{"bewerbung_id":5284}},"word\/numbering.xml":{"fields":{"bewerbung_id":5284}},"word\/styles.xml":{"fields":{"bewerbung_id":5284}},"word\/settings.xml":{"fields":{"bewerbung_id":5284}},"word\/webSettings.xml":{"fields":{"bewerbung_id":5284}},"word\/footnotes.xml":{"fields":{"bewerbung_id":5284}},"word\/endnotes.xml":{"fields":{"bewerbung_id":5284}},"word\/header1.xml":{"fields":{"bewerbung_id":5284}},"word\/footer1.xml":{"fields":{"bewerbung_id":5284}},"word\/header2.xml":{"fields":{"bewerbung_id":5284}},"word\/footer2.xml":{"fields":{"bewerbung_id":5284}},"word\/header3.xml":{"fields":{"bewerbung_id":5284}},"word\/fontTable.xml":{"fields":{"bewerbung_id":5284}},"word\/webextensions\/taskpanes.xml":{"fields":{"bewerbung_id":5284}},"word\/webextensions\/webextension1.xml":{"fields":{"bewerbung_id":5284}},"word\/theme\/theme1.xml":{"fields":{"bewerbung_id":5284}},"docProps\/core.xml":{"fields":{"bewerbung_id":5284}},"docProps\/app.xml":{"fields":{"bewerbung_id":5284}}}',
            JSON_OBJECT_AS_ARRAY
        );
    }

    /**
     * @return array
     */
    protected function getEZDate() : array
    {
        return json_decode(
            '{"word/document.xml":{"fields":{"66":null,"67":"Emma1","70":"Emma1","478":"Ronny","479":"Krämer","481":"ronny.kraemer@uni-bremen.de","488":null,"489":"+49 421 218 61629","490":null,"495":null,"499":"RK","501":"Mary-Somerville Str. 3","502":"Bremen","504":"28359","1698":null,"1699":null,"1700":null,"1736":0,"1801":null,"1981":"emma@uni-bremen.de","1982":null,"bewerbung_id":5391},"views":{"V_TITLE":null,"V_P_BEZEICHNUNG":"zum Weiterbildenden Masterstudiengang \"Entscheidungsmanagement (Professional Public Decision Making)\" Master","V_SALUTATION":"Guten Tag Emma1","V_WBTITLE":"Weiterbildungsstudierende:n","V_GROUP_2":true,"V_GROUP_1":false,"V_GROUP_3":false}},"customXml/itemProps1.xml":{"fields":{"bewerbung_id":5391}},"word/numbering.xml":{"fields":{"bewerbung_id":5391}},"word/styles.xml":{"fields":{"bewerbung_id":5391}},"word/settings.xml":{"fields":{"bewerbung_id":5391}},"word/webSettings.xml":{"fields":{"bewerbung_id":5391}},"word/footnotes.xml":{"fields":{"bewerbung_id":5391}},"word/endnotes.xml":{"fields":{"bewerbung_id":5391}},"word/comments.xml":{"fields":{"bewerbung_id":5391}},"word/commentsExtended.xml":{"fields":{"bewerbung_id":5391}},"word/commentsIds.xml":{"fields":{"bewerbung_id":5391}},"word/commentsExtensible.xml":{"fields":{"bewerbung_id":5391}},"word/header1.xml":{"fields":{"bewerbung_id":5391}},"word/footer1.xml":{"fields":{"bewerbung_id":5391}},"word/header2.xml":{"fields":{"bewerbung_id":5391}},"word/footer2.xml":{"fields":{"bewerbung_id":5391}},"word/header3.xml":{"fields":{"bewerbung_id":5391}},"word/fontTable.xml":{"fields":{"bewerbung_id":5391}},"word/people.xml":{"fields":{"bewerbung_id":5391}},"word/webextensions/taskpanes.xml":{"fields":{"bewerbung_id":5391}},"word/webextensions/webextension1.xml":{"fields":{"bewerbung_id":5391}},"word/theme/theme1.xml":{"fields":{"bewerbung_id":5391}},"docProps/core.xml":{"fields":{"bewerbung_id":5391}},"docProps/app.xml":{"fields":{"bewerbung_id":5391}}}',
            JSON_OBJECT_AS_ARRAY
        );
    }

    /**
     * @return array
     */
    protected function getPMQData() : array
    {
        return json_decode(
            '{
  "word/document.xml": {
    "views": {
      "V_SALUTATION": "Sehr <w:br/> geehrter Herr <w:br/> dddd dd <w:br/>",
      "V_TITLE": null
    },
    "word/numbering.xml": [],
    "word/styles.xml": [],
    "word/settings.xml": [],
    "word/webSettings.xml": [],
    "word/fontTable.xml": [],
    "word/webextensions/taskpanes.xml": [],
    "word/webextensions/webextension1.xml": [],
    "word/theme/theme1.xml": [],
    "docProps/core.xml": [],
    "docProps/app.xml": []
  }
}',
            JSON_OBJECT_AS_ARRAY
        );
    }

    /**
     * @return array
     */
    protected function getEmptyBlockData() : array
    {
        return json_decode(
            '{"word\/document.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/styles.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/settings.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/webSettings.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/footnotes.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/endnotes.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/header1.xml":{"fields":{"programmdurchlauf_id":"5314","1674":"Qualifizierung zur Praxismentorin \/ zum Praxismentor 2022\/2023","1676":"PL - 10158"}},"word\/footer1.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/header2.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/footer2.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/fontTable.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/webextensions\/taskpanes.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/webextensions\/webextension1.xml":{"fields":{"programmdurchlauf_id":"5314"}},"word\/theme\/theme1.xml":{"fields":{"programmdurchlauf_id":"5314"}},"docProps\/core.xml":{"fields":{"programmdurchlauf_id":"5314"}},"docProps\/app.xml":{"fields":{"programmdurchlauf_id":"5314"}}}',
            JSON_OBJECT_AS_ARRAY
        );
    }

    /**
     * @return array
     */
    protected function getBlockData() : array
    {
        return json_decode(
            '{"word\/document.xml":{"blocks":[{"fields":[{"bewerbung_id":"5164","70":"Dauer","67":"Sieglinde","1703":"09.12.1967","68":"30220","1940":"22.03.2022"},{"bewerbung_id":"5166","70":"Delorme","67":"Ina","1703":"09.03.1981","68":"30806","1940":"25.04.2022"},{"bewerbung_id":"5168","70":"Flentge","67":"Corinna","1703":"14.04.1974","68":"30807","1940":"19.04.2022"},{"bewerbung_id":"5170","70":"Hoffmann","67":"Tim","1703":"25.01.1989","68":"30808","1940":"13.04.2022"},{"bewerbung_id":"5172","70":"H\u00f6lke","67":"Dennis","1703":"07.09.1982","68":"30809","1940":"11.04.2022"},{"bewerbung_id":"5173","70":"Komissarova","67":"Anna","1703":"09.08.1982","68":"30499","1940":"12.04.2022"},{"bewerbung_id":"5174","70":"Seibt","67":"Christin","1703":"12.02.1985","68":"30506","1940":"31.03.2022"},{"bewerbung_id":"5176","70":"Tielert","67":"Imke","1703":"15.07.1971","68":"30810","1940":"22.04.2022"},{"bewerbung_id":"5178","70":"Wersig","67":"Thorben","1703":"16.07.1985","68":"30811","1940":"24.01.2022"},{"bewerbung_id":"5180","70":"Gerdes","67":"Bernd","1703":"14.01.1917","68":"30812","1940":"27.04.2022"},{"bewerbung_id":"5182","70":"Geyfman","67":"Elena","1703":"09.07.1976","68":"30813","1940":"27.04.2022"},{"bewerbung_id":"5185","70":"Becker","67":"Jennifer","1703":"01.08.1986","68":"30814","1940":"29.04.2022"},{"bewerbung_id":"5187","70":"Gack","67":"Franziska","1703":"11.05.1989","68":"30815","1940":"02.05.2022"},{"bewerbung_id":"5189","70":"Lonquich","67":"Petra","1703":"02.11.1967","68":"30816","1940":"03.05.2022"},{"bewerbung_id":"5191","70":"M\u00fcller","67":"Katherina","1703":"18.10.1978","68":"30817","1940":"28.04.2022"},{"bewerbung_id":"5193","70":"Saade","67":"Benjamin","1703":"18.06.1982","68":"30818","1940":"02.05.2022"},{"bewerbung_id":"5195","70":"Schaller","67":"Jens Kristoff","1703":"05.12.1983","68":"30819","1940":"02.05.2022"},{"bewerbung_id":"5197","70":"von Elling","67":"Karsten","1703":"01.03.1993","68":"30820","1940":"03.05.2022"},{"bewerbung_id":"5200","70":"Bernsen","67":"Linda","1703":"25.04.1996","68":"30821","1940":"04.05.2022"},{"bewerbung_id":"5202","70":"Leibinger","67":"Ramona","1703":"26.11.1992","68":"30822","1940":"04.05.2022"},{"bewerbung_id":"5205","70":"Bacher","67":"Vanessa Maria","1703":"13.08.1981","68":"30511","1940":"09.05.2022"},{"bewerbung_id":"5207","70":"Wendt","67":"Valentin","1703":"26.11.1993","68":"30823","1940":"09.05.2022"},{"bewerbung_id":"5209","70":"Stollberg","67":"Eva","1703":"28.03.1980","68":"30824","1940":"09.05.2022"},{"bewerbung_id":"5211","70":"Struhalla","67":"S\u00f6nke","1703":"02.11.1982","68":"30825","1940":"09.05.2022"},{"bewerbung_id":"5213","70":"Bekker","67":"Dennis","1703":"23.09.1993","68":"30826","1940":"09.05.2022"},{"bewerbung_id":"5215","70":"Schedler","67":"Melina","1703":"04.08.1994","68":"30827","1940":"09.05.2022"},{"bewerbung_id":"5217","70":"Wiebalck","67":"Sevtap","1703":"21.11.1988","68":"30828","1940":"09.05.2022"},{"bewerbung_id":"5219","70":"Waschitzek","67":"Dennis","1703":"28.06.1991","68":"30829","1940":"09.05.2022"},{"bewerbung_id":"5221","70":"Bretschneider","67":"Jonas","1703":"02.05.1994","68":"30830","1940":"09.05.2022"},{"bewerbung_id":"5223","70":"Eslikizi","67":"Umut","1703":"17.05.1992","68":"30831","1940":"09.05.2022"},{"bewerbung_id":"5225","70":"Katzorke","67":"Petra","1703":"01.09.1977","68":"30832","1940":"09.05.2022"},{"bewerbung_id":"5227","70":"Beyer","67":"Jana","1703":"20.06.1978","68":"30833","1940":"09.05.2022"},{"bewerbung_id":"5229","70":"Elsner","67":"Tabea","1703":"09.09.1994","68":"30834","1940":"09.05.2022"},{"bewerbung_id":"5231","70":"Metz","67":"Laura","1703":"27.05.1987","68":"30835","1940":"09.05.2022"},{"bewerbung_id":"5233","70":"Seber","67":"Jens","1703":"09.11.1987","68":"30836","1940":"09.05.2022"},{"bewerbung_id":"5235","70":"Hamelmann","67":"Kai","1703":"04.08.1997","68":"30837","1940":"09.05.2022"},{"bewerbung_id":"5237","70":"Klemens","67":"Michael","1703":"06.08.1986","68":"30838","1940":"09.05.2022"},{"bewerbung_id":"5239","70":"Schettler","67":"Rike","1703":"27.08.1987","68":"30839","1940":"09.05.2022"},{"bewerbung_id":"5241","70":"M\u00fcller","67":"Svenja","1703":"12.10.1991","68":"30840","1940":"09.05.2022"},{"bewerbung_id":"5243","70":"Reinold","67":"Carolin","1703":"07.03.2000","68":"30841","1940":"05.05.2022"},{"bewerbung_id":"5245","70":"Becher","67":"Sabine","1703":"26.04.1989","68":"30842","1940":"09.05.2022"},{"bewerbung_id":"5276","70":"Tas","67":"Dilara","1703":"05.10.1999","68":"30507","1940":"06.03.2022"}],"views":{"V_NUMBER":1,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":2,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":3,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":4,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":5,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":6,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":7,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":8,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":9,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":10,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":11,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":12,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":13,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":14,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":15,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":16,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":17,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":18,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":19,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":20,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":21,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":22,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":23,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":24,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":25,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":26,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":27,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":28,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":29,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":30,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":31,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":32,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":33,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":34,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":35,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":36,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":37,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":38,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":39,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":40,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":41,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF<w:br/> bla nla nla","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}},{"views":{"V_NUMBER":42,"V_HOCHSCHULE":"HOCHSCHULE","V_BERUF":"BERUF","BERUFSERFAHRUNG":"BERUFSERFAHRUNG"}}]},"word\/styles.xml":[],"word\/settings.xml":[],"word\/webSettings.xml":[],"word\/footnotes.xml":[],"word\/endnotes.xml":[],"word\/header1.xml":{"fields":{"bewerbung_id":"5164","1674":"Weiterbildender Masterstudiengang Entscheidungsmanagement (Professional Public Decision Making) 2022-2025","1676":"PL - 10155"}},"word\/footer1.xml":[],"word\/header2.xml":[],"word\/footer2.xml":[],"word\/fontTable.xml":[],"word\/webextensions\/taskpanes.xml":[],"word\/webextensions\/webextension1.xml":[],"word\/theme\/theme1.xml":[],"docProps\/core.xml":[],"docProps\/app.xml":[]}',
            JSON_OBJECT_AS_ARRAY
        );
    }

}