<?php

namespace Phug\Lexer;

use Phug\Reader;
use Phug\Lexer;
use Phug\LexerException;

/**
 * Represents the state of a currently running lexing process.
 */
class State
{

    /**
     * Holds an array of configuration options.
     *
     * @var array
     */
    private $options;

    /**
     * Stores the current indentation level of the lexing process.
     *
     * @var int
     */
    private $level;

    /**
     * Contains the current `Phug\Reader` instance used by the lexer.
     *
     * @var Reader $reader
     */
    private $reader;

    /**
     * Contains the currently detected indentation style.
     *
     * @var string
     */
    private $indentStyle;

    /**
     * Contains the currently detected indentation width.
     *
     * @var int
     */
    private $indentWidth;

    /**
     * Creates a new instance of the state.
     *
     * @param $input
     * @param array $options
     */
    public function __construct($input, array $options)
    {

        $this->options = array_replace([
            'readerClassName' => Reader::class,
            'encoding' => null,
            'level' => 0,
            'indentWidth' => null,
            'indentStyle' => null
        ], $options ?: []);


        $readerClassName = $this->options['readerClassName'];
        if (!is_a($readerClassName, Reader::class, true)) {
            throw new \InvalidArgumentException(
                'Configuration option `readerClassName` needs to be a valid FQCN of a class that extends '.
                Reader::class
            );
        }

        $this->reader = new $readerClassName(
            $input,
            $this->options['encoding']
        );
        $this->indentStyle = $this->options['indentWidth'];
        $this->indentWidth = $this->options['indentStyle'];
        $this->level = $this->options['level'];

        //This will strip \r, \0 etc. from the input
        $this->reader->normalize();
    }

    /**
     * Returns the current reader instance that is used for parsing the input.
     *
     * @return Reader
     */
    public function getReader()
    {

        return $this->reader;
    }

    /**
     * Returns the current indentation level the reader operates on.
     *
     * @return int|mixed
     */
    public function getLevel()
    {

        return $this->level;
    }

    /**
     * Sets the current indentation level to a new one.
     *
     * @param $level
     * @return $this
     */
    public function setLevel($level)
    {

        $this->level = $level;

        return $this;
    }

    /**
     * Returns the currently used indentation style.
     *
     * @return string
     */
    public function getIndentStyle()
    {

        return $this->indentStyle;
    }

    /**
     * Sets the current indentation style to a new one.
     *
     * The value needs to be one of the `Lexer::INDENT_*` constants, but you can also just
     * pass either a single space or a single tab for the respective style.
     *
     * @param $indentStyle
     * @return $this
     */
    public function setIndentStyle($indentStyle)
    {

        if (!in_array($indentStyle, [null, Lexer::INDENT_TAB, Lexer::INDENT_SPACE])) {
            throw new \InvalidArgumentException(
                "indentStyle needs to be null or one of the INDENT_* constants of the lexer"
            );
        }

        $this->indentStyle = $indentStyle;

        return $this;
    }

    /**
     * Returns the currently used indentation width.
     *
     * @return int
     */
    public function getIndentWidth()
    {
        return $this->indentWidth;
    }

    /**
     * Sets the currently used indentation width.
     *
     * The value of this specifies if e.g. 2 spaces make up one indentation level or 4.
     *
     * @param $indentWidth
     * @return $this
     */
    public function setIndentWidth($indentWidth)
    {

        if (!is_null($indentWidth) &&
            (!is_int($indentWidth) || $indentWidth < 1)
        ) {
            throw new \InvalidArgumentException(
                "indentWidth needs to be null or an integer above 0"
            );
        }

        $this->indentWidth = $indentWidth;

        return $this;
    }

    /**
     * Runs all passed scanners once on the input string.
     *
     * The first scan that returns valid tokens will stop the scanning and
     * yields these tokens. If you want to continuously scan on something, rather
     * use the `loopScan`-method
     *
     * @param array|string $scanners the scanners to run
     *
     * @return \Generator the generator yielding all tokens found
     * @throws LexerException
     */
    public function scan($scanners)
    {

        $scanners = $this->filterScanners($scanners);

        foreach ($scanners as $key => $scanner) {

            /** @var ScannerInterface $scanner */
            $success = false;
            foreach ($scanner->scan($this) as $token) {
                if (!($token instanceof TokenInterface)) {
                    $this->throwException(
                        "Scanner with key $key generated a result that is not a ".TokenInterface::class
                    );
                }

                yield $token;
                $success = true;
            }

            if ($success) {
                return;
            }
        }
    }

    /**
     * Continuously scans with all scanners passed as the first argument.
     *
     * If the second argument is true, it will throw an exception if none of the scanners
     * produced any valid tokens. The reading also stops when the end of the input as been reached.
     *
     * @param $scanners
     * @param bool $required
     * @return \Generator
     * @throws LexerException
     */
    public function loopScan($scanners, $required = false)
    {

        while ($this->reader->hasLength()) {
            $success = false;
            foreach ($this->scan($scanners) as $token) {
                $success = true;
                yield $token;
            }

            if (!$success) {
                break;
            }
        }

        if ($this->reader->hasLength() && $required) {
            $this->throwException(
                "Unexpected ".$this->reader->peek(20)
            );
        }
    }

    /**
     * Creates a new instance of a token.
     *
     * The token automatically receives line/offset/level information through this method.
     *
     * @param string $className the class name of the token
     *
     * @return array the token array
     */
    public function createToken($className)
    {

        if (!is_subclass_of($className, TokenInterface::class)) {
            $this->throwException(
                "$className is not a valid token sub-class"
            );
        }

        return new $className(
            $this->getReader()->getLine(),
            $this->getReader()->getOffset(),
            $this->level
        );
    }

    /**
     * Quickly scans for a token by a single regular expression pattern.
     *
     * If the pattern matches, this method will yield a new token. If not, it will yield nothing
     *
     * All named capture groups are converted to `set*()`-methods, e.g.
     * `(?:<name>[a-z]+)` will automatically call `setName(<matchedValue>)` on the token.
     *
     * This method could be written without generators, but the way its designed is easier to use
     * in scanners as you can simply return it's value without having to check for it to be null.
     *
     *
     * @param $className
     * @param $pattern
     * @param null $modifiers
     * @return TokenInterface|null
     */
    public function scanToken($className, $pattern, $modifiers = null)
    {

        if (!$this->reader->match($pattern, $modifiers)) {
            return;
        }

        $data = $this->reader->getMatchData();

        $token = $this->createToken($className);
        $this->reader->consume();
        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (method_exists($token, $method)) {
                call_user_func([$token, $method], $value);
            }
        }

        yield $token;
    }

    /**
     * Filters and validates the passed scanners.
     *
     * This method makes sure that all scanners given are turned into their respective instances.
     *
     * @param $scanners
     * @return array
     */
    private function filterScanners($scanners)
    {

        $scannerInstances = [];
        $scanners = is_array($scanners) ? $scanners : [$scanners];
        foreach ($scanners as $key => $scanner) {
            if (!is_a($scanner, ScannerInterface::class, true)) {
                throw new \InvalidArgumentException(
                    "The passed scanner with key `$key` doesn't seem to be either a valid ".ScannerInterface::class.
                    " instance or extended class"
                );
            }

            $scannerInstances[] = $scanner instanceof ScannerInterface
                ? $scanner
                : new $scanner();
        }

        return $scannerInstances;
    }

    /**
     * Throws a lexer-exception.
     *
     * The current line and offset of the exception
     * get automatically appended to the message
     *
     * @param string $message A meaningful error message
     *
     * @throws LexerException
     */
    public function throwException($message)
    {

        $pattern = "Failed to lex: %s \nNear: %s \nLine: %s \nOffset: %s \nPosition: %s";

        throw new LexerException(vsprintf($pattern, [
            $message,
            $this->reader->peek(20),
            $this->reader->getLine(),
            $this->reader->getOffset(),
            $this->reader->getPosition()
        ]));
    }
}
