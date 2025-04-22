<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFundRequest;
use App\Http\Requests\UpdateFundRequest;
use App\Services\FundService;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Fund Management API",
 *     version="1.0.0",
 *     description="API for managing investment funds",
 *     @OA\Contact(
 *         email="ricardof.campeol@gmail.com"
 *     )
 * )
 */
class FundController extends Controller
{
    public function __construct(protected FundService $service) {}

    /**
     * @OA\Get(
     *     path="/api/funds",
     *     tags={"Funds"},
     *     summary="List all funds",
     *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="manager", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="start_year", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of funds")
     * )
     */
    public function index(Request $request)
    {
        return response()->json($this->service->list($request));
    }

    /**
     * @OA\Post(
     *     path="/api/funds",
     *     tags={"Funds"},
     *     summary="Create a new fund",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","start_year","fund_manager_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="start_year", type="integer"),
     *             @OA\Property(property="fund_manager_id", type="string", format="uuid"),
     *             @OA\Property(property="aliases", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="companies", type="array", @OA\Items(type="string", format="uuid"))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Fund successfully created")
     * )
     */
    public function store(StoreFundRequest $request)
    {
        $fund = $this->service->create($request->validated());
        return response()->json($fund, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/funds/{id}",
     *     tags={"Funds"},
     *     summary="Get fund by ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Fund details")
     * )
     */
    public function show(string $id)
    {
        $fund = $this->service->find($id);
        return response()->json($fund);
    }

    /**
     * @OA\Put(
     *     path="/api/funds/{id}",
     *     tags={"Funds"},
     *     summary="Update a fund",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="start_year", type="integer"),
     *             @OA\Property(property="fund_manager_id", type="string"),
     *             @OA\Property(property="aliases", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="companies", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Fund updated")
     * )
     */
    public function update(UpdateFundRequest $request, string $id)
    {
        $fund = $this->service->update($id, $request->validated());
        return response()->json($fund);
    }

    /**
     * @OA\Delete(
     *     path="/api/funds/{id}",
     *     tags={"Funds"},
     *     summary="Delete a fund",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=204, description="Fund deleted")
     * )
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);
        return response()->noContent();
    }

    /**
     * @OA\Get(
     *     path="/api/funds/duplicates",
     *     tags={"Funds"},
     *     summary="Find potential duplicates",
     *     @OA\Parameter(name="name", in="query", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="fund_manager_id", in="query", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of potential duplicates")
     * )
     */
    public function showDuplicates(Request $request)
    {
        $duplicates = $this->service->findPotentialDuplicates($request->name, $request->fund_manager_id);
        return response()->json($duplicates);
    }
}
