<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::all();
        return response()->json($reports);
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_name' => 'required|string',
            'beneficiaries' => 'required|integer',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'distribution_date' => 'required|date',
            'proof_file' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $filePath = $request->file('proof_file')->store('proofs', 'public');

        $report = Report::create([
            'program_name' => $request->program_name,
            'beneficiaries' => $request->beneficiaries,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'distribution_date' => $request->distribution_date,
            'proof_file' => $filePath,
            'additional_notes' => $request->additional_notes,
        ]);

        return response()->json(['success', true, 'message' => 'Report submitted successfully', 'data' => $report], 201);
    }

    public function updateReport(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'id' => 'required|integer|exists:reports,id', // Ensure id is valid
            'program_name' => 'required|string',
            'beneficiaries' => 'required|integer',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'distribution_date' => 'required|date',
        ]);

        // Retrieve the report by ID from the request body
        $report = Report::find($request->id);

        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found', 'data' => []], 404);
        }

        // Update report fields
        $report->program_name = $request->program_name;
        $report->beneficiaries = $request->beneficiaries;
        $report->province = $request->province;
        $report->city = $request->city;
        $report->district = $request->district;
        $report->distribution_date = $request->distribution_date;
        $report->additional_notes = $request->additional_notes;

        // If a new proof file is uploaded, handle it
        if ($report->proof_file) {
            $oldFilePath = public_path('proofs/' . $report->proof_file);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // Store the new proof file directly in the 'public' folder
        $file = $request->file('proof_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = 'proofs/' . $fileName;

        // Move the file to the public folder
        $file->move(public_path('proofs'), $fileName);
        $report->proof_file = $filePath;
        // Save the updated report
        $report->save();

        return response()->json(['success' => true, 'message' => 'Report updated successfully', 'data' => $report], 200);
    }


    public function update(Request $request)
    {
        $id = $request->input('id');
        $report = Report::find($id);
        $report->status = $request->status;
        $report->rejection_reason = $request->rejection_reason;
        $report->save();
        return response()->json(['success' => true, 'message' => 'Report updated successfully', 'data' => $report], 200);
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found',
                'data' => [],
            ], 404);
        }

        // Delete the report
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully',
            'data' => [],
        ], 200);
    }

}