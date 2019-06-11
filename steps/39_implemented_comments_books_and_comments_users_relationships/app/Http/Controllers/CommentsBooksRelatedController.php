<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Services\JSONAPIService;
use Illuminate\Http\Request;

class CommentsBooksRelatedController extends Controller
{

    /**
     * @var JSONAPIService
     */
    private $service;

    public function __construct(JSONAPIService $service)
    {

        $this->service = $service;
    }

    public function index(Comment $comment)
    {
        return $this->service->fetchRelated($comment, 'books');
    }
}
