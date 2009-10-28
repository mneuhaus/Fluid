<?php
declare(ENCODING = 'utf-8');
namespace F3\Fluid\ViewHelpers\Uri;

/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

require_once(__DIR__ . '/../ViewHelperBaseTestcase.php');

/**
 * Testcase for the resource uri view helper
 *
 * @version $Id: AliasViewHelper.php 2614 2009-06-15 18:13:18Z bwaidelich $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 */
class ResourceViewHelperTest extends \F3\Fluid\ViewHelpers\ViewHelperBaseTestcase {

	/**
	 * var \F3\Fluid\ViewHelpers\Uri\ResourceViewHelper
	 */
	protected $viewHelper;

	public function setUp() {
		parent::setUp();
		$this->mockResourcePublisher = $this->getMock('F3\FLOW3\Resource\Publisher');
		$this->viewHelper = $this->getMock($this->buildAccessibleProxy('F3\Fluid\ViewHelpers\Uri\ResourceViewHelper'), array('renderChildren'), array(), '', FALSE);
		$this->viewHelper->injectResourcePublisher($this->mockResourcePublisher);
		$this->injectDependenciesIntoViewHelper($this->viewHelper);
		$this->viewHelper->initializeArguments();
	}

	/**
	 * @test
	 * @author Christopher Hlubek <hlubek@networkteam.com>
	 */
	public function renderUsesCurrentControllerPackageKeyToBuildTheResourceURI() {
		$this->mockResourcePublisher->expects($this->atLeastOnce())->method('getRelativeMirrorDirectory')->will($this->returnValue('Resources/'));
		$this->request->expects($this->atLeastOnce())->method('getControllerPackageKey')->will($this->returnValue('PackageKey'));

		$resourceUri = $this->viewHelper->render('foo');
		$this->assertEquals('Resources/Packages/PackageKey/foo', $resourceUri);
	}

	/**
	 * @test
	 * @author Bastian Waidelich <bastian@typo3.org>
	 */
	public function renderUsesCustomPackageKeyIfSpecified() {
		$this->mockResourcePublisher->expects($this->atLeastOnce())->method('getRelativeMirrorDirectory')->will($this->returnValue('Resources/'));
		$resourceUri = $this->viewHelper->render('foo', FALSE, 'SomePackage');
		$this->assertEquals('Resources/Packages/SomePackage/foo', $resourceUri);
	}

	/**
	 * @test
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 */
	public function renderUsesConfiguredMirrorPath() {
		$this->mockResourcePublisher->expects($this->atLeastOnce())->method('getRelativeMirrorDirectory')->will($this->returnValue('CustomMirrorDirectory/'));
		$resourceUri = $this->viewHelper->render('foo', FALSE, 'SomePackage');
		$this->assertEquals('CustomMirrorDirectory/Packages/SomePackage/foo', $resourceUri);
	}

	/**
	 * @test
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 */
	public function renderReturnsAbsoluteUrlIfRequested() {
		$this->request->expects($this->atLeastOnce())->method('getBaseURI')->will($this->returnValue('http://host.name/'));
		$this->mockResourcePublisher->expects($this->atLeastOnce())->method('getRelativeMirrorDirectory')->will($this->returnValue('CustomMirrorDirectory/'));
		$resourceUri = $this->viewHelper->render('foo', TRUE, 'SomePackage');
		$this->assertEquals('http://host.name/CustomMirrorDirectory/Packages/SomePackage/foo', $resourceUri);
	}

}

?>