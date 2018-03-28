<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\Tokens;

final class BlockStartAndEndFinder
{
    /**
     * @var int[]
     */
    private $contentToBlockType = [
        '(' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
        '[' => Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
    ];

    /**
     * @return int[]
     */
    public function findInTokensByBlockStart(Tokens $tokens, int $blockStart): array
    {
        $token = $tokens[$blockStart];

        $blockType = $this->getBlockTypeByContent($token->getContent());

        return [$blockStart, $tokens->findBlockEnd($blockType, $blockStart)];
    }

    /**
     * @return int[]|null
     */
    public function findInTokensByPositionAndContent(Tokens $tokens, int $position, string $content): ?array
    {
        $blockStart = $tokens->getNextTokenOfKind($position, [$content]);
        if ($blockStart === null) {
            return null;
        }

        $blockType = $this->getBlockTypeByContent($content);
        if ($blockType === null) {
            return null;
        }

        return [$blockStart, $tokens->findBlockEnd($blockType, $blockStart)];
    }

    private function getBlockTypeByContent(string $content): ?int
    {
        return $this->contentToBlockType[$content] ?? null;
    }
}