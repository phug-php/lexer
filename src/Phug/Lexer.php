<?php

namespace Phug;

use Phug\Lexer\Scanner\AssignmentScanner;
use Phug\Lexer\Scanner\AttributeScanner;
use Phug\Lexer\Scanner\BlockScanner;
use Phug\Lexer\Scanner\CaseScanner;
use Phug\Lexer\Scanner\ClassScanner;
use Phug\Lexer\Scanner\CodeScanner;
use Phug\Lexer\Scanner\CommentScanner;
use Phug\Lexer\Scanner\ConditionalScanner;
use Phug\Lexer\Scanner\DoctypeScanner;
use Phug\Lexer\Scanner\DoScanner;
use Phug\Lexer\Scanner\EachScanner;
use Phug\Lexer\Scanner\ExpansionScanner;
use Phug\Lexer\Scanner\ExpressionScanner;
use Phug\Lexer\Scanner\FilterScanner;
use Phug\Lexer\Scanner\ForScanner;
use Phug\Lexer\Scanner\IdScanner;
use Phug\Lexer\Scanner\ImportScanner;
use Phug\Lexer\Scanner\IndentationScanner;
use Phug\Lexer\Scanner\MarkupScanner;
use Phug\Lexer\Scanner\MixinCallScanner;
use Phug\Lexer\Scanner\MixinScanner;
use Phug\Lexer\Scanner\NewLineScanner;
use Phug\Lexer\Scanner\TagScanner;
use Phug\Lexer\Scanner\TextLineScanner;
use Phug\Lexer\Scanner\TextScanner;
use Phug\Lexer\Scanner\VariableScanner;
use Phug\Lexer\Scanner\WhenScanner;
use Phug\Lexer\Scanner\WhileScanner;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\ExpressionToken;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\Lexer\Token\TextToken;
use Phug\Lexer\TokenInterface;
use Phug\Util\OptionInterface;
use Phug\Util\Partial\OptionTrait;

/**
 * Performs lexical analysis and provides a token generator.
 *
 * Tokens are defined as single units of code
 * (e.g. tag, class, id, attributeStart, attribute, attributeEnd)
 *
 * These will run through the parser and be converted to an AST
 *
 * The lexer works sequentially, `lex()` will return a generator and
 * you can read that generator in any manner you like.
 * The generator will produce valid tokens until the end of the passed
 * input.
 *
 * Usage example:
 * <code>
 *
 *     use Phug\Lexer;
 *
 *     $lexer = new Lexer();
 *
 *     foreach ($lexer->lex($pugInput) as $token)
 *          var_dump($token);
 *
 * </code>
 */
class Lexer implements OptionInterface
{
    use OptionTrait;

    const INDENT_SPACE = ' ';
    const INDENT_TAB = "\t";

    /**
     * The state of the current lexing process.
     *
     * @var State
     */
    private $state;

    /**
     * Creates a new lexer instance.
     *
     * The options should be an associative array
     *
     * Valid options are:
     *
     * stateClassName:  The class of the lexer state to use
     * level:           The internal indentation level to start on
     * indentStyle:     The indentation character (auto-detected)
     * indentWidth:     How often to repeat indentStyle (auto-detected)
     * encoding:        The encoding when working with mb_*-functions (auto-detected)
     * scanners:        An array of scans that will be performed
     *
     * Add a new scan to 'scans' to extend the lexer.
     * Notice that the parser needs to be able to handle newly introduced tokens provided by scanners.
     *
     * @param array|null $options the options passed to the lexer instance
     *
     * @throws \Exception
     */
    public function __construct(array $options = null)
    {
        $this->setOptionsRecursive([
            'state_class_name'   => State::class,
            'level'              => 0,
            'indent_style'       => null,
            'indent_width'       => null,
            'allow_mixed_indent' => true,
            'encoding'           => null,
            'scanners'           => [
                //TODO: Several of these are non-standard and need to be capsulated into extensions
                //Namely: ForScanner, DoScanner, VariableScanner
                'new_line'    => NewLineScanner::class,
                'indent'      => IndentationScanner::class,
                'import'      => ImportScanner::class,
                'block'       => BlockScanner::class,
                'conditional' => ConditionalScanner::class,
                'each'        => EachScanner::class,
                'case'        => CaseScanner::class,
                'when'        => WhenScanner::class,
                'do'          => DoScanner::class,
                'while'       => WhileScanner::class,
                'for'         => ForScanner::class,
                'mixin'       => MixinScanner::class,
                'mixin_call'  => MixinCallScanner::class,
                'doctype'     => DoctypeScanner::class,
                'tag'         => TagScanner::class,
                'class'       => ClassScanner::class,
                'id'          => IdScanner::class,
                'attribute'   => AttributeScanner::class,
                'assignment'  => AssignmentScanner::class,
                'variable'    => VariableScanner::class,
                'comment'     => CommentScanner::class,
                'filter'      => FilterScanner::class,
                'expression'  => ExpressionScanner::class,
                'code'        => CodeScanner::class,
                'markup'      => MarkupScanner::class,
                'expansion'   => ExpansionScanner::class,
                'text_line'   => TextLineScanner::class,
                //Notice that TextScanner is always added in lex(), as we'd basically disable extensions otherwise
                //As this array is replaced recursively, your extensions are either added or overwritten
                //If Text would be last one, every extension would end up as text, as text matches everything
            ],
        ], $options ?: []);

        $this->state = null;
    }

    /**
     * Returns the current scanners registered for the lexing process.
     *
     * @return ScannerInterface[]
     */
    public function getScanners()
    {
        return $this->getOption('scanners');
    }

    /**
     * Returns the state object of the current lexing process.
     *
     * @return State
     */
    public function getState()
    {
        if (!$this->state) {
            throw new \RuntimeException(
                'Failed to get state: No lexing process active. Use the `lex()`-method'
            );
        }

        return $this->state;
    }

    /**
     * Adds a new scanner class to use in the lexing process.
     *
     * The scanner class needs to extend Phug\Lexer\ScannerInterface. It can be the class name itself
     * or an instance of it.
     *
     * @param string                  $name
     * @param ScannerInterface|string $scanner
     * @param string                  $before
     *
     * @return $this
     */
    public function addScanner($name, $scanner, $before = null)
    {
        $this->filterScanner($scanner);

        //Quick return if we don't want to prepend
        if (!$before) {
            $this->setOption(['scanners', $name], $scanner);

            return $this;
        }

        $scanners = [];
        $scanners[$name] = $scanner;

        foreach ($this->getOption('scanners') as $scannerName => $classNameOrInstance) {
            if ($scannerName !== $name) {
                $scanners[$scannerName] = $classNameOrInstance;
            }
        }

        $this->setOption('scanners', $scanners);

        return $this;
    }

    /**
     * Returns a generator that will lex the passed input sequentially.
     *
     * If you don't move the generator, the lexer does nothing.
     * Only as soon as you iterate the generator or call `next()`/`current()` on it
     * the lexer will start its work and spit out tokens sequentially.
     * This approach requires less memory during the lexing process.
     *
     * The returned tokens are required to be `Phug\Lexer\TokenInterface` instances.
     *
     * @param string $input the pug-string to lex into tokens.
     * @param null   $path
     *
     * @return \Generator a generator that can be iterated sequentially
     */
    public function lex($input, $path = null)
    {
        $stateClassName = $this->getOption('state_class_name');

        if (!is_a($stateClassName, State::class, true)) {
            throw new \InvalidArgumentException(
                'state_class_name needs to be a valid '.State::class.' sub class'
            );
        }

        //Put together our initial state
        $this->state = new State($input, [
            'encoding'           => $this->getOption('encoding'),
            'indent_style'       => $this->getOption('indent_style'),
            'indent_width'       => $this->getOption('indent_width'),
            'allow_mixed_indent' => $this->getOption('allow_mixed_indent'),
            'level'              => $this->getOption('level'),
            'path'               => $path,
        ]);

        $scanners = $this->getOption('scanners');

        //We always scan for text at the very end.
        $scanners['final_plain_text'] = TextScanner::class;

        //Scan for tokens
        foreach ($this->state->loopScan($scanners) as $token) {
            yield $token;
        }

        //Free state
        $this->state = null;
    }

    public function dump($input)
    {
        if ($input instanceof TokenInterface) {
            return $this->dumpToken($input);
        }

        if (!($input instanceof \Generator) && !is_array($input)) {
            $input = $this->lex((string) $input);
        }

        $dumped = '';
        foreach ($input as $token) {
            $dumped .= $this->dump($token);
        }

        return $dumped;
    }

    private function dumpToken(TokenInterface $token)
    {
        $dumped = '';
        $suffix = '';
        switch (get_class($token)) {
            case IndentToken::class:
                $dumped = '->';
                break;
            case OutdentToken::class:
                $dumped = '<-';
                break;
            case NewLineToken::class:
                $dumped = '\n';
                $suffix = "\n";
                break;
            case AttributeStartToken::class:
                $dumped = '(';
                break;
            case AttributeToken::class:
                /** @var AttributeToken $token */
                $dumped = sprintf(
                    'Attr %s=%s (%s, %s)',
                    $token->getName() ?: '""',
                    $token->getValue() ?: '""',
                    $token->isEscaped() ? 'escaped' : 'unescaped',
                    $token->isChecked() ? 'checked' : 'unchecked'
                );
                break;
            case AttributeEndToken::class:
                $dumped = ')';
                break;
            case TextToken::class:
                /** @var TextToken $token */
                $dumped = 'Text '.$token->getValue();
                break;
            case ExpressionToken::class:
                /** @var ExpressionToken $token */
                $dumped = sprintf(
                    'Expr %s (%s, %s)',
                    $token->getValue() ?: '""',
                    $token->isEscaped() ? 'escaped' : 'unescaped',
                    $token->isChecked() ? 'checked' : 'unchecked'
                );
                break;
            default:
                $dumped = $this->getTokenName($token);
                break;
        }

        return "[$dumped]".$suffix;
    }

    private function filterScanner($scanner)
    {
        if (!is_subclass_of($scanner, ScannerInterface::class)) {
            throw new \InvalidArgumentException(
                "Scanner $scanner is not a valid ".ScannerInterface::class.' instance or extended class'
            );
        }
    }

    private function getTokenName($token)
    {
        return preg_replace('/Token$/', '', get_class($token));
    }
}
