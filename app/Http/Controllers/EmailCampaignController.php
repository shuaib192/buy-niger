<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = EmailCampaign::with('creator')->latest()->paginate(20);
        return view('superadmin.email.campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templates = EmailTemplate::where('is_active', true)->get();
        return view('superadmin.email.campaigns.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'subject' => 'required|string',
            'template_id' => 'required|exists:email_templates,id',
            'target_audience' => 'required|in:all,customers,vendors,custom',
        ]);

        $template = EmailTemplate::findOrFail($request->template_id);

        EmailCampaign::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $template->body, // Snapshot of current template
            'status' => 'draft', // Default to draft, user can schedule later
            'target_audience' => $request->target_audience,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('superadmin.email.campaigns.index')->with('success', 'Campaign created as draft.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailCampaign $campaign)
    {
        return view('superadmin.email.campaigns.show', compact('campaign'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailCampaign $campaign)
    {
        $campaign->delete();
        return back()->with('success', 'Campaign deleted.');
    }
}
