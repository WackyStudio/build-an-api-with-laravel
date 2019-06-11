<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\JSONAPIRelationshipRequest;
use App\Services\JSONAPIService;
use Illuminate\Http\Request;

class CommentsBooksRelationshipsController extends Controller
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
        return $this->service->fetchRelationship($comment, 'books');
    }

    public function update(JSONAPIRelationshipRequest $request, Comment $comment)
    {
        return $this->service->updateToOneRelationship($comment, 'books', $request->input('data.id'));
    }
}
