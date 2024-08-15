<?php

namespace ACAT\Parser;

/**
 *
 */
class ParserConstants
{

    /**
     *
     */
    public const string ACAT_VIEW_NODE = 'acat:view';
    /**
     *
     */
    public const string ACAT_FIELD_NODE = 'acat:field';
    /**
     *
     */
    public const string ACAT_BLOCK_NODE = 'acat:block';
    /**
     *
     */
    public const string ACAT_CONDITION_NODE = 'acat:condition';
    /**
     *
     */
    public const string ACAT_TEXT_NODES = 'acat:text';
    /**
     *
     */
    public const string WORD_TEXT_NODES = "//w:t";
    /**
     *
     */
    public const string WORD_LINE_BREAK_NODE_PATTERN = "/(<w:br\/>)/";
    /**
     *
     */
    public const string WORD_LINE_BREAK_NODE = "<w:br/>";
    /**
     *
     */
    public const string HTML_TEXT_NODES = "//text()";
    /**
     *
     */
    public const string MARKER_REG_EX = '/\${[a-zA-Z\d_\-<>=.:\!äÄöÖüÜß&()\s]*}/iu';
    /**
     * @var array|string[]
     */
    public static array $wordNamespaces = [
        'wpc'   => 'https://schemas.microsoft.com/office/word/2010/wordprocessingCanvas',
        'cx'    => 'https://schemas.microsoft.com/office/drawing/2014/chartex',
        'mc'    => 'https://schemas.openxmlformats.org/markup-compatibility/2006',
        'o'     => 'urn:schemas-microsoft-com:office:office',
        'r'     => 'https://schemas.openxmlformats.org/officaeDocument/2006/relationships',
        'm'     => 'https://schemas.openxmlformats.org/officeDocument/2006/math',
        'v'     => 'urn:schemas-microsoft-com:vml',
        'w'     => 'https://schemas.openxmlformats.org/wordprocessingml/2006/main',
        'wp14'  => 'https://schemas.microsoft.com/office/word/2010/wordprocessingDrawing',
        'wp'    => 'https://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing',
        'w10'   => 'urn:schemas-microsoft-com:office:word',
        'w14'   => 'https://schemas.microsoft.com/office/word/2010/wordml',
        'w15'   => 'https://schemas.microsoft.com/office/word/2012/wordml',
        'w16se' => 'https://schemas.microsoft.com/office/word/2015/wordml/symex',
        'wpg'   => 'https://schemas.microsoft.com/office/word/2010/wordprocessingGroup',
        'wpi'   => 'https://schemas.microsoft.com/office/word/2010/wordprocessingInk',
        'wne'   => 'https://schemas.microsoft.com/office/word/2006/wordml',
        'wps'   => 'https://schemas.microsoft.com/office/word/2010/wordprocessingShape',
        'a'     => 'https://schemas.openxmlformats.org/drawingml/2006/main',
        'acat'  => 'https://schemas.acat.akademie.uni-bremen.de'
    ];

}