<?php

namespace Phug\Test;

use Phug\AbstractLexerModule;
use Phug\Lexer;
use Phug\LexerEvent;

//@codingStandardsIgnoreStart
class TestModule extends AbstractLexerModule
{
    public function getEventListeners()
    {
        return [
            LexerEvent::TOKEN => function (Lexer\TokenEvent $e) {
                if ($e->getToken() instanceof Lexer\Token\TagToken) {
                    $e->setToken(new Lexer\Token\ClassToken());
                }
            },
        ];
    }
}

class GeneratorTestModule extends AbstractLexerModule
{
    private function generateTokens()
    {
        yield (new Lexer\Token\TagToken())->setName('div');
        yield new Lexer\Token\ClassToken();
        yield new Lexer\Token\IdToken();
    }

    public function getEventListeners()
    {
        return [
            LexerEvent::TOKEN => function (Lexer\TokenEvent $e) {
                $token = $e->getToken();
                if ($token instanceof Lexer\Token\TagToken && $token->getName() === 'p') {
                    $e->setTokenGenerator($this->generateTokens());
                }
            },
        ];
    }
}

/**
 * @coversDefaultClass Phug\AbstractLexerModule
 */
class LexerModuleTest extends AbstractLexerTest
{
    /**
     * @covers ::<public>
     * @covers \Phug\Lexer::lex
     * @covers \Phug\Lexer\TokenEvent::__construct
     * @covers \Phug\Lexer\TokenEvent::getToken
     * @covers \Phug\Lexer\TokenEvent::setToken
     * @covers \Phug\Lexer::handleTokens
     * @covers \Phug\Lexer::getModuleBaseClassName
     */
    public function testTokenEvent()
    {
        self::assertTokens('p Test', [
            Lexer\Token\TagToken::class,
            Lexer\Token\TextToken::class,
        ]);

        $lexer = new Lexer(['modules' => [TestModule::class]]);

        self::assertTokens('p Test', [
            Lexer\Token\ClassToken::class,
            Lexer\Token\TextToken::class,
        ], $lexer);
    }

    /**
     * @covers ::<public>
     * @covers \Phug\Lexer::lex
     * @covers \Phug\Lexer\TokenEvent::__construct
     * @covers \Phug\Lexer\TokenEvent::getTokenGenerator
     * @covers \Phug\Lexer\TokenEvent::setTokenGenerator
     * @covers \Phug\Lexer::handleTokens
     * @covers \Phug\Lexer::getModuleBaseClassName
     */
    public function testTokenGeneratorEvent()
    {
        self::assertTokens('p Test', [
            Lexer\Token\TagToken::class,
            Lexer\Token\TextToken::class,
        ]);

        $lexer = new Lexer(['modules' => [GeneratorTestModule::class]]);

        self::assertTokens('p Test', [
            Lexer\Token\TagToken::class,
            Lexer\Token\ClassToken::class,
            Lexer\Token\IdToken::class,
            Lexer\Token\TextToken::class,
        ], $lexer);
    }
}
//@codingStandardsIgnoreEnd
