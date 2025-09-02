<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Http\Requests\Profile\AddRequest;
use App\Models\Country;
use App\Models\Designation;
use App\Models\Education;
use App\Models\Role;
use App\Models\User;
use App\Models\UserContact;
use App\Models\UserDetail;
use App\Models\UserEmail;
use App\Models\UserExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Profiler\Profile;

class ProfileController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return redirect("dashboard");
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(Request $request)
  {
    $id = $request->query('user_id');
    $userId = $id ?? Auth::id();
    $profile = UserDetail::where('user_id', $userId)->first();
    $user = User::where("id",$userId)->first();
    if ($profile !== null) {
      
      return redirect()->route('profiles.edit',$user->id);
    }
    $countries = Country::get();
    $roles = Role::get();
    $designations = Designation::orderBy('name', 'asc')->get();
    $educations = Education::orderBy('name', 'asc')->get();
    return view($this->view, compact('designations', 'educations', 'roles', 'countries', 'userId','user'));
  }

  /**
   * Store a newly created resource in storage.
   */
  // public function store(AddRequest $request)
  public function store(AddRequest $request)
  {
    $validated = $request->validated();

    $documentsPath = 'public/uploads/users/' . $this->formatUserName($validated) . '/';
    // dd($validated,$request->file("upload_national_identity"),$documentsPath);
    $validated['file_path'] = $documentsPath;

    $contactDetail = Arr::only($validated, ['file_path', 'user_id', 'country_id', 'city_id', 'complete_address', 'national_identity', 'working_designation', 'responsibilities', 'education_id', 'status', 'start_year', 'end_year', 'disease', 'treatment', 'no_of_years']);
    $emails = $request->input("emails");
    try {
      $userDetail = UserDetail::createWithTransaction($contactDetail);
      if (isset($emails) && is_array($emails)) {
        foreach ($emails as $emailData) {
          UserEmail::create([
            'user_detail_id' => $userDetail->id,
            'email' => $emailData['email'],
          ]);
        }
      }
      if (isset($validated['experiences']) && is_array($validated['experiences'])) {
        foreach ($validated['experiences'] as $experience) {
          if ($experience['designation'] != null) {
            UserExperience::create([
              'user_detail_id' => $userDetail->id,
              'company' => $experience['company'],
              'years' => $experience['years'],
              'designation_id' => $experience['designation'],
            ]);
          } 
        }
      }
      if (isset($validated['contacts']) && is_array($validated['contacts'])) {
        foreach ($validated['contacts'] as $contact) {
          UserContact::create([
            'user_detail_id' => $userDetail->id,
            'contact_type' => $contact['contact_types'],
            'contact_number' => $contact['contact_numbers'],
          ]);
        }
      }
      if (null != $request->file("photo")) {
        $folderPath = 'uploads/users/' . $this->formatUserName($validated) . '/user-images';
        if (Storage::disk('public')->exists($folderPath)) {
          Storage::disk('public')->deleteDirectory($folderPath);
        }
        $userImage =  'image' . '.' . $request->file("photo")->getClientOriginalExtension();
        Storage::putFileAs($documentsPath . 'user-images/', $request->file("photo"), $userImage);
        $photo = $userImage;

        if (Auth::id() == $validated['user_id']) {
          session()->put('user_image', $photo);
          session()->put('has_user', true);
        }
      }
      $this->handleFileUploads($request, $documentsPath, ['upload_national_identity', 'misc_documents', 'medical_documents']);
     
      return redirect()->route('profiles.edit', $userDetail->user_id)->with('success', '' . $this->controller . ' created successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    try {

      $countries = Country::get();
      $roles = Role::get();
      $result = UserDetail::with('experiences', 'contacts', 'emails')->where('user_id', $id)->first();
      // dd($result->emails->toArray());
      $designations = Designation::orderBy('name', 'asc')->get();
      $educations = Education::orderBy('name', 'asc')->get();
      $user = User::where("id",$id)->first();

      $filesMiscs = Storage::disk('public')->files('uploads/users/' . $this->formatUserName($result) . '/misc_documents');
      $filteredFilesMisc = array_filter($filesMiscs, function ($filesMisc) {
        return in_array(pathinfo($filesMisc, PATHINFO_EXTENSION), ['pdf', 'doc', 'docx', 'jpeg', 'JPEG', 'png', 'PNG', 'jpg', 'JPG']);
      });
      $filteredFilesMisc = array_map('basename', $filteredFilesMisc);

      $filesMedicals = Storage::disk('public')->files('uploads/users/' . $this->formatUserName($result) . '/medical_documents');
      $filteredFilesMedical = array_filter($filesMedicals, function ($filesMedical) {
        return in_array(pathinfo($filesMedical, PATHINFO_EXTENSION), ['pdf', 'jpeg', 'JPEG', 'png', 'PNG', 'jpg', 'JPG']);
      });
      $filteredFilesMedical = array_map('basename', $filteredFilesMedical);



      $filesIdentities = Storage::disk('public')->files('uploads/users/' . $this->formatUserName($result) . '/upload_national_identity');
      $filteredFilesIdentities = array_filter($filesIdentities, function ($filesIdentity) {
        return in_array(pathinfo($filesIdentity, PATHINFO_EXTENSION), ['jpeg', 'JPEG', 'png', 'PNG', 'jpg', 'JPG']);
      });
      $filteredFilesIdentities = array_map('basename', $filteredFilesIdentities);


      return view($this->view, compact('designations', 'educations', 'roles', 'countries', 'result', 'filteredFilesMisc', 'filteredFilesMedical', 'filteredFilesIdentities','user'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to find ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(AddRequest $request, string $id)
  {
    $validated = $request->validated();
    $user_name = ucwords(
      Str::of(Auth::user()->name)
        ->lower()
    );
    $user_name =  str_replace(' ', '', $user_name);
    $documentsPath = 'public/uploads/users/' . $this->formatUserName($validated) . '/';
    $validated['file_path'] = $documentsPath;

    $contactDetail = Arr::only($validated, ['file_path', 'user_id', 'country_id', 'city_id', 'complete_address', 'national_identity', 'working_designation', 'responsibilities', 'education_id', 'status', 'start_year', 'end_year', 'disease', 'treatment', 'no_of_years']);
    $emails = $request->input("emails");

    try {
      $userDetail = UserDetail::updateWithTransaction($id, $contactDetail);
      if (isset($emails) && is_array($emails)) {
        $userDetail->emails()->forceDelete();
        foreach ($emails as $emailData) {
          UserEmail::create([
            'user_detail_id' => $userDetail->id,
            'email' => $emailData['email'],
          ]);
        }
      }
      if (isset($validated['experiences']) && is_array($validated['experiences'])) {
        $userDetail->experiences()->forceDelete();
        foreach ($validated['experiences'] as $experience) {
          UserExperience::create([
            'user_detail_id' => $userDetail->id,
            'company' => $experience['company'],
            'years' => $experience['years'],
            'designation_id' => $experience['designation'],
          ]);
        }
      }
      if (isset($validated['contacts']) && is_array($validated['contacts'])) {

        $userDetail->contacts()->forceDelete();
        foreach ($validated['contacts'] as $contact) {
          UserContact::create([
            'user_detail_id' => $userDetail->id,
            'contact_type' => $contact['contact_types'],
            'contact_number' => $contact['contact_numbers'],
          ]);
        }
      }
      if (null != $request->file("photo")) {
        $folderPath = 'uploads/users/' . $this->formatUserName($validated) . '/user-images';
        if (Storage::disk('public')->exists($folderPath)) {
          Storage::disk('public')->deleteDirectory($folderPath);
        }
        $userImage =  'image' . '.' . $request->file("photo")->getClientOriginalExtension();
        Storage::putFileAs($documentsPath . 'user-images/', $request->file("photo"), $userImage);
        $photo = $userImage;
        if (Auth::id() == $validated['user_id']) {
          session()->put('user_image', $photo);
        }
      }
      $this->handleFileUploads($request, $documentsPath, ['upload_national_identity', 'misc_documents', 'medical_documents']);
      return redirect()->route('profiles.edit', $userDetail->user_id)->with('success', '' . $this->controller . ' updated successfully.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to create ' . $this->controller . ': ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id) {}
  /**
   * Handle file uploads for various document types.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  string  $documentsPath
   * @param  array  $fileKeys
   * @return void
   */
  protected function handleFileUploads($request, $documentsPath, $fileKeys)
  {
    foreach ($fileKeys as $key) {
      if ($request->hasFile($key)) {
        foreach ($request->file($key) as $image) {
          if ($image->isValid()) {
            $imageName = uniqid() . '_' . $image->getClientOriginalName();
            Storage::putFileAs($documentsPath . $key . '/', $image, $imageName);
          }
        }
      }
    }
  }
  protected function formatUserName($validated)
  {
    $user = User::findOrFail($validated['user_id'] ?? $validated->user_id);
    $user_name = ucwords(Str::of($user->name)->lower());
    $user_name = str_replace(' ', '', $user_name);
    $formatted_user_name = 'FMB_' . $user->id . '_' . $user_name;
    return $formatted_user_name;
  }
}
