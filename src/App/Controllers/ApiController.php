<?php

namespace App\Controllers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Service\ApiService;

use function GuzzleHttp\json_decode;

class ApiController extends BaseController implements ControllerProviderInterface
{

   /**
    * @var ApiService
    */
   protected $service;

   /**
    * ApiController constructor.
    * @param ApiService $enterService
    */
   public function __construct(ApiService $enterService)
   {
      $this->service = $enterService;
   }

   /**
    * @param Application $app
    * @return mixed
    */
   public function connect(Application $app)
   {
      $this->controllers = $app['controllers_factory'];

      $this->get('/', 'index');
      $this->get('/user', 'getUserAll');
      $this->get('/user/{id}', 'getUser');
      $this->post('/user', 'postUser');
      $this->put('/user/{id}', 'putUser');

      return $this->controllers;
   }

   public function index()
   {
      return new JsonResponse(['api' => 'index',]);
   }

   public function getUser()
   {
      $id = $this->getRequest()->get('id');
      
      $data = $this->service->getId($id); 
   
      return new JsonResponse($data);
   }
   
   public function getUserAll()
   {
      $data = $this->service->getAll();
      
      return new JsonResponse($data);
   }

   public function postUser()
   {
      $item = json_decode($this->getRequest()->getContent());
      $item = (array)$item;

      $status = $this->service->createUser($item);

      $data = [
         'status' => $status
      ];

      return new JsonResponse($data);
   }

   public function putUser()
   {
      $id = json_decode($this->getRequest()->get('id'));
      $id = ['id' => $id];

      $item = json_decode($this->getRequest()->getContent());
      $item = (array)$item;

      $status = $this->service->update($item, $id);

      $data = [
         'status' => $status
      ];

      return new JsonResponse($data);
   }
}
