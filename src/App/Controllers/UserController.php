<?php

namespace App\Controllers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Service\UserService;


class UserController extends BaseController implements ControllerProviderInterface
{
   /**
    * @var UserController
    */
   public $service;

   /**
    * UserController constructor
    * @param UserController $baseService
    */
    public function __construct(UserService $baseService)
    {
       $this->service = $baseService;
    }

   /**
    * @param Application $app
    * @return mixed
    */
   public function connect(Application $app)
   {
      $this->controllers = $app['controllers_factory'];

      $this->get('/', 'index');
      
      $this->get('/create', 'form');
      $this->post('/create', 'create');
      
      $this->get('/edit/{id}', 'editForm');
      $this->post('/edit/{id}', 'userUpdate');

      return $this->controllers;
   }

   public function index()
   {
      $data = $this->service->getAll();

      return $this->render('access/userIndex.twig',['data' => $data]);
   }

   public function form()
   {
      return $this->render('access/userCreate.twig');  
   }

   /**
    * @return bool
    */
   public function create()
   {      
      $data = $this->getRequest()->request;
      $data = (array)$data;
      $pars = array_shift($data);

      $status = $this->service->createUser($pars);
      $data = [
         'status' => $status
      ];
      $this->redirect('/user');
   }

   /**
    * @return array
    */
   public function editForm()
   {  
      $id = $this->getRequest()->get('id');
      
      $data = $this->service->getId($id);

      return $this->render('access/userEdit.twig', ['data' => $data]); 
   }

   /**
    * @return bool
    */
   public function userUpdate()
   {
      $id = $this->getRequest()->get('id');
      $id = ['id' => $id];

      $pars = $this->getRequest()->request;
      $pars = (array)$pars;
      $pars = array_shift($pars);

      $this->service->update($pars, $id);

      $this->redirect('/user');
   }


   /**
    * @return JsonResponse
    */
   public function registerUser()
   {
      $pars = $this->getRequest();

      $status = $this->service->register($pars);
      $data = [
         'status' => $status,
         'content' => $this->service->getData()
      ];
      return new JsonResponse($data);
   }
}
