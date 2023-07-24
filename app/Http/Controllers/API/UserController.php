<?php

namespace App\Http\Controllers\API;

use App\Models\User;
// use App\Services\ImageServices;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use Intervention\Image\ImageManagerStatic as Image;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in UserController.show',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        try {
            $user = User::findOrFail($id);

            if ($request->hasFile('image')) {
                // $image = $request->file('image');
                // $file_name = time() . '.' . $image->extension();
                // $imageResize = Image::make($image->getRealPath());
                // $imageResize = $imageResize->crop(
                //     $request->width,
                //     $request->height,
                //     $request->left,
                //     $request->top
                // );
                // $imageResize->save(public_path($file_name));
                $user->image = 'postman';
            }

            // $user->first_name = $request->first_name;
            // $user->last_name = $request->last_name;
            // $user->location = $request->location;
            // $user->description = $request->description;

            $user->save();

            return response()->json($request->hasFile('image'));
            // return response()->json('User details updated!', 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in UserController.update',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
