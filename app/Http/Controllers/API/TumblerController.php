<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Tumbler;
use OpenApi\Annotations as OA;

/**
 * Class TumblerController.
 * 
 * @author Yobel <yobel.422023001@civitas.ukrida.ac.id>
 */

class TumblerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tumbler",
     *     tags={"tumbler"},
     *     description="Display a listing of items",
     *     operationId="index",
     *     @OA\Response(
     *         response=200,
     *         description="successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="_page",
     *         in="query",
     *         description="current page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_limit",
     *         in="query",
     *         description="max item in a page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=10
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="_search",
     *         in="query",
     *         description="word to search",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_publisher",
     *         in="query",
     *         description="search by publisher like name",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_sort_by",
     *         in="query",
     *         description="word to search",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="latest"
     *         )
     *     ),
     * )
     */
    public function index(Request $request)
    {
        try {
            $data['filter']       = $request->all();
            $page                 = $data['filter']['_page']  = (@$data['filter']['_page'] ? intval($data['filter']['_page']) : 1);
            $limit                = $data['filter']['_limit'] = (@$data['filter']['_limit'] ? intval($data['filter']['_limit']) : 1000);
            $offset               = ($page?($page-1)*$limit:0);
            $data['products']     = Book::whereRaw('1 = 1');
            
            if($request->get('_search')){
                $data['products'] = $data['products']->whereRaw('(LOWER(title) LIKE "%'.strtolower($request->get('_search')).'%" OR LOWER(author) LIKE "%'.strtolower($request->get('_search')).'%")');
            }
            if($request->get('_publisher')){
                $data['products'] = $data['products']->whereRaw('LOWER(publisher) = "'.strtolower($request->get('_publisher')).'"');
            }
            if($request->get('_sort_by')){
            switch ($request->get('_sort_by')) {
                default:
                case 'latest_publication':
                $data['products'] = $data['products']->orderBy('publication_year','DESC');
                break;
                case 'latest_added':
                $data['products'] = $data['products']->orderBy('created_at','DESC');
                break;
                case 'title_asc':
                $data['products'] = $data['products']->orderBy('title','ASC');
                break;
                case 'title_desc':
                $data['products'] = $data['products']->orderBy('title','DESC');
                break;
                case 'price_asc':
                $data['products'] = $data['products']->orderBy('price','ASC');
                break;
                case 'price_desc':
                $data['products'] = $data['products']->orderBy('price','DESC');
                break;
            }
            }
            $data['products_count_total']   = $data['products']->count();
            $data['products']               = ($limit==0 && $offset==0)?$data['products']:$data['products']->limit($limit)->offset($offset);
            // $data['products_raw_sql']       = $data['products']->toSql();
            $data['products']               = $data['products']->get();
            $data['products_count_start']   = ($data['products_count_total'] == 0 ? 0 : (($page-1)*$limit)+1);
            $data['products_count_end']     = ($data['products_count_total'] == 0 ? 0 : (($page-1)*$limit)+sizeof($data['products']));
           return response()->json($data, 200);

        } catch(\Exception $exception) {
            throw new HttpException(400, "Invalid data : {$exception->getMessage()}");
        }
    }

    /**
     * @OA\Post(
     *     path="/api/tumbler",
     *     tags={"tumbler"},
     *     summary="Store a newly created item",
     *     operationId="store",
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body description",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Tumbler",
     *             example={"tumbler_name": "Elegan Tumbler", 
     *                      "description": "Tumbler elegan dengan sentuhan warna emas yang menawan",
     *                      "price": 95000}
     *         ),
     *     ),
     *     security={{"passport_token_ready":{},"passport":{}}}
     * )
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tumbler_name' => 'required|unique:tumbler',
            ]);
            if ($validator->fails()) {
                throw new HttpException(400, $validator->messages()->first());
            }
            $tumbler = new Tumbler;
            $tumbler->fill($request->all())->save();

            return $tumbler;
        } catch (\Exception $exception) {
            throw new HttpException(400, "Invalid data - {$exception->getMessage()}");
        }
    }

    /**
     * @OA\Get(
     *     path="/api/tumbler/{id}",
     *     tags={"tumbler"},
     *     summary="Display the specified items",
     *     operationId="show",
     *     @OA\Response(
     *         response=404,
     *         description="item Not found",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of item that needs to be displayed",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $tumbler = Tumbler::find($id);
        if(!$tumbler){
            throw new HttpException(404, 'Item not found');
        }

        return $tumbler;
    }

    /**
     * @OA\Put(
     *     path="/api/tumbler/{id}",
     *     tags={"tumbler"},
     *     summary="Update the specified items",
     *     operationId="update",
     *     @OA\Response(
     *         response=404,
     *         description="item Not found",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of item that needs to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body description",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Tumbler",
     *             example={"tumbler_name": "Elegan Tumbler", 
     *                      "description": "Tumbler elegan dengan sentuhan warna emas yang menawan",
     *                      "price": 95000}
     *         ),
     *     ),
     *     security={{"passport_token_ready":{},"passport":{}}}
     * )
     */
    public function update(Request $request, $id)
    {
        $tumbler = Tumbler::find($id);
        if (!$tumbler) {
            throw new HttpException(404, "Item not found");
        }

        try {
            $validator = Validator::make($request->all(), [
                'tumbler_name' => 'required|unique:tumbler',
            ]);
            if ($validator->fails()) {
                throw new HttpException(400, $validator->messages()->first());
            }
            $tumbler->fill($request->all())->save();
            return response()->json(array('message'=>'Updated successfully'), 200);

        } catch (\Exception $exception) {
            throw new HttpException(400, "Invalid data - {$exception->getMessage()}");
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/tumbler/{id}",
     *     tags={"tumbler"},
     *     summary="Remove the specified items",
     *     operationId="destroy",
     *     @OA\Response(
     *         response=404,
     *         description="item Not found",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of item that needs to be removed",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     security={{"passport_token_ready":{},"passport":{}}}
     * )
     */
    public function destroy($id)
    {
        $tumbler = Tumbler::find($id);
        if(!$tumbler){
            throw new HttpException(404, 'item not found');
        }

        try {
            $tumbler->delete();
            return response()->json(array('message'=>'Deleted successfully'), 200);

        } catch(\Exception $exception) {
            throw new HttpException(400, "Invalid data : {$exception->getMessage()}");
        }
    }
}
