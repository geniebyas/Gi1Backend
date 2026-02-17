<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function getAllJobs(Request $request)
    {
        $jobs = \App\Models\JobMst::with('creator');
        if ($request->has('status')) {
            $jobs->where('status', $request->input('status'));
        }
        $jobs = $jobs->get();
        return response()->json([
            'status'=>1,
            'data'=>$jobs,
            'message'=>'Jobs fetched successfully'
        ]);
    }
    public function createJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'experience' => 'nullable|string',
            'salary' => 'nullable|string',
            'skills' => 'nullable|string',
            'location' => 'nullable|string',
            'company' => 'nullable|string',
            'website' => 'nullable|url',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'type' => 'required',
            'created_by' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>$validator->errors()->first()
            ]);
        }

        $jobData = $request->only(['name', 'description', 'experience', 'salary', 'skills', 'location', 'company', 'website', 'type', 'created_by']);
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = time() . '_' . $file->getExtension();
            $filePath = $file->storeAs('public/job_banners', $filename);
            $jobData['banner'] = str_replace('public/', '', $filePath);
        }
        $job = \App\Models\JobMst::create($jobData);
        return response()->json([
            'status'=>1,
            'data'=>$job,
            'message'=>'Job created successfully'
        ]);
    }

    public function getJob(Request $request,$id){
        $job = \App\Models\JobMst::with('creator')->find($id);
        if(!$job){
            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>'Job not found'
            ]);
        }
        return response()->json([
            'status'=>1,
            'data'=>$job,
            'message'=>'Job fetched successfully'
        ]);
    }
    public function updateJob(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'experience' => 'nullable|string',
            'salary' => 'nullable|string',
            'skills' => 'nullable|string',
            'location' => 'nullable|string',
            'company' => 'nullable|string',
            'website' => 'nullable|url',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'type' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>$validator->errors()->first()
            ]);
        }
        $job = \App\Models\JobMst::find($id);
        if(!$job){

            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>'Job not found'
            ]);
        }
        $jobData = $request->only(['name', 'description', 'experience', 'salary', 'skills', 'location', 'company', 'website', 'type']);
        if ($request->hasFile('banner')) {
            if($job->banner){
                Storage::delete('public/'.$job->banner);
            }
            $file = $request->file('banner');
            $filename = time() . '_' . $file->getExtension();
            $filePath = $file->storeAs('public/job_banners', $filename);
            $jobData['banner'] = str_replace('public/', '', $filePath);
        }
        $job->merge($jobData);
        $job->save();
        return response()->json([
            'status'=>1,
            'data'=>$job,
            'message'=>'Job updated successfully'
        ]);
    }

    public function deleteJob(Request $request,$id){
        $job = \App\Models\JobMst::find($id);
        if(!$job){
            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>'Job not found'
            ]);
        }
        $job->status = 'inactive';
        $job->save();
        $job->delete();
        return response()->json([
            'status'=>1,
            'data'=>null,
            'message'=>'Job deleted successfully'
        ]);
    }
    public function applyJob(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'applicant_name' => 'required',
            'applicant_email' => 'required|email',
            'applicant_phone' => 'required',
            'cover_letter' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf,doc,docx'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>$validator->errors()->first()
            ]);
        }
        $job = \App\Models\JobMst::find($id);
        if(!$job){
            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>'Job not found'
            ]);
        }
        $applicationData = $request->only(['applicant_name', 'applicant_email', 'applicant_phone', 'cover_letter']);
        $applicationData['job_id'] = $id;
        $applicationData['created_by'] = $request->header('uid');
        $applicationData['received_by'] = $job->created_by;
        $applicationData['status'] = 'pending';
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $filename = time() . '_' . $file->getExtension();
            $filePath = $file->storeAs('public/job_resumes', $filename);
            $applicationData['resume'] = str_replace('public/', '', $filePath);
        }
        $application = \App\Models\JobApplication::create($applicationData);
        return response()->json([
            'status'=>1,
            'data'=>$application,
            'message'=>'Job application submitted successfully'
        ]);
    }

    public function getJobApplications(Request $request,$id){
        $job = \App\Models\JobMst::find($id);
        if(!$job){
            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>'Job not found'
            ]);
        }
        $applications = \App\Models\JobApplication::with('applicant')->where('job_id',$id)->get();
        return response()->json([
            'status'=>1,
            'data'=>$applications,
            'message'=>'Job applications fetched successfully'
        ]);
    }
    public function getJobApplication(Request $request,$id){
        $application = \App\Models\JobApplication::with('applicant')->find($id);
        if(!$application){
            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>'Job application not found'
            ]);
        }
        return response()->json([
            'status'=>1,
            'data'=>$application,
            'message'=>'Job application fetched successfully'
        ]);

    }
    public function updateJobApplication(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,accepted,rejected'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>$validator->errors()->first()
            ]);
        }
        $application = \App\Models\JobApplication::find($id);
        if(!$application){
            return response()->json([
                'status'=>0,
                'data'=>null,
                'message'=>'Job application not found'
            ]);
        }
        $application->status = $request->input('status');
        $application->save();
        return response()->json([
            'status'=>1,
            'data'=>$application,
            'message'=>'Job application updated successfully'
        ]);
    }
    

}
