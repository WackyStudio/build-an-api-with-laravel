<?php

namespace App\Http\Controllers;

use App\Author;
use App\Http\Requests\JSONAPIRelationshipRequest;
use App\Services\JSONAPIService;
use Illuminate\Http\Request;

class AuthorsBooksRelationshipsController extends Controller
{

    /**
     * @var JSONAPIService
     */
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    public function index(Author $author)
    {
        return $this->service->fetchRelationship($author, 'books');
    }

    public function update(JSONAPIRelationshipRequest $request, Author $author)
    {
        return $this->service->updateManyToManyRelationships($author, 'books', $request->input('data.*.id'));
    }
}
