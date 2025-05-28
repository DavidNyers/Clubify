@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Name --}}
    <div>
        <label for="name"
            class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
        <input id="name" class="block mt-1 w-full" type="text" name="name"
            value="{{ old('name', $plan->name ?? '') }}" required autofocus />
        @error('name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Preis --}}
    <div>
        <label for="price"
            class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Preis') }}</label>
        <input id="price" class="block mt-1 w-full" type="number" step="0.01" min="0" name="price"
            value="{{ old('price', $plan->price ?? '0.00') }}" required />
        @error('price')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Währung --}}
    <div>
        <label for="currency"
            class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Währung') }}</label>
        <input id="currency" class="block mt-1 w-full" type="text" name="currency"
            value="{{ old('currency', $plan->currency ?? 'EUR') }}" required maxlength="3" />
        @error('currency')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Billing Interval --}}
    <div>
        <label for="billing_interval"
            class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Abrechnungsintervall') }}</label>
        <select id="billing_interval" name="billing_interval" class="block mt-1 w-full" required>
            <option value="month"
                {{ old('billing_interval', $plan->billing_interval ?? 'month') == 'month' ? 'selected' : '' }}>
                Monatlich</option>
            <option value="year"
                {{ old('billing_interval', $plan->billing_interval ?? '') == 'year' ? 'selected' : '' }}>Jährlich
            </option>
        </select>
        @error('billing_interval')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Beschreibung --}}
    <div class="md:col-span-2">
        <label for="description"
            class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Beschreibung') }}</label>
        <textarea id="description" name="description" rows="4" class="block mt-1 w-full">{{ old('description', $plan->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Features (als Textarea, 1 pro Zeile) --}}
    <div class="md:col-span-2">
        <label for="features"
            class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Features (eins pro Zeile)') }}</label>
        {{-- Wir wandeln das Array für die Textarea in einen String um --}}
        <textarea id="features" name="features" rows="5" class="block mt-1 w-full">{{ old('features', isset($plan->features) && is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
        @error('features')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Stripe Plan ID --}}
    <div>
        <label for="stripe_plan_id"
            class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Stripe Plan ID (Optional)') }}</label>
        <input id="stripe_plan_id" class="block mt-1 w-full" type="text" name="stripe_plan_id"
            value="{{ old('stripe_plan_id', $plan->stripe_plan_id ?? '') }}" />
        @error('stripe_plan_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- PayPal Plan ID --}}
    <div>
        <label for="paypal_plan_id"
            class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('PayPal Plan ID (Optional)') }}</label>
        <input id="paypal_plan_id" class="block mt-1 w-full" type="text" name="paypal_plan_id"
            value="{{ old('paypal_plan_id', $plan->paypal_plan_id ?? '') }}" />
        @error('paypal_plan_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Testtage --}}
    <div>
        <label for="trial_days"
            class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Testtage (Optional)') }}</label>
        <input id="trial_days" class="block mt-1 w-full" type="number" min="0" name="trial_days"
            value="{{ old('trial_days', $plan->trial_days ?? 0) }}" />
        @error('trial_days')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Sort Order --}}
    <div>
        <label for="sort_order"
            class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Sortierreihenfolge') }}</label>
        <input id="sort_order" class="block mt-1 w-full" type="number" name="sort_order"
            value="{{ old('sort_order', $plan->sort_order ?? 0) }}" required />
        @error('sort_order')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Aktiv Checkbox --}}
    <div class="md:col-span-2 flex items-center">
        <input id="is_active" name="is_active" type="checkbox" value="1"
            {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}
            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
        <label for="is_active"
            class="ml-2 block text-sm text-gray-700 dark:text-gray-300">{{ __('Plan ist aktiv (kann abonniert werden)') }}</label>
    </div>
</div>

{{-- Standard Input Styling hinzufügen (für alle Inputs/Textareas/Selects im Formular) --}}
@push('styles')
    {{-- Oder direkt globale Styles --}}
    <style>
        input[type='text'],
        input[type='number'],
        input[type='email'],
        input[type='password'],
        select,
        textarea {
            @apply border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm;
        }
    </style>
@endpush
