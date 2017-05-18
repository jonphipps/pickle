<?php


namespace GD\Tests;

use GD\Exceptions\TestDoesNotFileExists;

class AppendBrowserFileTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->default_test_type = 'browser';
        $this->setupFolderAndAppendBrowserFile();
    }

    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
        $this->cleanUpFile();
    }

    public function testBrowserShouldSetContentOfFileToAppend()
    {

        $path = $this->gd->setContext('browser')->getDestinationFolderRoot() . '/TestAppendTest.php';
        \PHPUnit_Framework_Assert::assertFileExists($path);

        $path = 'tests/features/test_append.feature';

        $this->gd->setPathToFeature($path)
            ->appendFeatures();

        $this->assertNotNull($this->gd->getAppendBrowserTest()->getExistingTestContent());

        $content = $this->gd->getAppendBrowserTest()->getUpdatedContent();
        
        $this->assertContains("thenICanNotGoIntoEditMode", $content);
        $this->assertContains("andTheLastNameOfFoo", $content);
        $this->assertContains("thenICanSeeTheFirstNameOfFoo", $content);
        $this->assertContains("andIGoToLookAtTheProfileOfUserBar", $content);
        $this->assertContains("givenIAmLoggedInAsUserFoo", $content);
        $this->assertContains("testGuestViewsProfile", $content);
    }

    public function testShouldNotAddDuplicateMethodsForBrowserAppend()
    {

        $path = 'tests/features/test_append.feature';

        $this->gd->setContext('browser')
            ->setPathToFeature($path)
            ->appendFeatures();
        
        $string = $this->gd->getAppendBrowserTest()->getDuskClassAndMethodsString();
        $found = substr_count($string, 'protected function givenIHaveAProfileCreated');

        \PHPUnit_Framework_Assert::assertEquals(1, $found);
    }

    public function testCompareBrowserResultsToFixture()
    {
        $results_path = $this->gd
                ->setContext('browser')
                ->getDestinationFolderRoot() . '/TestAppendTest.php';

        $path = 'tests/features/test_append.feature';

        $this->gd->setPathToFeature($path)
            ->appendFeatures();

        $results_content = $this->file->get($results_path);

        $this->assertContains("protected function thenICanNotGoIntoEditMode", $results_content);
        $this->assertContains("protected function andTheLastNameOfFoo", $results_content);
        $this->assertContains("protected function thenICanSeeTheFirstNameOfFoo", $results_content);
        $this->assertContains("protected function andIGoToLookAtTheProfileOfUserBar", $results_content);
        $this->assertContains("protected function givenIAmLoggedInAsUserFoo", $results_content);
        $this->assertContains("public function testGuestViewsProfile", $results_content);

        $string = '
        $this->givenIAmLoggedInAsUserFoo();
        $this->andIGoToLookAtTheProfileOfUserBar();
        $this->thenICanSeeTheFirstNameOfFoo();
        $this->andTheLastNameOfFoo();
        $this->thenICanNotGoIntoEditMode();';
        $this->assertContains($string, $results_content);
    }

    /**
     * @expectedException \GD\Exceptions\TestDoesNotFileExists
     */
    public function testExceptionFileNotThere()
    {

        $path = 'tests/features/test_append_not_there.feature';

        $this->gd->setContext('browser')->setPathToFeature($path)
            ->appendFeatures();
    }
}
