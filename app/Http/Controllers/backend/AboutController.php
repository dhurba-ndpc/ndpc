<?php

namespace App\Http\Controllers\backend;

use App\Http\Requests\AboutRequest;

class AboutController extends BasePostController
{
    protected string $postType;
    protected string $viewPath;
    protected string $routePrefix;
    protected ?string $requestClass = AboutRequest::class;

    public function __construct()
    {
        $this->postType    = 'about';
        $this->viewPath    = 'backend.about';
        $this->routePrefix = 'about';
    }
}
