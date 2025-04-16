<?php

namespace App\Http\Controllers;

use App\Events\TripAccepted;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;
use App\Events\TripStarted;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required',
        ]);

        return $request->user()->trips()->create([
            'origin'=> $request->origin,
            'destination'=> $request->destination,
            'destination_name'=> $request->destination_name,
        ]);
    }

    public function show(Request $request, Trip $trip)
    {
        if($trip->user->id === $request->user()->id){
            return $trip;
        }

        if ($trip->driver && $request->user()->driver){
        if($trip->driver->id === $request->user()->driver->id){
            return $trip;
        }}
        return response()->json([
            'message' => 'Cannot find trip'
        ], 404);
    }

    public function accept(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required',
        ]);
        // driver accepts trip
        $trip->update([
            'driver_id'=>$request->user()->id,
            'driver_location'=>$request->driver_location,
        ]);

        $trip->load('driver.user');

        return $trip;

        TripAccepted::dispatch($trip, $request->user());
    }

    public function start(Request $request, Trip $trip)
    {
        //driver starts trip
        $trip->update([
            'is_started' => true,
        ]);

        $trip->load('driver.user');
        return $trip;
        TripStarted::dispatch($trip, $request->user());
    }

    public function end(Request $request, Trip $trip)
    {
        // driver ends trip
        $trip->update([
            'is_completed' => true,
        ]);
        $trip->load('driver.user');
        return $trip;
        TripEnded::dispatch($trip, $request->user());
    }

    public function location(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required',
        ]);
        //update drivers location
        $trip->update([
            'driver_location' => $request->driver_location,
        ]);

        $trip->load('driver.user');
        return $trip;
        TripLocationUpdated::dispatch($trip, $request->user());
    }
}
