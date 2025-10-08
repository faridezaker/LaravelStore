<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     tags={"Categories"},
     *     summary="Get list of categories",
     *     description="Retrieve paginated list of categories",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Categories retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Electronics"),
     *                     @OA\Property(property="parent_id", type="integer", example=null),
     *                     @OA\Property(property="description", type="string", example="Category description"),
     *                     @OA\Property(property="children", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="Smartphones"),
     *                             @OA\Property(property="description", type="string", example="Child category description")
     *                         )
     *                     ),
     *                     @OA\Property(property="parent", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Electronics")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $categories = $this->categoryService->all(5);

        return response()->success(
            CategoryResource::collection($categories),
            'Categories retrieved successfully',
            200,
            CategoryResource::collection($categories)->response()->getData()->meta,
            CategoryResource::collection($categories)->response()->getData()->links
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/categories",
     *     tags={"Categories"},
     *     summary="Create a new category",
     *     description="Creates a category",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Smartphones"),
     *             @OA\Property(property="parent_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="Child category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Smartphones"),
     *                 @OA\Property(property="parent_id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="Child category")
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(CategoryStoreRequest $request)
    {
        $category = $this->categoryService->create($request->validated());
        return response()->success(new CategoryResource($category), 'Category created successfully', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}",
     *     tags={"Categories"},
     *     summary="Get a category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Smartphones"),
     *                 @OA\Property(property="parent_id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="Child category")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Brand not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="data", type="object", nullable=true, example=null),
     *             @OA\Property(property="message", type="string", example="Brand not found")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $category = $this->categoryService->find($id);
            return response()->success(new CategoryResource($category), 'Category retrieved successfully', 200);
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error(null, "Category not found", 404);
            }
            return response()->error(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/categories/{id}",
     *     tags={"Categories"},
     *     summary="Update a category",
     *     description="Updates category fields by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Smartphones Updated"),
     *             @OA\Property(property="parent_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Smartphones Updated"),
     *                 @OA\Property(property="parent_id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="Updated description")
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category = $this->categoryService->update($category, $request->validated());
        return response()->success(new CategoryResource($category), 'Category updated successfully', 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/categories/{id}",
     *     tags={"Categories"},
     *     summary="Delete a category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully"),
     *             @OA\Property(property="data", type="null", example=null),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $this->categoryService->delete($id);
        return response()->success(null, 'Category deleted successfully', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}/children",
     *     tags={"Categories"},
     *     summary="Get category children",
     *     description="Retrieve all children of a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category children retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category children retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", example="Smartphones"),
     *                     @OA\Property(property="parent_id", type="integer", example=0),
     *                     @OA\Property(property="description", type="string", example="Child category description"),
     *                     @OA\Property(property="children", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="name", type="string", example="iPhone"),
     *                             @OA\Property(property="parent_id", type="integer", example=1),
     *                             @OA\Property(property="description", type="string", example="Sub-child category")
     *                         )
     *                     )
     *                 )
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function getChildren(Category $category)
    {
        return response()->success(new CategoryResource($category->load('children')), 'Category children retrieved successfully', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}/parent",
     *     tags={"Categories"},
     *     summary="Get category parent",
     *     description="Retrieve the parent of a category including nested parent data",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category parent retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category parent retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Smartphones"),
     *                 @OA\Property(property="parent_id", type="integer", example=2),
     *                 @OA\Property(property="description", type="string", example="Child category"),
     *                 @OA\Property(
     *                     property="parent",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="Electronics"),
     *                     @OA\Property(property="parent_id", type="integer", example=0),
     *                     @OA\Property(property="description", type="string", example="Main parent category")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function getParent(Category $category)
    {
        return response()->success(new CategoryResource($category->load('parent')), 'Category parent retrieved successfully', 200);
    }

}
