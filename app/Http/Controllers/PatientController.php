<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use App\Models\PatientAlergy;
use App\Models\PatientDisease;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function GetAll()
    {
        return Patient::where('is_deleted', false)
            ->with('alergies')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function GetActive()
    {
        return Patient::where('is_active', true)
            ->with('alergies')
            ->where('is_deleted', false)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function GetById($id)
    {
        $patient = Patient::where('id', $id)
            ->with('alergies')
            ->with('disease')
            ->where('is_deleted', false)
            ->first();

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
        $patient->patient_gender = $request->input('patient_gender');
        $patient->patient_age = $request->input('patient_age');
        $patient->patient_type_id = $request->input('patient_type_id');
        $patient->is_active = $request->input('is_active') ?? true;
        $patient->created_by = $user->id;
        $patient->save();
        
        if (!empty($request->patient_alergies)) {
            foreach ($request->patient_alergies as $alergy) {
                $patientAlergy = new PatientAlergy();
                $patientAlergy->patient_id = $patient->id;
                $patientAlergy->alergy_id = $alergy;
                $patientAlergy->save(['timestamps' => false]);
            }
        }
        
        if (!empty($request->patient_diseases)) {
            foreach ($request->patient_diseases as $disease) {
                $patientDisease = new PatientDisease();
                $patientDisease->patient_id = $patient->id;
                $patientDisease->disease_id = $disease;
                $patientDisease->save(['timestamps' => false]);
            }
        }

        return response()->json(['message' => 'Patient created successfully']);
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
            $patient->patient_gender = $request->input('patient_gender');
            $patient->patient_age = $request->input('patient_age');
            $patient->patient_type_id = $request->input('patient_type_id');
            $patient->is_active = $request->input('is_active') ?? true;
            $patient->updated_by = $user->id;
            
            PatientAlergy::where('patient_id', $id)->delete();
            if (!empty($request->patient_alergies)) {
                foreach ($request->patient_alergies as $alergy) {
                    $patientAlergy = new PatientAlergy();
                    $patientAlergy->patient_id = $patient->id;
                    $patientAlergy->alergy_id = $alergy;
                    $patientAlergy->save(['timestamps' => false]);
                }
            }
            
            PatientDisease::where('patient_id', $id)->delete();
            if (!empty($request->patient_diseases)) {
                foreach ($request->patient_diseases as $disease) {
                    $patientDisease = new PatientDisease();
                    $patientDisease->patient_id = $patient->id;
                    $patientDisease->disease_id = $disease;
                    $patientDisease->save(['timestamps' => false]);
                }
            }

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
