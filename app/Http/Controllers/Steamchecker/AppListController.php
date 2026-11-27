<?php

namespace App\Http\Controllers\Steamchecker;

use App\Http\Controllers\Controller;
use App\Services\SteamAppListFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppListController extends Controller
{
    /**
     * Every synced Steam app as {"apps":[{"appid":..,"name":..}]}.
     */
    public function __invoke(SteamAppListFile $appList): BinaryFileResponse
    {
        return response()
            ->file($appList->path(), ['Content-Type' => 'application/json'])
            ->setMaxAge(3600)
            ->setPublic();
    }
}
