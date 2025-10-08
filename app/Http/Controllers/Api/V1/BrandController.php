<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\BrandStoreRequest;
use App\Http\Requests\Brand\BrandUpdateRequest;
use App\Http\Resources\Brand\BrandResource;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    private $brandService;
    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/brands",
     *     tags={"Brands"},
     *     summary="Get list of brands",
     *     description="Retrieve paginated list of brands",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of brands retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Brands retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", example="Samsung"),
     *                     @OA\Property(property="display_name", type="string", example="سامسونگ")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="total", type="integer", example=25)
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string", example="http://localhost/api/v1/brands?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://localhost/api/v1/brands?page=5"),
     *                 @OA\Property(property="prev", type="string", example=null),
     *                 @OA\Property(property="next", type="string", example="http://localhost/api/v1/brands?page=2")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $brands = $this->brandService->all(5);

        return response()->success(
            BrandResource::collection($brands),
            'Brands retrieved successfully',
            200,
            BrandResource::collection($brands)->response()->getData()->meta,
            BrandResource::collection($brands)->response()->getData()->links
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/brands",
     *     tags={"Brands"},
     *     summary="Create a new brand",
     *     description="Creates a brand with unique display name",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","display_name"},
     *             @OA\Property(property="name", type="string", example="samsung"),
     *             @OA\Property(property="display_name", type="string", example="new Brand")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Brand created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="name", type="string", example="samsung"),
     *                 @OA\Property(property="display_name", type="string", example="سامسونگ")
     *             ),
     *             @OA\Property(property="message", type="string", example="Brand created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The display name has already been taken."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="display_name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The display name has already been taken.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(BrandStoreRequest $request)
    {
        $brand = $this->brandService->create($request->validated());

        return response()->success(new BrandResource($brand), 'Brand created successfully', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/brands/{id}",
     *     tags={"Brands"},
     *     summary="Get a brand by ID",
     *     description="Returns a single brand resource",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Brand ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Brand retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="apple"),
     *                 @OA\Property(property="display_name", type="string", example="Apple Inc")
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Brand not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="data", type="object", example="null"),
     *             @OA\Property(property="message", type="string", example="Brand not found")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $brand = $this->brandService->find($id);
            return response()->success(new BrandResource($brand), 'Brand retrieved successfully', 200);
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error(null, "Brand not found", 404);
            }
            return response()->error(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/brands/{id}",
     *     tags={"Brands"},
     *     summary="Update a brand",
     *     description="Updates brand fields by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Brand ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="samsung"),
     *             @OA\Property(property="display_name", type="string", example="Samsung Updated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Brand updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="samsung"),
     *                 @OA\Property(property="display_name", type="string", example="Samsung Updated"),
     *             ),
     *         )
     *     ),
     *       @OA\Response(
     *           response=404,
     *           description="Brand not found",
     *           @OA\JsonContent(
     *           @OA\Property(property="success", type="boolean", example=false),
     *           @OA\Property(property="data", type="object", example="null"),
     *           @OA\Property(property="message", type="string", example="Brand not found")
     *          )
     *      ),
     *      @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(BrandUpdateRequest $request, Brand $brand)
    {
        $brand = $this->brandService->update($brand, $request->validated());

        return response()->success(new BrandResource($brand), 'Brand updated successfully', 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/brands/{id}",
     *     tags={"Brands"},
     *     summary="Delete a brand",
     *     description="Deletes a brand by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Brand ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Brand deleted successfully"),
     *             @OA\Property(property="data", type="null", example=null),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Brand not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $this->brandService->delete($id);
        return response()->success(null, 'Brand deleted successfully', 200);
    }
}
