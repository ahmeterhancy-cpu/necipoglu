<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        // Marka sayfasindan gelinmisse konu satiri markanin ADIYLA dolsun;
        // adres cubugundaki slug ("dura-bagno") konu satirinda cirkin durur.
        $konu = null;

        if ($slug = $request->query('urun')) {
            $urun = \App\Models\Product::where('slug', $slug)->first(['name', 'sku']);
            $konu = $urun ? trim($urun->name.($urun->sku ? ' ('.$urun->sku.')' : '')) : null;
        }

        if ($slug = $request->query('marka')) {
            $konu = \App\Models\Brand::where('slug', $slug)->value('name') ?? $konu;
        }

        return view('pages.contact', [
            'branches' => Branch::query()->active()->ordered()->get(),
            'konu' => $konu,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:160'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'message' => ['required', 'string', 'max:4000'],
            // Bal küpü: insanlar bu alanı görmez, botlar doldurur.
            'website' => ['prohibited'],
        ], [
            'website.prohibited' => __('site.contact.spam'),
        ]);

        ContactMessage::create([
            ...collect($data)->except('website')->all(),
            'locale' => app()->getLocale(),
            'source' => url()->previous(),
            'ip' => $request->ip(),
        ]);

        return back()->with('contact.sent', true);
    }
}
