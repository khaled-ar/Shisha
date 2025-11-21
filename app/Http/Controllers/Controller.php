<?php

namespace App\Http\Controllers;

use App\Traits\{
    Files,
    Responses
};

abstract class Controller
{
    use Responses, Files;
}
