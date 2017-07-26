<?php

namespace Phug\Test;

use Phug\AbstractLexerModule;
use Phug\Lexer;
use Phug\Lexer\Event\LexEvent;
use Phug\Lexer\Event\TokenEvent;
use Phug\LexerEvent;

//@codingStandardsIgnoreStart
class TestModule extends AbstractLexerModule
{
    public function getEventListeners()
    {
        return [
            LexerEvent::TOKEN => function (TokenEvent $event) {
                if ($event->getToken() instanceof Lexer\Token\TagToken) {
                    $event->setToken(new Lexer\Token\ClassToken());
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
            LexerEvent::TOKEN => function (TokenEvent $event) {
                $token = $event->getToken();
                if ($token instanceof Lexer\Token\TagToken && $token->getName() === 'p') {
                    $event->setTokenGenerator($this->generateTokens());
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
     * @covers \Phug\Lexer\Event\TokenEvent::__construct
     * @covers \Phug\Lexer\Event\TokenEvent::getToken
     * @covers \Phug\Lexer\Event\TokenEvent::setToken
     * @covers \Phug\Lexer::handleTokens
     * @covers \Phug\Lexer::getModuleBaseClassName
     */
    public function testTokenEvent()
    {
        self::assertTokens('p Test', [
            Lexer\Token\TagToken::class,
            Lexer\Token\TextToken::class,
        ]);

        $lexer = new Lexer(['lexer_modules' => [TestModule::class]]);

        self::assertTokens('p Test', [
            Lexer\Token\ClassToken::class,
            Lexer\Token\TextToken::class,
        ], $lexer);
    }

    /**
     * @covers ::<public>
     * @covers \Phug\Lexer::lex
     * @covers \Phug\Lexer::__construct
     * @covers \Phug\Lexer\Event\LexEvent::__construct
     * @covers \Phug\Lexer\Event\LexEvent::getInput
     * @covers \Phug\Lexer\Event\LexEvent::setInput
     * @covers \Phug\Lexer\Event\LexEvent::getPath
     * @covers \Phug\Lexer\Event\LexEvent::setPath
     * @covers \Phug\Lexer\Event\LexEvent::getStateClassName
     * @covers \Phug\Lexer\Event\LexEvent::setStateClassName
     * @covers \Phug\Lexer\Event\LexEvent::getStateOptions
     * @covers \Phug\Lexer\Event\LexEvent::setStateOptions
     * @covers \Phug\Lexer::handleTokens
     * @covers \Phug\Lexer::getModuleBaseClassName
     */
    public function testLexEvent()
    {
        $lexer = new Lexer([
            'on_lex' => function (LexEvent $event) {
                $event->setInput('.bar:'.$event->getInput());
            },
            'on_token' => function (TokenEvent $event) {
                if ($event->getToken() instanceof Lexer\Token\ClassToken) {
                    $event->setToken(new Lexer\Token\TagToken());
                }
            },
        ]);

        self::assertTokens('.foo Test', [
            Lexer\Token\TagToken::class,
            Lexer\Token\ExpansionToken::class,
            Lexer\Token\TagToken::class,
            Lexer\Token\TextToken::class,
        ], $lexer);
    }

    /**
     * @covers ::<public>
     * @covers \Phug\Lexer::lex
     * @covers \Phug\Lexer\Event\TokenEvent::__construct
     * @covers \Phug\Lexer\Event\TokenEvent::getTokenGenerator
     * @covers \Phug\Lexer\Event\TokenEvent::setTokenGenerator
     * @covers \Phug\Lexer::handleTokens
     * @covers \Phug\Lexer::getModuleBaseClassName
     */
    public function testTokenGeneratorEvent()
    {
        self::assertTokens('p Test', [
            Lexer\Token\TagToken::class,
            Lexer\Token\TextToken::class,
        ]);

        $lexer = new Lexer(['lexer_modules' => [GeneratorTestModule::class]]);

        self::assertTokens('p Test', [
            Lexer\Token\TagToken::class,
            Lexer\Token\ClassToken::class,
            Lexer\Token\IdToken::class,
            Lexer\Token\TextToken::class,
        ], $lexer);
    }
}
//@codingStandardsIgnoreEnd
