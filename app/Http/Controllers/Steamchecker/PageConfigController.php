<?php

namespace App\Http\Controllers\Steamchecker;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PageConfigController extends Controller
{
    /**
     * The versioned page config (schema_version 2) the extension loads its selectors from.
     * Contract: browser-extensions/steamchecker/docs/remote-config.md.
     */
    public function __invoke(): JsonResponse
    {
        $config = json_decode(file_get_contents(resource_path('steamchecker/config-v2.json')), true, flags: JSON_THROW_ON_ERROR);

        return response()->json($config)->setPublic()->setMaxAge(300);
    }
}
