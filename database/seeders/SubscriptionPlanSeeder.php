<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        SubscriptionPlan::create([
            'name' => 'Free',
            'price' => 0.00,
            'billing_interval' => 'month',
            'description' => 'Grundlegender Zugang zur Plattform.',
            'features' => ['Events durchsuchen', 'Clubs ansehen'],
            'is_active' => true,
            'sort_order' => 0,
        ]);

        SubscriptionPlan::create([
            'name' => 'Clubber',
            'price' => 9.99,
            'billing_interval' => 'month',
            'description' => 'Mehr Features für regelmäßige Besucher.',
            'features' => ['Alle Free Features', 'Events speichern', 'Bewertungen schreiben', 'Gamification teilnehmen'],
            'stripe_plan_id' => 'price_clubber_monthly', // Beispielhafte ID
            'is_active' => true,
            'sort_order' => 10,
        ]);

         SubscriptionPlan::create([
            'name' => 'VIP Access',
            'price' => 19.99,
            'billing_interval' => 'month',
            'description' => 'Exklusive Vorteile und Rabatte.',
            'features' => ['Alle Clubber Features', 'Rabatte auf Vorbestellungen', 'VIP-Badges', 'Früherer Zugang zu Tickets (optional)'],
             'stripe_plan_id' => 'price_vip_monthly', // Beispielhafte ID
            'is_active' => true,
            'sort_order' => 20,
        ]);

        // Ggf. Jahrespläne hinzufügen
         SubscriptionPlan::create([
            'name' => 'Clubber (Jährlich)',
            'price' => 99.99, // Beispiel-Rabatt
            'billing_interval' => 'year',
            'description' => 'Ein Jahr lang Clubber-Vorteile zum Sparpreis.',
            'features' => ['Alle Free Features', 'Events speichern', 'Bewertungen schreiben', 'Gamification teilnehmen'],
            'stripe_plan_id' => 'price_clubber_yearly', // Beispielhafte ID
            'is_active' => true,
            'sort_order' => 15, // Zwischen Monats- und VIP-Plan
        ]);
    }
}