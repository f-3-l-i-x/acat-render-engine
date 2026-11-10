# acat-render-engine

Renders `.docx` Word documents as templates: fields, repeating blocks and conditional logic can be
defined directly inside the document by an author in Word, and are then filled in and evaluated
against PHP data at render time.

## Requirements

- PHP >= 8.5
- Extensions: `dom`, `json`, `mbstring`, `xml`, `zip`, `libxml`

## Installation

```bash
composer require acat/render-engine
```

## How it works

A `.docx` file is a zip archive of XML parts (`word/document.xml`, `word/settings.xml`, headers,
footers, ...). The engine:

1. Opens the archive and reads its content parts (`ACAT\Document\Word\WordDocument`).
2. **Normalizes** each part: Word frequently splits a placeholder across several `<w:t>` runs
   whenever the text has mixed formatting (e.g. `${F:101}` typed with a different font on the
   digits). The `Normalizer` finds these runs and merges them back into a single text node before
   parsing continues (`ACAT\Parser\Normalizer\Normalizer`).
3. **Generates tags**: every `${...}` marker found in the merged text is turned into a small custom
   XML element (`acat:field`, `acat:text`, `acat:view`, `acat:block`, `acat:condition`) inserted
   right into the Word XML, next to the surrounding `w:t`/`w:r`/`w:p` nodes
   (`ACAT\Parser\Tag\TagGenerator`).
4. **Collects elements**: `ACAT\Parser\Element\ElementGenerator` walks the tagged document and
   groups everything into fields, text, views, conditions and blocks (with their child elements).
5. **Renders**: `ACAT\Render\RenderEngine` replaces each field/text/view element with the
   corresponding value, evaluates conditions and repeats blocks, then `WordDocument::save()` writes
   the modified parts back into the `.docx` archive.

## Placeholder syntax

Markers are written directly into the Word document text as `${...}`.

| Marker | Meaning |
|---|---|
| `${F:id}` | **Field.** Replaced with the plain-text value for `id`. |
| `${T:text}` | **Static text.** Replaced with `text` itself; mainly useful together with a condition, e.g. to print a fixed label only when a condition is met. |
| `${V:id}` | **View.** Like a field, but its value may contain the literal substring `<w:br/>` to insert line breaks — useful for multi-line values (e.g. an address) that a plain field can't represent. |
| `${B:0}` / `${B:1}` | **Block start / block end.** Marks the boundaries of a repeating region (see below). |
| `${C:id:expression:action}` | **Condition.** Evaluates `expression` against the value of field `id` and, if true, performs `action` (see below). |

### Fields, text and views

```
Customer number: ${F:101}
```

If the rendered data provides a value for field `101`, the marker is replaced with it; if no value
is provided, the marker is simply removed (a missing value renders as nothing, not as an error).

### Conditions

```
${C:101:<>:0}
```

Supported operators: `=`, `<>`, `<`, `>`, `>=`, `<=`. The right-hand side can be a literal
(`${C:101:=:1000}`) or another field's value, referenced as `FIELD_COMPARE_<id>`. Values that look
like a `d.m.Y` date are normalized before comparing so `01.02.2024` and `2024-02-01` compare
correctly.

The `action` selects what gets removed when the condition is true:

| Action | Effect |
|---|---|
| `0` | Delete the whole paragraph the condition is in. |
| `1` | Delete every remaining field/text/view inside the condition's element. |
| `2` | Delete the next field/text/view marker that follows. |
| `3` | Delete everything remaining after the condition in its parent run. |
| `4` | Delete everything up to (and including) the next field/text/view/condition marker. |

A condition marker is always removed after evaluation, whether or not it triggered — it never
appears in the rendered output.

### Blocks

A block repeats everything between `${B:0}` and `${B:1}` once per row of data. Blocks may wrap a
single word (`w:t`), a paragraph (`w:p`), a table row (`w:tr`) or a table cell (`w:tc`) — the engine
detects which one applies from the position of the start/end markers. Start and end markers of one
block must be siblings (or in sibling paragraphs/rows/cells); blocks cannot be nested.

```
${B:0}${F:101} – ${F:102}${B:1}
```

placed inside a table row and fed three rows of data repeats that row three times, once per entry.

## Rendering a document

```php
use ACAT\Document\Word\WordDocument;
use ACAT\Render\RenderEngine;

$document = new WordDocument('/path/to/template.docx');

$values = [
    'word/document.xml' => [
        'fields' => [
            '101' => 'Ada Lovelace',
        ],
        'views' => [
            '102' => "Erste Zeile<w:br/>Zweite Zeile",
        ],
        'blocks' => [
            // block 0 = the first ${B:0}...${B:1} pair found in the document
            0 => [
                'fields' => [
                    0 => ['101' => 'Row one'],
                    1 => ['101' => 'Row two'],
                ],
            ],
        ],
    ],
];

$engine = new RenderEngine($logger); // $logger is optional, any PSR-3 LoggerInterface
$engine->render($document, $values);
```

Values are keyed by the content part they belong to (`word/document.xml`, or a header/footer part),
then by `fields`, `views` and `blocks`. Blocks are addressed by their numeric position in the
document (`0` for the first block found, `1` for the second, and so on), and each block's `fields`/
`views` is itself a list of one value-set per repeated row.

## Inspecting a template

`ACAT\Render\RecordStructure` parses a template without rendering it and reports which field,
view, condition and block IDs it references — useful for validating a template or building a form
for the data it expects:

```php
use ACAT\Document\Word\WordDocument;
use ACAT\Render\RecordStructure;

$structure = new RecordStructure(new WordDocument('/path/to/template.docx'));
$recordStructure = $structure->getRecordStructure();
// ['word/document.xml' => ['views' => [...], 'fields' => [...], 'blocks' => [...], 'conditions' => [...]]]
```

`validate()` checks the same document for `${B:0}`/`${B:1}` markers that could not be paired into a
block — e.g. one of them was accidentally deleted or duplicated while editing the template. An
unpaired marker is never rendered or cleaned up; it silently stays in the output as a raw XML
element, so this is meant to be checked before rendering:

```php
$problems = $structure->validate();
// [] if the template is fine, otherwise e.g. ['word/document.xml' => ['1 block marker(s) could not be paired into a start/end block']]
```

## Password-protecting a document

`WordDocument::protect(?string $password = null)` marks the document read-only using Word's own
document-protection mechanism (a random password is generated if none is given).

**This is not encryption and not access control.** It is the same "Restrict Editing" feature Word
itself offers — a UI-level restriction that Word enforces while editing, not a way to keep the
document's contents confidential. Anyone with the file can read it normally, and the restriction is
trivially removed by deleting the `w:documentProtection` element from `word/settings.xml` or by
using any of the widely available password-removal tools. Use it to discourage accidental edits,
not to protect sensitive content.

## Testing

```bash
composer install
vendor/bin/phpunit
```

## License

MIT, see [LICENSE](LICENSE) — free to use, modify and distribute, provided the copyright notice
(Ronny Krämer, Akademie für Weiterbildung, Universität Bremen) stays in every copy.
