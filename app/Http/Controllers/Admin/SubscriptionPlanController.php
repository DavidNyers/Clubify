<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Für Enum Rule

class SubscriptionPlanController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');
        $query = SubscriptionPlan::query();

        if ($searchTerm) {
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        $subscriptionPlans = $query->orderBy('sort_order')->orderBy('name')->paginate(15);

        return view('admin.subscription-plans.index', compact('subscriptionPlans', 'searchTerm'));
    }

    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        // Features aus Textarea (eine pro Zeile) in Array umwandeln
        if (!empty($validatedData['features'])) {
            $validatedData['features'] = array_map('trim', explode("\n", $validatedData['features']));
            $validatedData['features'] = array_filter($validatedData['features']); // Leere Zeilen entfernen
        } else {
            $validatedData['features'] = null; // Oder leeres Array: []
        }

        // Boolean für is_active korrekt behandeln
        $validatedData['is_active'] = $request->boolean('is_active');

        SubscriptionPlan::create($validatedData);

        return redirect()->route('admin.subscription-plans.index')
                         ->with('success', 'Abo-Plan erfolgreich erstellt.');
    }

    public function show(SubscriptionPlan $subscriptionPlan)
    {
        // Direkt zum Bearbeiten weiterleiten
        return redirect()->route('admin.subscription-plans.edit', $subscriptionPlan);
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plans.edit', compact('subscriptionPlan'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
         // Passe unique Regeln für das Update an
        $validatedData = $request->validate($this->validationRules($subscriptionPlan->id));

        // Features aus Textarea (eine pro Zeile) in Array umwandeln
        if (!empty($validatedData['features'])) {
             $validatedData['features'] = array_map('trim', explode("\n", $validatedData['features']));
             $validatedData['features'] = array_filter($validatedData['features']);
        } else {
            $validatedData['features'] = null; // Oder leeres Array: []
        }

        // Boolean für is_active korrekt behandeln
        $validatedData['is_active'] = $request->boolean('is_active');


        $subscriptionPlan->update($validatedData);

        return redirect()->route('admin.subscription-plans.index')
                         ->with('success', 'Abo-Plan erfolgreich aktualisiert.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        // Vorsicht: Hier fehlt die Prüfung auf aktive Abonnenten! Für später vormerken.
        try {
             $subscriptionPlan->delete();
             return redirect()->route('admin.subscription-plans.index')
                              ->with('success', 'Abo-Plan erfolgreich gelöscht.');
        } catch (\Exception $e) {
             // Generischer Fehler, falls z.B. Foreign Key Constraints greifen
             report($e); // Fehler loggen
             return redirect()->route('admin.subscription-plans.index')
                              ->with('error', 'Abo-Plan konnte nicht gelöscht werden.');
        }
    }

    /**
     * Hilfsfunktion für Validierungsregeln (DRY)
     */
    protected function validationRules($ignoreId = null): array
    {
         // Basisregeln
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('subscription_plans', 'name')->ignore($ignoreId)],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_interval' => ['required', Rule::in(['month', 'year'])],
            'description' => ['nullable', 'string'],
            'features' => ['nullable', 'string'], // Validieren als String aus Textarea
            'stripe_plan_id' => ['nullable', 'string', 'max:255', Rule::unique('subscription_plans', 'stripe_plan_id')->ignore($ignoreId)],
            'paypal_plan_id' => ['nullable', 'string', 'max:255', Rule::unique('subscription_plans', 'paypal_plan_id')->ignore($ignoreId)],
            'is_active' => ['nullable', 'boolean'], // Wird später explizit behandelt
            'trial_days' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['required', 'integer'],
        ];

        return $rules;
    }
}