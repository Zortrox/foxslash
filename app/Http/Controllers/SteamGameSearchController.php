<?php

namespace App\Http\Controllers;

use App\Models\SteamGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SteamGameSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // validate names as required string array
        $request->validate([
            'names' => 'required|array',
            'names.*' => 'string',
        ]);

        $names = $request->input('names');
        Log::debug('Searching for games with names: '.implode(', ', $names));

        // get games where name is in names or alias is in names
        $games = SteamGame::query()
            ->select(['appid', 'name'])
            ->whereIn('name', $names)
            ->orWhereHas('aliases', function ($query) use ($names) {
                $query->whereIn('alias', $names);
            })
            ->with('aliases')
            ->get()
            ->unique('name')
            ->setHidden(['aliases'])
            ->append(['alias_list']);

        return response()->json($games);
    }
}
