<?php
namespace App\Traits;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;
trait HasImageTrait
{
    public function addImage($photoPath)
    {
        $image = Image::create([
            'url' => $photoPath,
            'imageable_id' => $this->id,
            'imageable_type' => static::class,
        ]);

        return $image;
    }
    public function updateImage($request)
    {
        if ($request->hasFile('photo')) {
            if (!empty($this->images)) {
                foreach ($this->images as $image) {
                    if ($image instanceof Image) {
                        Storage::delete($image->url);
                        $image->delete();
                    }
                }
            }
            $photoPath = $request->file('photo')->store('photos');
            $this->addImage($photoPath);
        }
}
}
