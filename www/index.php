<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Silex\Application;
use Milena\LoginService;
use Milena\UserRoles;

$app = new Application();
$app['debug'] = true;

/**
 * Register services and service providers
 */

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\CsrfServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(),
    include __DIR__ . '/../config.php');
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views',
]);

$app['login'] = function ($app) {
    return new LoginService($app['db']);
};

/**
 * Index page
 */

$app->get('/', function (Application $app) {
    $nav = $app['url_generator'];

    return $app['twig']->render('index.html.twig', [
        'nav' => [
            $nav->generate('admin_login'),
            $nav->generate('sales_login'),
            $nav->generate('site_login', ['hash' => 'RVM1G5621DGYHI']),
            $nav->generate('site_login', ['hash' => 'AVF1G5621DG34D']),
            $nav->generate('logout'),
        ],
    ]);
})->bind('index');

/**
 * Login pages
 */

$loginPage = function (Request $request, Application $app) {
    if ($app['session']->has('logged_user')) {
        return $app['twig']->render('stub.html.twig');
    }

    $form = $app['form.factory']->createBuilder(FormType::class)
        ->add('email', TextType::class)
        ->add('password', PasswordType::class)
        ->getForm();

    $form->handleRequest($request);
    if ($form->isValid()) {
        $data = $form->getData() + $request->attributes->get('constraints');

        if ($login = $app['login']->login($data)) {
            $app['session']->set('logged_user', $login);
            $app['session']->getFlashBag()
                ->add('success', 'You are successfully logged in.');
            return $app->redirect($app['url_generator']->generate('index'));
        }

        $app['session']->getFlashBag()->add('danger', 'Login incorrect.');
    }

    return $app['twig']->render('login.html.twig', [
        'form' => $form->createView(),
    ]);
};

$app->match('/admin/login', $loginPage)
    ->bind('admin_login')
    ->value('constraints', [
        'roles' => [UserRoles::GSM_ADMIN],
    ]);

$app->match('/sales/login', $loginPage)
    ->bind('sales_login')
    ->value('constraints', [
        'roles' => [
            UserRoles::GSM_ADMIN,
            UserRoles::SALES_ADMIN,
            UserRoles::SALES_USER,
        ],
    ]);

$app->match('/site/login/pid/{hash}', $loginPage)
    ->bind('site_login')
    ->before(function (Request $request) {
        $request->attributes->set('constraints', [
            'clientAdminHash' => $request->attributes->get('hash'),
        ]);
    });

/**
 * Logout page
 */

$app->get('/logout', function (Application $app) {
    if ($app['session']->has('logged_user')) {
        $app['session']->remove('logged_user');
        $app['session']->getFlashBag()
            ->add('info', 'You are successfully logged out.');
    }

    return $app->redirect($app['url_generator']->generate('index'));
})->bind('logout');

$app->run();
