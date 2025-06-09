<?php

namespace App\Http\Controllers;

use App\Infra\Repositories\DestinationRepository;
use Domain\Destination\UseCases\Index\Index;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $useCase = new Index(new DestinationRepository());
        $destinations = $useCase->execute();

        return response()->json($destinations, 200);
    }
}
