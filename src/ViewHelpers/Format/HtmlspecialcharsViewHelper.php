<?php
namespace TYPO3Fluid\Fluid\ViewHelpers\Format;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Applies htmlspecialchars() escaping to a value
 *
 * @see http://www.php.net/manual/function.htmlspecialchars.php
 *
 * = Examples =
 *
 * <code title="default notation">
 * <f:format.htmlspecialchars>{text}</f:format.htmlspecialchars>
 * </code>
 * <output>
 * Text with & " ' < > * replaced by HTML entities (htmlspecialchars applied).
 * </output>
 *
 * <code title="inline notation">
 * {text -> f:format.htmlspecialchars(encoding: 'ISO-8859-1')}
 * </code>
 * <output>
 * Text with & " ' < > * replaced by HTML entities (htmlspecialchars applied).
 * </output>
 *
 * @api
 */
class HtmlspecialcharsViewHelper extends AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $escapeChildren = FALSE;

	/**
	 * Disable the output escaping interceptor so that the value is not htmlspecialchar'd twice
	 *
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('value', 'string', 'Value to format', FALSE, NULL);
		$this->registerArgument('keepQuotes', 'boolean', 'If TRUE quotes will not be replaced (ENT_NOQUOTES)', FALSE, FALSE);
		$this->registerArgument('encoding', 'string', 'Encoding', FALSE, 'UTF-8');
		$this->registerArgument('doubleEncode', 'boolean', 'If FALSE html entities will not be encoded', FALSE, TRUE);
	}

	/**
	 * Escapes special characters with their escaped counterparts as needed using PHPs htmlspecialchars() function.
	 *
	 * @return string the altered string
	 * @see http://www.php.net/manual/function.htmlspecialchars.php
	 * @api
	 */
	public function render() {
		$value = $this->arguments['value'];
		$keepQuotes = $this->arguments['keepQuotes'];
		$encoding = $this->arguments['encoding'];
		$doubleEncode = $this->arguments['doubleEncode'];
		if ($value === NULL) {
			$value = $this->renderChildren();
		}

		if (!is_string($value) && !(is_object($value) && method_exists($value, '__toString'))) {
			return $value;
		}
		$flags = $keepQuotes ? ENT_NOQUOTES : ENT_COMPAT;

		return htmlspecialchars($value, $flags, $encoding, $doubleEncode);
	}

	/**
	 * This ViewHelper is used a *lot* because it is used by the escape interceptor.
	 * Therefore we render it to raw PHP code during compilation
	 *
	 * @param string $argumentsName
	 * @param string $closureName
	 * @param string $initializationPhpCode
	 * @param ViewHelperNode $node
	 * @param TemplateCompiler $compiler
	 * @return string
	 */
	public function compile($argumentsName, $closureName, &$initializationPhpCode, ViewHelperNode $node, TemplateCompiler $compiler) {
		$valueVariableName = $compiler->variableName('value');
		$initializationPhpCode .= sprintf('%1$s = (%2$s[\'value\'] !== NULL ? %2$s[\'value\'] : %3$s());', $valueVariableName, $argumentsName, $closureName) . chr(10);

		return sprintf('!is_string(%1$s) && !(is_object(%1$s) && method_exists(%1$s, \'__toString\')) ? %1$s : htmlspecialchars(%1$s, (%2$s[\'keepQuotes\'] ? ENT_NOQUOTES : ENT_COMPAT), %2$s[\'encoding\'], %2$s[\'doubleEncode\'])',
			$valueVariableName, $argumentsName);
	}
}
