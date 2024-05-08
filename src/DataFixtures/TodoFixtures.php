<?php

namespace App\DataFixtures;

use App\Entity\Todo;
use App\Util\SecurityUtil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class TodoFixtures
 *
 * TodoFixtures loads sample Todo data into the database.
 *
 * @package App\DataFixtures
 */
class TodoFixtures extends Fixture
{
    private SecurityUtil $securityUtil;

    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Sample todo texts.
     *
     * @var array<string>
     */
    private $todos = [
        'Fix bug in login page',
        'Implement user profile page',
        'Update CSS styles for homepage',
        'Add pagination to blog posts',
        'Create admin dashboard',
        'Write unit tests for authentication',
        'Optimize database queries',
        'Refactor legacy codebase',
        'Integrate third-party API',
        'Design email templates',
        'Implement two-factor authentication',
        'Update privacy policy',
        'Add file upload feature',
        'Improve error handling',
        'Implement search functionality',
        'Upgrade to latest framework version',
        'Fix broken links in footer',
        'Create mobile-responsive layout',
        'Optimize images for web',
        'Add multi-language support',
        'Implement password reset functionality',
        'Update terms of service',
        'Create user onboarding process',
        'Optimize website performance',
        'Fix cross-browser compatibility issues',
        'Implement role-based access control',
        'Add social media sharing buttons',
        'Write documentation for developers',
        'Implement automated testing',
        'Upgrade server infrastructure',
        'Add SSL certificate',
        'Integrate payment gateway',
        'Create sitemap.xml file',
        'Set up cron jobs for scheduled tasks',
        'Improve user interface design',
        'Optimize mobile app for speed',
        'Implement feedback form',
        'Fix broken image links',
        'Create product demo video',
        'Implement live chat support',
        'Add custom error pages',
        'Improve website security',
        'Optimize database schema',
        'Implement server-side caching',
        'Write blog post about latest features',
        'Set up continuous integration pipeline',
        'Implement cookie consent banner',
        'Add analytics tracking',
        'Create automated backup system',
        'Optimize CSS and JavaScript files'
    ];

    /**
     * Load todo fixtures into the database.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        // create 50 non-completed todos
        for ($i = 0; $i < 50; $i++) {
            $todo = new Todo();
            $todo->setText($this->securityUtil->encryptAes($this->todos[array_rand($this->todos)]));
            $todo->setStatus('non-completed');
            $todo->setAddedTime(date('Y-m-d H:i:s'));
            $todo->setCompletedTime('non-completed');
            $todo->setAddedBy('test');
            $todo->setClosedBy('non-closed');

            $manager->persist($todo);
        }

        // create 50 completed todos
        for ($i = 0; $i < 50; $i++) {
            $todo = new Todo();
            $todo->setText($this->securityUtil->encryptAes($this->todos[array_rand($this->todos)]));
            $todo->setStatus('completed');
            $todo->setAddedTime(date('Y-m-d H:i:s', strtotime("-$i days")));
            $todo->setCompletedTime(date('Y-m-d H:i:s'));
            $todo->setAddedBy('test');
            $todo->setClosedBy('test');

            $manager->persist($todo);
        }

        $manager->flush();
    }
}
