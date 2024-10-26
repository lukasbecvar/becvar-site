<?php

namespace App\Tests\Repository;

use App\Entity\Visitor;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class VisitorRepositoryTest
 *
 * Test for doctrine entity repository class
 *
 * @package App\Tests\Repository
 */
class VisitorRepositoryTest extends KernelTestCase
{
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        // boot the Symfony kernel
        self::bootKernel();

        // get the EntityManager from the service container
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * Test get all IDs
     *
     * @return void
     */
    public function testGetAllIds(): void
    {
        /** @var \App\Repository\VisitorRepository $visitorRepository */
        $visitorRepository = $this->entityManager->getRepository(\App\Entity\Visitor::class);

        // get visitors
        $visitors = $visitorRepository->getAllIds();

        // assert output
        $this->assertIsArray($visitors, "The result should be an array of IDs.");
        foreach ($visitors as $id) {
            $this->assertIsInt($id, "Each ID should be an integer.");
        }
    }

    /**
     * Test find visitors by time filter
     *
     * @return void
     */
    public function testFindByTimeFilter(): void
    {
        /** @var \App\Repository\VisitorRepository $visitorRepository */
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        // get visitors
        $visitors = $visitorRepository->findByTimeFilter('H');

        // assert output
        $this->assertIsArray($visitors, "The result should be an array of visitors.");
        foreach ($visitors as $visitor) {
            $this->assertInstanceOf(\App\Entity\Visitor::class, $visitor, "Each item should be an instance of Visitor.");
        }
    }

    /**
     * Test get visitors count by period
     *
     * @return void
     */
    public function testGetVisitorsCountByPeriod(): void
    {
        /** @var \App\Repository\VisitorRepository $visitorRepository */
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        // get visitors
        $visitors = $visitorRepository->getVisitorsCountByPeriod('last_week');

        // assert output
        $this->assertIsArray($visitors, "The result should be an associative array of visitor counts.");
        foreach ($visitors as $date => $count) {
            $this->assertIsString($date, "Each key should be a date string.");
            $this->assertIsInt($count, "Each value should be an integer representing visitor count.");
        }
    }

    /**
     * Test get visitors by country
     *
     * @return void
     */
    public function testGetVisitorsByCountry(): void
    {
        /** @var \App\Repository\VisitorRepository $visitorRepository */
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        // get visitors
        $visitors = $visitorRepository->getVisitorsByCountry();

        // assert output
        $this->assertIsArray($visitors, "The result should be an associative array of country visitor counts.");
        foreach ($visitors as $country => $count) {
            $this->assertIsString($country, "Each key should be a country string.");
            $this->assertIsInt($count, "Each value should be an integer representing visitor count.");
        }
    }

    /**
     * Test get visitors by city
     *
     * @return void
     */
    public function testGetVisitorsByCity(): void
    {
        /** @var \App\Repository\VisitorRepository $visitorRepository */
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        // get visitors
        $visitors = $visitorRepository->getVisitorsByCity();

        // assert output
        $this->assertIsArray($visitors, "The result should be an associative array of city visitor counts.");
        foreach ($visitors as $city => $count) {
            $this->assertIsString($city, "Each key should be a city string.");
            $this->assertIsInt($count, "Each value should be an integer representing visitor count.");
        }
    }

    /**
     * Test get visitors used browsers
     *
     * @return void
     */
    public function testGetVisitorsUsedBrowsers(): void
    {
        /** @var \App\Repository\VisitorRepository $visitorRepository */
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        // get visitors
        $visitors = $visitorRepository->getVisitorsUsedBrowsers();

        // assert output
        $this->assertIsArray($visitors, "The result should be an associative array of browser visitor counts.");
        foreach ($visitors as $browser => $count) {
            $this->assertIsString($browser, "Each key should be a browser string.");
            $this->assertIsInt($count, "Each value should be an integer representing visitor count.");
        }
    }
}
