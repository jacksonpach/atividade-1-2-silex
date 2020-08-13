<?php

namespace App\Service;

/**
 * Class AccessService
 * @package App\Service
 */
class ApiService extends BaseService
{
   /**
    * @param int $id
    * @return array 
    */
   public function getId($id)
   {
      return $this->getItem($id, 'user');
   }

   /**
    * @param int $id
    * @return array 
    */
   public function getAll()
   {
      return $this->getItems('user');
   }

   public function createUser(array $pars)
   {
      return $this->insert('user', $pars);
   }

   /**
    * @param array $data
    * @return bool
    */
    public function update(array $data, $id)
    {
       return $this->updateData('user', $data, $id);
    }
}
