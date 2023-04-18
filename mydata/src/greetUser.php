<?php
namespace Drupal\mydata;

use Drupal\Core\Session\AccountProxyInterface;

class greetUser{
    protected $current_user;
    public function __construct(AccountProxyInterface $current_user)
    {
        // $this->greetings = "Good Morning ! In case I don't see ya, good afternoon, good evening and good night !";   
        $this->current_user = $current_user;
    } 

    public function greet(){
        return "Good Morning " . $this->current_user->getAccountName() . " ! In case I don't see ya, good afternoon, good evening and good night !";
    }
}
