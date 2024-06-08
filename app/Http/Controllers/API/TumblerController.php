<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Tumbler;

class TumblerController extends Controller
{
    /**
     * Display a listing of the item.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Tumbler::get();
    }

    /**
     * Store a newly created item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $tumbler = new Tumbler;
            $tumbler->fill($request->validated())->save();

            return $tumbler;
        } catch (\Exception $exception) {
            throw new HttpException(400, "Invalid data - {$exception->getMessage()}");
        }
    }

    /**
     * Display the specified item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tumbler = Tumbler::findOrFail($id);

        return $tumbler;
    }

    /**
     * Update the specified item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }

        try {
            $tumbler = Tumbler::find($id);
            $tumbler->fill($request->validated())->save();

            return $tumbler;
        } catch (\Exception $exception) {
            throw new HttpException(400, "Invalid data - {$exception->getMessage()}");
        }
    }

    /**
     * Remove the specified item from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tumbler = Tumbler::findOrFail($id);
        $tumbler->delete();

        return response()->json(null, 204);
    }
}
