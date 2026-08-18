<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Year; 
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the academic years.
     */
    public function details(Request $request)
    {
        if ($request->has('search') && !$request->filled('search')) {
        session()->forget('academic_last_query');
    } 
    // ၂။ User က စာအသစ်ရိုက်ထည့်ပြီး ရှာရင်
    elseif ($request->filled('search')) {
        session(['academic_last_query' => $request->search]);
    } 
    // ၃။ Search ဘောက်စ်က လွတ်နေပြီး session ထဲမှာ အဟောင်းရှိနေရင် အဟောင်းကို ပြန်သုံးမယ်
    elseif (session()->has('academic_last_query')) {
        $request->merge(['search' => session('academic_last_query')]);
    }
        // Capture the search input value from the form
        $search = $request->input('search');

        // Query the database with optional search filtering and fetch all records
        $academicYears = Year::query()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('academic_year', 'like', '%' . $search . '%')
                    ->orWhereRaw("DATE_FORMAT(created_at, '%d-%b-%Y') LIKE ?", ['%' . $search . '%']);
            });
        })
            ->orderBy('created_at', 'desc')
            ->get(); // Fetches all matching entries on a single view layer without pagination splitting

        return view('admin.academicYear.home', compact('academicYears'));
    }

    /**
     * Store a newly created academic year in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string|unique:years,academic_year',
        ]);

        Year::create([
            'academic_year' => $request->academic_year,
        ]);

        return redirect()->route('list#yearDetails')
            ->with('createSuccess', __('Academic year created successfully.'));
    }

    /**
     * Update the specified academic year in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'academic_year' => 'required|string|max:255|unique:years,academic_year,' . $id,
        ]);

        $year = Year::findOrFail($id);
        $year->update([
            'academic_year' => $request->academic_year,
        ]);

        return redirect()->route('list#yearDetails')
            ->with('updateSuccess', __('Academic year updated successfully.'));
    }

    /**
     * Remove the specified academic year from storage.
     */
    public function destroy($id)
    {
        $year = Year::findOrFail($id);
        $year->delete();

        return redirect()->route('list#yearDetails')
            ->with('deleteSuccess', __('Academic year deleted successfully.'));
    }
}