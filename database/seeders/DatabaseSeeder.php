<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Administrateur ──
        User::create([
            'name'      => 'Admin',
            'firstname' => 'Super',
            'email'     => 'admin@businessdesk.ma',
            'password'  => Hash::make('Mouad@#$1991'),
            'role'      => 'admin',
        ]);

        // ── Gérant de démonstration ──
        $manager = User::create([
            'name'      => 'Rachdaoui',
            'firstname' => 'M',
            'email'     => 'm.rachdaoui@businessdesk.ma',
            'password'  => Hash::make('m.rachdaoui@#2026'),
            'role'      => 'manager',
            'mobile'    => '+212 661 00 00 01',
        ]);

        // ── Entreprise de démonstration ──
        $company = Company::create([
            'user_id'          => $manager->id,
            'name'             => 'Atlasora Services',
            'address'          => '2 Rue Essanaoubar, Étg 4 Apt 12, Casablanca',
            'phone'            => '+212 661 39 46 32',
            'email'            => 'contact@atlasora.ma',
            'rc'               => '713323',
            'ice'              => '003867696000080',
            'if_number'        => '71776708',
            'tp'               => '34216655',
            'capital'          => 100000.00,
            'doc_prefix'       => 'AS',
            'bank_account_name'=> 'ATLASORA SERVICES SARL AU',
            'bank'             => 'ATTIJARIWAFA BANK',
            'rib'              => '007 624 0007295000000568 18',
            'conditions_bc'    => implode("\n", [
                'La commande est ferme et définitive à réception du présent bon de commande signé et cacheté.',
                'Les délais d\'exécution ou de livraison seront convenus d\'un commun accord entre les deux parties.',
                'Tout litige sera réglé à l\'amiable ou devant les juridictions compétentes de Casablanca.',
                'Les prix sont fermes et non révisables sauf accord écrit préalable d\'Atlasora Services.',
                'Atlasora Services se réserve le droit de suspendre toute prestation en cas de non-paiement.',
            ]),
        ]);

        // ── Clients de démonstration ──
        Client::create([
            'company_id'   => $company->id,
            'company_name' => 'ESAV Marrakech',
            'address'      => 'Marrakech',
            'phone'        => '05 24 29 90 00',
            'ice'          => null,
        ]);

        Client::create([
            'company_id'   => $company->id,
            'company_name' => 'Groupe Logistique Fès',
            'address'      => 'Zone industrielle, Fès',
            'phone'        => '05 35 00 00 00',
            'ice'          => '004512300000001',
        ]);
    }
}
