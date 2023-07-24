<?php

namespace App\Services;

use Image;

class ImageServices
{
  public function updateImage($model, $request, $path, $method)
  {
    $image = Image::class::make($request->file('image'));

    if (!empty($model->image)) {
      $currentImage = public_path() . $path . $model->image;

      if (file_exists($currentImage)) {
        unlink($currentImage);
      }
    }

    $file = $request->file('image');
    $extension = $file->getClientOriginalExtension();

    $image->crop(
      $request->width,
      $request->height,
      $request->left,
      $request->top
    );

    $name = time() . '.' . $extension;
    $image->save(public_path() . $path . $name);

    if ($method == 'store') {
      $model->user_id = $request->get('user_id');
    }

    $model->image = $name;
    $model->save();
  }
}
