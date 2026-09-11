<?php

namespace App\Http\Controllers\backend;

use App\Http\Requests\HeroBannerRequest;


class HeroBannerController extends BasePostController
{
    protected string $postType;
    protected string $viewPath;
    protected string $routePrefix;
    protected ?string $requestClass = HeroBannerRequest::class;

    public function __construct()
    {
        $this->postType    = 'banner';
        $this->viewPath    = 'backend.hero_banner';
        $this->routePrefix = 'hero-banners';
    }
}
