<?php

namespace App\Http\Controllers;

use App\Services\JSONAPIService;
use App\User;
use Illuminate\Http\Request;

class UsersCommentsRelatedController extends Controller
{
    /**
     * @var JSONAPIService
     */
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    public function index(User $user)
    {
        return $this->service->fetchRelated($user, 'comments');
    }
}
