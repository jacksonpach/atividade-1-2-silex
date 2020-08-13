<?php

namespace App\Service;

/**
 * Class UserService
 * @package App\Service
 */
class UserService extends BaseService
{

   /**
    * @param array $data
    * @return bool
    */
   public function register(array $data)
   {
      return true;
   }

   /**
    * @param array $data
    * @return bool
    */
   public function access(array $data)
   {
      return true;
   }

   /**
    * @param array $data
    * @return bool
    */
   public function auth(array $data)
   {
      return true;
   }

   /**
    * @param array $data
    * @return bool
    */
   public function update(array $data, $id)
   {
      return $this->updateData('user', $data, $id);
   }

   public function createUser(array $pars)
   {
      return $this->insert('user', $pars);
   }

   /**
    * @param int $id
    * @return array 
    */
   public function getAll()
   {
      return $this->getItems('user');
   }

   /**
    * @param int $id
    * @return array 
    */
   public function getId($id)
   {
      return $this->getItem($id, 'user');
   }

   


}
