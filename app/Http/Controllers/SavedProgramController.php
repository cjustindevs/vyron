<?php

namespace App\Http\Controllers;

use App\Models\SavedProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SavedProgramController extends Controller
{
    /**
     * My Programs – saved workout library shown as program cards.
     */
    public function index(): View
    {
        $programs = SavedProgram::where('user_id', Auth::id())
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('saved-programs.index', compact('programs'));
    }

    /**
     * Remove a saved program from the library.
     */
    public function destroy(Request $request, SavedProgram $program): RedirectResponse
    {
        if ((int) $program->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $program->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Program removed from your library.']);
        }

        return back()->with('success', 'Program removed from your library.');
    }
}