<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomField;

class CustomFieldController extends Controller
{
    // Allowed fields (you can change these anytime)
    private array $allowedFields = [
        'address',
        'date_of_birth',
    ];

    // Store or update a custom field
    public function store(Request $request)
    {
        $request->validate([
            'field_name' => 'required|string',
            'value' => 'nullable|string',
        ]);

        // Check if field_name is allowed
        if (!in_array($request->field_name, $this->allowedFields)) {
            return response()->json(['message' => 'Field not allowed'], 422);
        }

        $user = $request->user();

        // Upsert (update if exists, create if not)
        $field = CustomField::updateOrCreate(
            [
                'user_id' => $user->id,
                'field_name' => $request->field_name
            ],
            [
                'value' => $request->value
            ]
        );

        return response()->json([
            'message' => 'Field saved successfully',
            'field' => $field
        ]);
    }

    // Get all custom fields for the logged-in user
    public function index(Request $request)
    {
        $fields = CustomField::where('user_id', $request->user()->id)->get();
        return response()->json($fields);
    }

    // Delete a specific custom field
    public function destroy(Request $request, $field_name)
    {
        $deleted = CustomField::where('user_id', $request->user()->id)
            ->where('field_name', $field_name)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Field deleted successfully']);
        }

        return response()->json(['message' => 'Field not found'], 404);
    }

    public function getByUserId($id)
    {
        $fields = CustomField::where('user_id', $id)->get();

        if ($fields->isEmpty()) {
            return response()->json([
                'message' => 'No custom fields found for this user.'
            ], 404);
        }

        return response()->json($fields);
    }

    public function availableFields()
    {
        return response()->json($this->allowedFields);
    }

}
