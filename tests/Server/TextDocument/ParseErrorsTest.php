<?php
declare(strict_types = 1);

namespace LanguageServer\Tests\Server\TextDocument;

use PHPUnit\Framework\TestCase;
use LanguageServer\Tests\MockProtocolStream;
use LanguageServer\{Server, Client, LanguageClient, Project};
use LanguageServer\Protocol\{TextDocumentIdentifier, TextDocumentItem, DiagnosticSeverity};

class ParseErrorsTest extends TestCase
{
    /**
     * @var Server\TextDocument
     */
    private $textDocument;

    private $args;

    public function setUp()
    {
        $client = new LanguageClient(new MockProtocolStream());
        $client->textDocument = new class($this->args) extends Client\TextDocument {
            private $args;
            public function __construct(&$args)
            {
                parent::__construct(new MockProtocolStream());
                $this->args = &$args;
            }
            public function publishDiagnostics(string $uri, array $diagnostics)
            {
                $this->args = func_get_args();
            }
        };
        $project = new Project($client);
        $this->textDocument = new Server\TextDocument($project, $client);
    }

    private function openFile($file) {
        $textDocumentItem = new TextDocumentItem();
        $textDocumentItem->uri = 'whatever';
        $textDocumentItem->languageId = 'php';
        $textDocumentItem->version = 1;
        $textDocumentItem->text = file_get_contents($file);
        $this->textDocument->didOpen($textDocumentItem);
    }

    public function testParseErrorsArePublishedAsDiagnostics()
    {
        $this->openFile(__DIR__ . '/../../../fixtures/invalid_file.php');
        $this->assertEquals([
            'whatever',
            [[
                'range' => [
                    'start' => [
                        'line' => 2,
                        'character' => 10
                    ],
                    'end' => [
                        'line' => 2,
                        'character' => 15
                    ]
                ],
                'severity' => DiagnosticSeverity::ERROR,
                'code' => null,
                'source' => 'php',
                'message' => "Syntax error, unexpected T_CLASS, expecting T_STRING"
            ]]
        ], json_decode(json_encode($this->args), true));
    }

    public function testParseErrorsWithOnlyStartLine()
    {
        $this->openFile(__DIR__ . '/../../../fixtures/namespace_not_first.php');
        $this->assertEquals([
            'whatever',
            [[
                'range' => [
                    'start' => [
                        'line' => 4,
                        'character' => 0
                    ],
                    'end' => [
                        'line' => 4,
                        'character' => 0
                    ]
                ],
                'severity' => DiagnosticSeverity::ERROR,
                'code' => null,
                'source' => 'php',
                'message' => "Namespace declaration statement has to be the very first statement in the script"
            ]]
        ], json_decode(json_encode($this->args), true));
    }
}
