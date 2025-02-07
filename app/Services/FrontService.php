<?php

namespace App\Services;

use App\Models\Handphone;
use App\Repositories\Contracts\HandphoneRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class FrontService
{
    protected $categoryRepository;
    protected $handphoneRepository;

    public function __construct(HandphoneRepositoryInterface $handphoneRepository, CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->handphoneRepository = $handphoneRepository;
    }

    public function searchHandphone(string $keyword)
    {
        return $this->handphoneRepository->searchByName($keyword);
    }

    public function getFrontPageData()
    {
        $categories = $this->categoryRepository->getAllCategories();
        $popularHandphones = $this->handphoneRepository->getPopularHandphones(4);
        $newHandphones = $this->handphoneRepository->getAllNewHandphones();
        return compact('categories', 'popularHandphones', 'newHandphones');
    }
}
