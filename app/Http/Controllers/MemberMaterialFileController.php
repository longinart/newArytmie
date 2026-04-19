<?php

namespace App\Http\Controllers;

use App\Models\MemberResourceFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberMaterialFileController extends Controller
{
    /**
     * Stažení souboru z členské sekce (jen po hesle v session).
     */
    public function download(Request $request, MemberResourceFile $memberResourceFile): StreamedResponse
    {
        abort_unless($request->session()->get('members_area_unlocked') === true, 403);

        $disk = Storage::disk('members_private');
        abort_unless($disk->exists($memberResourceFile->stored_path), 404);

        return $disk->download($memberResourceFile->stored_path, $memberResourceFile->original_name);
    }
}
