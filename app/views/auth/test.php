<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//@extends('layout')

//section('content')
    foreach($users as $user)
        { echo $user->Name; }
   

?>