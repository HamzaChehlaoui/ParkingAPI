<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParkingController extends Controller
{

    public function index()
    {
        $parkings = Parking::all();
        return response()->json($parkings, Response::HTTP_OK);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'total_spots' => 'required|integer|min:1',
        ]);

        $parking = Parking::create($validated);

        return response()->json($parking, Response::HTTP_CREATED);
    }


    public function show($id)
    {
        $parking = Parking::find($id);

        if (!$parking) {
            return response()->json(['message' => 'Parking not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($parking, Response::HTTP_OK);
    }


    public function update(Request $request, $id)
    {
        $parking = Parking::find($id);

        if (!$parking) {
            return response()->json(['message' => 'Parking not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string',
            'total_spots' => 'sometimes|integer|min:1',
        ]);

        $parking->update($validated);

        return response()->json($parking, Response::HTTP_OK);
    }


    public function destroy($id)
    {
        $parking = Parking::find($id);

        if (!$parking) {
            return response()->json(['message' => 'Parking not found'], Response::HTTP_NOT_FOUND);
        }

        $parking->delete();

        return response()->json(['message' => 'Parking deleted'], Response::HTTP_OK);
    }
}
