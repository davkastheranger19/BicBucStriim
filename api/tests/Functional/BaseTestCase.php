<?php

namespace Tests\Functional;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use RedBeanPHP\R;

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected $withMiddleware = true;

    protected $environment;
    protected $token;

    const CALIBRE = __DIR__ . '/../fixtures/lib2';
    const DB2 = __DIR__ . '/../fixtures/data2.db';

    const DATA = __DIR__ . '/../data';
    const DATADB = __DIR__ . '/../data/data.db';


    function setUp() {
        if (file_exists(self::DATA))
            system("rm -rf ".self::DATA);
        //print_r(getcwd().'\n');
        //print_r(self::DATA);
        mkdir(self::DATA);
        chmod(self::DATA,0777);
        copy(self::DB2, self::DATADB);
    }

    function tearDown() {
        R::nuke();
        R::close();
        system("rm -rf ".self::DATA);
    }

    /**
     * Process the application given a request method and URI as an admin user with HTTP Basic Auth
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     * @return \Slim\Http\Response
     */
    public function runAppWithAdmin($requestMethod, $requestUri, $requestData = null)
    {
        // Create a mock environment for testing with
        $this->environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => $requestUri,
                'CONTENT_TYPE' => 'application/json;charset=utf-8',
                'PHP_AUTH_USER'=> 'admin',
                'PHP_AUTH_PW' => 'admin',
            ]
        );
        return $this->runAppInt($requestMethod, $requestUri, $requestData);
    }

    /**
     * Process the application given a request method and URI with a JWT Auth
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     * @return \Slim\Http\Response
     */
    public function runApp($requestMethod, $requestUri, $requestData = null)
    {
        // Create a mock environment for testing with
        $this->environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => $requestUri,
                'CONTENT_TYPE' => 'application/json;charset=utf-8',
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token
            ]
        );
        return $this->runAppInt($requestMethod, $requestUri, $requestData);
    }

    /**
     * Process the application given a request method and URI
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     * @return \Slim\Http\Response
     */
    public function runAppInt($requestMethod, $requestUri, $requestData = null)
    {
        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($this->environment);

        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }

        // Set up a response object
        $response = new Response();

        // Use the application settings
        $settings = require __DIR__ . '/../../src/settings.php';
        $settings['settings']['bbs']['dataDb'] = self::DATADB;

        // Instantiate the application
        $app = new App($settings);

        // Set up dependencies
        require __DIR__ . '/../../src/dependencies.php';

        // Register middleware
        if ($this->withMiddleware) {
            require __DIR__ . '/../../src/middleware.php';
        }
        // Route helpers
        require_once __DIR__ . '/../../src/helpers.php';

        // Register routes
        require __DIR__ . '/../../src/routes/auth.php';
        require __DIR__ . '/../../src/routes/admin.php';
        require __DIR__ . '/../../src/routes/opds.php';
        require __DIR__ . '/../../src/routes/titles.php';
        // Process the application
        $response = $app->process($request, $response);

        // Return the response
        return $response;
    }
}
