<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSteamGameRequest;
use App\Http\Requests\UpdateSteamGameRequest;
use App\Models\SteamGame;

class SteamGameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSteamGameRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SteamGame $steamGame)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SteamGame $steamGame)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSteamGameRequest $request, SteamGame $steamGame)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SteamGame $steamGame)
    {
        //
    }
}
