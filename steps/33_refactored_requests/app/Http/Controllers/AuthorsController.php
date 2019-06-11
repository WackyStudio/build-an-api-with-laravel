<?php

namespace App\Http\Controllers;

use App\Author;
use App\Http\Requests\CreateAuthorRequest;
use App\Http\Requests\JSONAPIRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\JSONAPICollection;
use App\Http\Resources\JSONAPIResource;
use App\Services\JSONAPIService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AuthorsController extends Controller
{

    /**
     * @var JSONAPIService
     */
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->service->fetchResources(Author::class, 'authors');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JSONAPIRequest $request)
    {
        return $this->service->createResource(Author::class, $request->input('data.attributes'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function show(Author $author)
    {
        return $this->service->fetchResource($author);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(JSONAPIRequest $request, Author $author)
    {
        return $this->service->updateResource($author, $request->input('data.attributes'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author)
    {
        return $this->service->deleteResource($author);
    }
}
