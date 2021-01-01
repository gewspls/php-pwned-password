<?php

require_once '../src/PwnedPassword.php';

$passwordToCheck = "password";

$p = new gewspls\PwnedPassword($passwordToCheck);

if($p->CheckPasswordExposure())
{
    echo "Password was found exposed: ".$p->GetExposureCount()." times.";
}