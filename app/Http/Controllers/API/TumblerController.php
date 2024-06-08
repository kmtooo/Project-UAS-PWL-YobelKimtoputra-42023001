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
     *     )
     * )
     */
    public function index()
    {
        return Tumbler::get();
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
     *     )
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
     *     )
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
     *     )
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
