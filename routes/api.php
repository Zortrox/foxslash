<?php

use App\Models\SteamGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('games', function (Request $request) {
    return SteamGame::search($request->input('query'))
        ->options([
            'query_by' => 'formatted_name',
            'sort_by' => '_text_match:desc,formatted_name_length:asc', // formatted_name:asc
            'prioritize_token_position' => true,
            'enable_synonyms' => false,
            'enable_typos_for_numerical_tokens' => false,
        ])
        ->get();
});
