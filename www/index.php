<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

$app = new Silex\Application();
$app['debug'] = true;

////////////////////////////////////////////////////////////////////////////////
// Register service providers

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(),
    include __DIR__.'/../config.php');

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../views',
]);

$app->register(new Silex\Provider\FormServiceProvider());

$app->register(new Silex\Provider\CsrfServiceProvider());

$app->register(new Silex\Provider\LocaleServiceProvider());

$app->register(new Silex\Provider\TranslationServiceProvider());

////////////////////////////////////////////////////////////////////////////////

$app['login_form'] = $app->factory(function($app) {
    return $app['form.factory']->createBuilder(FormType::class)
        ->add('email', TextType::class)
        ->add('password', PasswordType::class)
        ->getForm();
});

////////////////////////////////////////////////////////////////////////////////
// Index page

$app->get('/', function () use ($app) {
    $count = $app['session']->get('count', 0);
    $count++;
    $app['session']->set('count', $count);
    return $app['twig']->render('index.html.twig');
})->bind('index');

////////////////////////////////////////////////////////////////////////////////
// Admin login page

$app->match('/admin/login', function (Request $request) use ($app) {
    if ($app['session']->has('logged_user')) {
        return $app['twig']->render('already_logged.html.twig');
    }
    $form = $app['login_form'];
    $form->handleRequest($request);
    if ($form->isValid()) {
        $data = $form->getData();
        $app['session']->set('logged_user', ['id'=>123, 'table'=>'users']);
        $app['session']->getFlashBag()
            ->add('success', 'You are successfully logged in.');
        return $app->redirect($app['url_generator']->generate('index'));
    }
    return $app['twig']->render('login/admin.html.twig', [
        'form' => $form->createView(),
    ]);
})->bind('admin_login');

////////////////////////////////////////////////////////////////////////////////
// Logout page

$app->get('/logout', function () use ($app) {
    if ($app['session']->has('logged_user')) {
        $app['session']->remove('logged_user');
        $app['session']->getFlashBag()
            ->add('info', 'You are successfully logged out.');
    }
    return $app->redirect($app['url_generator']->generate('index'));
})->bind('logout');

////////////////////////////////////////////////////////////////////////////////

$app->run();
