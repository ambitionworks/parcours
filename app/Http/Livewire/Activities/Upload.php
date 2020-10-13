<?php

namespace App\Http\Livewire\Activities;

use App\Models\ActivityUpload;
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;

    public $file;

    public function save()
    {
        $this->validate([
            'file' => 'required|file|mimetypes:application/octet-stream|max:12288',
        ], [
            'mimetypes' => __('Please upload a .fit file.'),
        ]);

        $path = $this->file->store('activity_uploads');

        $upload = ActivityUpload::create(['file_path' => $path]);

        return redirect()->route('activities.show', $upload->activity);
    }

    public function render()
    {
        return view('activities.upload');
    }
}
