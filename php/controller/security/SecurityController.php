<?php
declare(strict_types=1);

namespace php\controller\security;

class SecurityController
{
   
    public function isConnect():bool
    {
        if(isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            return true;
        }
        else {
            return false;
        }
    }
    public function isAdmin():bool {
        if(isset($_SESSION['admin']) && !empty($_SESSION['admin'])){
            return true;
        }
        else {
            return false;
        }
    }
}