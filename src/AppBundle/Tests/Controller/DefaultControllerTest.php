<?php

/**
 * Defsult Test Controller
 * When we have programmed in other controllers this can be deleted. Left in for
 * now to act as a template
 * 
 * PHP version 5.6
 * 
 * @category WPM
 * @package  Test
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  GIT: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Default Test Controller
 * When we have programmed in other controllers this can be deleted. Left in for
 * now to act as a template
 * 
 * PHP version 5.6
 * 
 * @category WPM
 * @package  Test
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  Release: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */
class DefaultControllerTest extends WebTestCase
{
    /**
     * Test Index
     * I think this would test the index action in the default controller if
     * there was one. Left in for now.
     * @return nothing
     */
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            'Welcome to Symfony',
            $crawler->filter('#container h1')->text()
        );
    }
}
