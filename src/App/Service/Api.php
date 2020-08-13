<?php

namespace App\Service;

class api
{
   public function getUsers()
   {
      $url = 'https://reqres.in/api/users';

      $response = json_decode(file_get_contents($url), true);

      return $response;
   }
}

