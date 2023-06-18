<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateRequest;
use App\Models\TemplateHeader;
use App\Models\TemplateBody;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    public function GetAll($centerid)
    {
        $user = Auth::user();
        return TemplateHeader::with('templateBodies')
            ->with('templateType')
            ->where('template_headers.is_deleted', false)
            ->where('template_center_id', $centerid)
            ->get();
    }

    public function GetActiveByTypes($typeId)
    {
        $user = Auth::user();
        return TemplateHeader::with('templateBodies')
            ->with('templateType')
            ->where('template_headers.is_active', true)
            ->where('template_headers.is_deleted', false)
            ->where('template_type_id', $typeId)
            // ->where('template_center_id', $user->center)
            ->get();
    }

    public function GetById($id)
    {
        $user = Auth::user();
        $template = TemplateHeader::with('templateBodies')
            ->with('templateType')
            ->where('template_center_id', $user->center)
            ->where('id', $id)
            ->get();

        if ($template) {
            return response()->$template;
        } else {
            return response()->json(['message' => 'Information list not found'], 404);
        }
    }

    public function Store(TemplateRequest $request)
    {
        $user = Auth::user();
        $template = new TemplateHeader;        

        $template->template_name = $request->input('template_name');
        $template->template_type_id = $request->input('template_type_id');
        $template->template_center_id = $request->input('template_center_id');
        $template->is_active = $request->input('is_active') ?? true;
        $template->created_by = $user->id;
        $template->save();

        if (!empty($request->template_bodies)) {
            foreach ($request->template_bodies as $body) {
                $templateBody = new TemplateBody();
                $templateBody->template_id = $template->id;
                $templateBody->product_id = $body['product_id'];
                $templateBody->quantity = $body['quantity'];
                $templateBody->save();
            }
        }

        return response()->json(['message' => 'Template created successfully']);
    }

    public function Update(TemplateRequest $request, $id)
    {
        $user = Auth::user();
        $template = TemplateHeader::find($id);

        if ($template) {
            $template->template_name = $request->input('template_name');
            $template->template_type_id = $request->input('template_type_id');
            $template->template_center_id = $request->input('template_center_id');
            $template->is_active = $request->input('is_active') ?? true;
            $template->updated_by = $user->id;

            TemplateBody::where('template_id', $id)->delete();
            if (!empty($request->job_list_selected_job_types)) {
                foreach ($request->template_bodies as $body) {
                    $templateBody = new TemplateBody();
                    $templateBody->template_id = $template->id;
                    $templateBody->product_id = $body['product_id'];
                    $templateBody->quantity = $body['quantity'];
                    $templateBody->created_by = $user->id;
                    $templateBody->save();
                }
            }

            if ($template->save()) {
                return response()->json(['message' => 'Template updated successfully']);
            } else {
                return response()->json(['message' => 'Failed to update Template'], 500);
            }
        } else {
            return response()->json(['message' => 'Template not found'], 404);
        }
    }

    public function SoftDelete($id)
    {
        $user = Auth::user();
        $template = TemplateHeader::find($id);

        if ($template) {
            $template->is_deleted = true;
            $template->deleted_by = $user->id;
            $template->deleted_at = now();

            if ($template->save()) {
                return response()->json(['message' => 'Template deleted successfully']);
            } else {
                return response()->json(['message' => 'Failed to delete Template'], 500);
            }
        } else {
            return response()->json(['message' => 'Template not found'], 404);
        }
    }
}
