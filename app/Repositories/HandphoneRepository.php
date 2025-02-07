<?php 

namespace App\Repositories;

use App\Models\Handphone;
use App\Repositories\Contracts\HandphoneRepositoryInterface;

class HandphoneRepository implements HandphoneRepositoryInterface
{
    public function getPopularHandphones($limit = 5)
    {
        return Handphone::where('is_popular', true)->take($limit)->get();
    }

    public function searchByName(string $keyword)
    {
        return Handphone::where('name', 'LIKE', '%' . $keyword . '%')->get();
    }

    public function getAllNewHandphones()
    {
        return Handphone::latest()->get();
    }

    public function find($id) // 1
    {
        return Handphone::find($id);
    }

    public function getPrice($handphoneId)
    {
        $handphone = $this->find($handphoneId);
        return $handphone ? $handphone->price : 0;
    }
}