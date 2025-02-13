<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Photo;

class PhotosController extends Controller
{

  public function __construct()
  {
      $this->middleware('auth');
  }

    public function create($album_id){
      return view('photos.create')->with('album_id', $album_id);
    }

    public function store(Request $request){
      $this->validate($request, [
        'title' => 'required',
        'description' => 'required',
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      ]);

      // Get filename with extension
      $filenameWithExt = $request->file('photo')->getClientOriginalName();

      // Get just the filename
      $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

      // Get extension
      $extension = $request->file('photo')->getClientOriginalExtension();

      // Create new filename
      $filenameToStore = $filename.'_'.time().'.'.$extension;

      // Uplaod image
      $path= $request->file('photo')->storeAs('public/photos/'.$request->input('album_id'), $filenameToStore);

      // Upload Photo
      $photo = new Photo;
      $photo->album_id = $request->input('album_id');
      $photo->title = $request->input('title');
      $photo->description = $request->input('description');
      $photo->size = $request->file('photo')->getClientSize();
      $photo->photo = $filenameToStore;

      $photo->save();

      return redirect('/albums/'.$request->input('album_id'))->with('success', 'Photo Uploaded');
    }

    public function show($id){
      $photo = Photo::find($id);
  return view('photos.show')->with('photo', $photo);
  }

//new method delete
  public function destroy($id){
       $photo = Photo::find($id);

  if(Storage::delete('public/photos/'.$photo->album_id.'/'.$photo->photo)){
      $photo->delete();
//new directory got
    return redirect('/albums/'.$photo->album_id)->with('success', 'Photo Deleted');
  }
}

public function Download($id)
{
        $photo = Photo::find($id);

      return response()->download(storage_path('app/public/photos/'.$photo->album_id.'/'.$photo->photo));

    }


public function index1()
{
$user_id = auth()->user()->id;

$user = User::find($user_id);

return view('login');
}
}
