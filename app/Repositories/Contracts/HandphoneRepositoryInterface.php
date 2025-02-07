<?php

namespace App\Repositories\Contracts;

interface HandphoneRepositoryInterface
{
    public function getPopularHandphones($limit);

    public function searchByName(string $keyword);

    public function getAllNewHandphones();

    public function find($id);

    public function getPrice($ticketId);
}