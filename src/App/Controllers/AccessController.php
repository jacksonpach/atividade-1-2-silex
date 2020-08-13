<?php

namespace App\Controllers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


use App\Service\AccessService;
use App\Service\Api;

class AccessController extends BaseController implements ControllerProviderInterface
{

   /**
    * @var AccessService
    */
   public $service;

   /**
    * AccessController constructor.
    * @param AccessService $baseService
    */
   public function __construct(AccessService $baseService)
   {
      $this->service = $baseService;
   }

   /**
    * @param Application $app
    * @return mixed|\Silex\ControllerCollection
    */
   public function connect(Application $app)
   {
      $this->controllers = $app['controllers_factory'];

      $this->get('/', 'index');
      $this->get('/add', 'add');
      
      //$this->get('/logoff', 'logoff');

      return $this->controllers;
   }

   /**
    * @return array
    */
   public function index()
   {
      $api = new Api;
      $data = $api->getUsers();

      return $this->render('access/index.twig', $data);
   }

   /**
    * @return bool
    */
   public function add()
   {
      $api = new Api;
      $data = $api->getUsers();
      $data = $data['data'];      
      
      foreach ($data as $value)
      {
         unset($value['id']);
         $this->service->createUser($value);
      }

      $this->redirect('/');
   }

   /**
    * @todo add code for error operations.
    *
    * @return JsonResponse
    */
   public function login()
   {
      $pars = $this->getParameters();
      $isAccess = $this->service->confirmData($pars);

      $data = [
         'status' => $isAccess,
         'message' => $this->service->getMessage()
      ];

      return new JsonResponse($data);
   }

   /**
    * @return void
    */
   public function logoff()
   {
      $this->removeSessionVars();
      $this->redirect('/');
   }
}
