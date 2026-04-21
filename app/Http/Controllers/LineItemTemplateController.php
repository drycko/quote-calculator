<?php

namespace App\Http\Controllers;

use App\Models\LineItemTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LineItemTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $query = LineItemTemplate::query()->orderBy('template_type')->orderBy('category')->orderBy('name');

        if ($request->filled('type')) {
            $query->where('template_type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $templates  = $query->paginate(50)->withQueryString();
        $categories = LineItemTemplate::distinct()->orderBy('category')->pluck('category');

        return view('templates.index', compact('templates', 'categories'));
    }

    public function create(): View
    {
        return view('templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'category'           => 'required|string|max:100',
            'template_type'      => 'required|in:web,manual,ilead',
            'calculation_type'   => 'required|in:fixed,hourly,percentage,converted',
            'default_rate'       => 'nullable|numeric|min:0',
            'default_percentage' => 'nullable|numeric|min:0|max:100',
            'currency'           => 'nullable|string|size:3',
            'conversion_rate'    => 'nullable|numeric|min:0',
            'is_plugin'          => 'boolean',
            'default_notes'      => 'nullable|string|max:500',
        ]);

        $data['is_plugin'] = $request->boolean('is_plugin');

        LineItemTemplate::create($data);

        return redirect()->route('templates.index')->with('success', 'Template created.');
    }

    public function edit(LineItemTemplate $template): View
    {
        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, LineItemTemplate $template): RedirectResponse
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'category'           => 'required|string|max:100',
            'template_type'      => 'required|in:web,manual,ilead',
            'calculation_type'   => 'required|in:fixed,hourly,percentage,converted',
            'default_rate'       => 'nullable|numeric|min:0',
            'default_percentage' => 'nullable|numeric|min:0|max:100',
            'currency'           => 'nullable|string|size:3',
            'conversion_rate'    => 'nullable|numeric|min:0',
            'is_plugin'          => 'boolean',
            'default_notes'      => 'nullable|string|max:500',
        ]);

        $data['is_plugin'] = $request->boolean('is_plugin');

        $template->update($data);

        return redirect()->route('templates.index')->with('success', 'Template updated.');
    }

    public function destroy(LineItemTemplate $template): RedirectResponse
    {
        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Template deleted.');
    }
}
