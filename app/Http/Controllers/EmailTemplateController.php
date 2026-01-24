<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = EmailTemplate::latest()->paginate(20);
        return view('superadmin.email.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.email.templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:email_templates',
            'subject' => 'required',
            'body' => 'required',
            'variables' => 'nullable|string' // Input as comma-separated string, converted to array
        ]);

        $variables = $request->variables 
            ? array_map('trim', explode(',', $request->variables)) 
            : [];

        EmailTemplate::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $request->body,
            'variables' => $variables,
            'is_active' => true
        ]);

        return redirect()->route('superadmin.email.templates.index')->with('success', 'Template created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $template)
    {
        return view('superadmin.email.templates.edit', compact('template'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailTemplate $template)
    {
        $request->validate([
            'name' => 'required|unique:email_templates,name,' . $template->id,
            'subject' => 'required',
            'body' => 'required',
            'variables' => 'nullable|string'
        ]);

        $variables = $request->variables 
            ? array_map('trim', explode(',', $request->variables)) 
            : [];

        $template->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $request->body,
            'variables' => $variables,
        ]);

        return redirect()->route('superadmin.email.templates.index')->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $template)
    {
        $template->delete();
        return back()->with('success', 'Template deleted successfully.');
    }
}
