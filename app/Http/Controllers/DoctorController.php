<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Doctor;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $doctors = Doctor::latest()->get();
            return response()->json([
                'error' => false,
                'message' => 'All doctors',
                'data' => [
                    'doctors' => $doctors,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDoctorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDoctorRequest $request)
    {
        try {
            $data = [
                'name' => $request->input('name'),
                'designation' => $request->input('designation'),
                'phone' => $request->input('phone'),
                'biography' => $request->input('biography'),
            ];
            if ($request->has('image')) {
                $uploadFolder = 'doctors';
                $image = $request->file('image');
                $imagePath = $image->store($uploadFolder, 'public');

                $data['image'] = $imagePath;
            }

            $doctor = Doctor::create($data);

            if ($doctor->image) {
                $doctor->image = asset(Storage::url($doctor->image));
            }

            return response()->json([
                'error' => false,
                'message' => 'Doctor created successfully.',
                'data' => [
                    'doctor' => $doctor,
                ],
            ], 201);
        } catch(Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function show(Doctor $doctor)
    {
        try {
            $doctor->image = asset(Storage::url($doctor->image));
            return response()->json([
                'error' => false,
                'message' => 'Doctor created successfully.',
                'data' => [
                    'doctor' => $doctor,
                ],
            ]);
        } catch(Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDoctorRequest  $request
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        try {
            $data = [
                'name' => $request->input('name') ?? $doctor->name,
                'designation' => $request->input('designation') ?? $doctor->designation,
                'phone' => $request->input('phone'),
                'biography' => $request->input('biography'),
            ];

            if ($request->has('image')) {
                if ($doctor->image) {
                    if(Storage::disk('public')->exists($doctor->image)) {
                        Storage::disk('public')->delete($doctor->image);
                    }
                }
                $imagePath = $request->file('image')->store('doctors', 'public');
                $data['image'] = $imagePath;
            }
            $doctor->update($data);

            $doctor->image = asset(Storage::url($doctor->image));

            return response()->json([
                'error' => false,
                'message' => 'Doctor updated successfully.',
                'data' => [
                    'doctor' => $doctor,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Doctor $doctor)
    {
        try {
            if ($doctor->image) {
                if(Storage::disk('public')->exists($doctor->image)) {
                    Storage::disk('public')->delete($doctor->image);
                }
            }
            $doctor->delete();
            return response()->json([
                'error' => false,
                'message' => 'Doctor deleted successfully.',
                'data' => [
                    'doctor' => $doctor,
                ],
            ]);
        } catch(Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
