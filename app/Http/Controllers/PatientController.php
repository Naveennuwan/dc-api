<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function GetAll()
    {
        return Patient::where('is_deleted', false)
            ->get();
    }

    public function GetActive()
    {
        return Patient::where('is_active', true)
            ->where('is_deleted', false)
            ->get();
    }

    public function GetById($id)
    {
        $patient = Patient::find($id);

        if ($patient) {
            return response()->json($patient);
        } else {
            return response()->json(['message' => 'Patient not found'], 404);
        }
    }

    public function Store(PatientRequest $request)
    {
        $user = Auth::user();
        $patient = new Patient;

        $patient->patient_name = $request->input('patient_name');
        $patient->patient_incharge = $request->input('patient_incharge');
        $patient->patient_address = $request->input('patient_address');
        $patient->patient_contact_no = $request->input('patient_contact_no');
        $patient->patient_type_id = $request->input('patient_type_id');
        $patient->is_active = $request->input('is_active') ?? true;
        $patient->created_by = $user->id;

        if ($patient->save()) {
            return response()->json(['message' => 'Patient created successfully']);
        } else {
            return response()->json(['message' => 'Failed to create Patient'], 500);
        }
    }

    public function Update(PatientRequest $request, $id)
    {
        $user = Auth::user();
        $patient = Patient::find($id);

        if ($patient) {
            $patient->patient_name = $request->input('patient_name');
            $patient->patient_incharge = $request->input('patient_incharge');
            $patient->patient_address = $request->input('patient_address');
            $patient->patient_contact_no = $request->input('patient_contact_no');
            $patient->patient_type_id = $request->input('patient_type_id');
            $patient->is_active = $request->input('is_active') ?? true;
            $patient->updated_by = $user->id;

            if ($patient->save()) {
                return response()->json(['message' => 'Patient updated successfully']);
            } else {
                return response()->json(['message' => 'Failed to update Patient'], 500);
            }
        } else {
            return response()->json(['message' => 'Patient not found'], 404);
        }
    }

    public function SoftDelete($id)
    {
        $user = Auth::user();
        $patient = Patient::find($id);

        if ($patient) {
            $patient->is_deleted = true;
            $patient->deleted_by = $user->id;
            $patient->deleted_at = now();

            if ($patient->save()) {
                return response()->json(['message' => 'Patient deleted successfully']);
            } else {
                return response()->json(['message' => 'Failed to delete Patient'], 500);
            }
        } else {
            return response()->json(['message' => 'Patient not found'], 404);
        }
    }
}
