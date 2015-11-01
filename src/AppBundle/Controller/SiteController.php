<?php
/**
 * Site controller
 * It is through this object that users log into the application.
 * 
 * PHP version 5.6
 * 
 * @category WPM
 * @package  User
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  GIT: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Site controller
 * It is through this object that users log into the application.
 * @category WPM
 * @package User
 * @author Matthew Turner <tmatt95@gmail.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL3
 * @version Release: <1.0.0>
 * @link https://github.com/tmatt95/WPM/
 */
class SiteController extends Controller
{
    /**
     * Login page
     * The page that users will see when they log into the application. and 
     * controls what is displayed if they get their user name or password 
     * wrong.
     * @return HTML the login page
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login screen
        return $this->render(
            'login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error' => $error,
            )
        );
    }

    /**
     * Login Check Action
     * This controller will not be executed, as the route is handled by the 
     * Security system.
     * @return Nothing This function is not executed
     */
    public function loginCheckAction()
    {
    }
}
