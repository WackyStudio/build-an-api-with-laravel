<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\JSONAPIRelationshipRequest;
use App\Services\JSONAPIService;
use Illuminate\Http\Request;

class CommentsUsersRelationshipsController extends Controller
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
        return $this->service->fetchRelationship($comment, 'users');
    }

    public function update(JSONAPIRelationshipRequest $request, Comment $comment)
    {
        return $this->service->updateToOneRelationship($comment, 'users', $request->input('data.id'));
    }
}
