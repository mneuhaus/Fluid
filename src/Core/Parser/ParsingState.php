<?php
namespace TYPO3Fluid\Fluid\Core\Parser;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\AbstractNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\RootNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3Fluid\Fluid\View;

/**
 * Stores all information relevant for one parsing pass - that is, the root node,
 * and the current stack of open nodes (nodeStack) and a variable container used
 * for PostParseFacets.
 */
class ParsingState implements ParsedTemplateInterface {

	/**
	 * Root node reference
	 *
	 * @var RootNode
	 */
	protected $rootNode;

	/**
	 * Array of node references currently open.
	 *
	 * @var array
	 */
	protected $nodeStack = array();

	/**
	 * Variable container where ViewHelpers implementing the PostParseFacet can
	 * store things in.
	 *
	 * @var VariableProviderInterface
	 */
	protected $variableContainer;

	/**
	 * The layout name of the current template or NULL if the template does not contain a layout definition
	 *
	 * @var AbstractNode
	 */
	protected $layoutNameNode;

	/**
	 * @var boolean
	 */
	protected $compilable = TRUE;

	/**
	 * @var ViewHelperResolver
	 */
	protected $viewHelperResolver;

	/**
	 * @param ViewHelperResolver $viewHelperResolver
	 * @return void
	 */
	public function setViewHelperResolver(ViewHelperResolver $viewHelperResolver) {
		$this->viewHelperResolver = $viewHelperResolver;
	}

	/**
	 * @return ViewHelperResolver
	 */
	public function getViewHelperResolver() {
		return $this->viewHelperResolver;
	}

	/**
	 * Injects a variable container. ViewHelpers implementing the PostParse
	 * Facet can store information inside this variableContainer.
	 *
	 * @param VariableProviderInterface $variableContainer
	 * @return void
	 */
	public function setVariableProvider(VariableProviderInterface $variableContainer) {
		$this->variableContainer = $variableContainer;
	}

	/**
	 * Set root node of this parsing state
	 *
	 * @param NodeInterface $rootNode
	 * @return void
	 */
	public function setRootNode(AbstractNode $rootNode) {
		$this->rootNode = $rootNode;
	}

	/**
	 * Get root node of this parsing state.
	 *
	 * @return NodeInterface The root node
	 */
	public function getRootNode() {
		return $this->rootNode;
	}

	/**
	 * Render the parsed template with rendering context
	 *
	 * @param RenderingContextInterface $renderingContext The rendering context to use
	 * @return string Rendered string
	 */
	public function render(RenderingContextInterface $renderingContext) {
		return $this->rootNode->evaluate($renderingContext);
	}

	/**
	 * Push a node to the node stack. The node stack holds all currently open
	 * templating tags.
	 *
	 * @param NodeInterface $node Node to push to node stack
	 * @return void
	 */
	public function pushNodeToStack(AbstractNode $node) {
		array_push($this->nodeStack, $node);
	}

	/**
	 * Get the top stack element, without removing it.
	 *
	 * @return NodeInterface the top stack element.
	 */
	public function getNodeFromStack() {
		return $this->nodeStack[count($this->nodeStack) - 1];
	}

	/**
	 * Pop the top stack element (=remove it) and return it back.
	 *
	 * @return NodeInterface the top stack element, which was removed.
	 */
	public function popNodeFromStack() {
		return array_pop($this->nodeStack);
	}

	/**
	 * Count the size of the node stack
	 *
	 * @return integer Number of elements on the node stack (i.e. number of currently open Fluid tags)
	 */
	public function countNodeStack() {
		return count($this->nodeStack);
	}

	/**
	 * Returns a variable container which will be then passed to the postParseFacet.
	 *
	 * @return VariableProviderInterface The variable container or NULL if none has been set yet
	 */
	public function getVariableContainer() {
		return $this->variableContainer;
	}

	/**
	 * Returns TRUE if the current template has a template defined via <f:layout name="..." />
	 *
	 * @return boolean
	 */
	public function hasLayout() {
		return $this->variableContainer->exists('layoutName');
	}

	/**
	 * Returns the name of the layout that is defined within the current template via <f:layout name="..." />
	 * If no layout is defined, this returns NULL
	 * This requires the current rendering context in order to be able to evaluate the layout name
	 *
	 * @param RenderingContextInterface $renderingContext
	 * @return string
	 * @throws View\Exception
	 */
	public function getLayoutName(RenderingContextInterface $renderingContext) {
		return $renderingContext->getVariableProvider()->get('layoutName');
	}

	/**
	 * @param RenderingContextInterface $renderingContext
	 * @return void
	 */
	public function addCompiledNamespaces(RenderingContextInterface $renderingContext) {
	}

	/**
	 * @return boolean
	 */
	public function isCompilable() {
		return $this->compilable;
	}

	/**
	 * @param boolean $compilable
	 */
	public function setCompilable($compilable) {
		$this->compilable = $compilable;
	}

	/**
	 * @return boolean
	 */
	public function isCompiled() {
		return FALSE;
	}
}
